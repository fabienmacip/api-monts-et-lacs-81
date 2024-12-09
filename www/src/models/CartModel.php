
<?php
class CartModel {
    public static function addToCart($db, $userId, $productId, $quantity) {
        $stmt = $db->prepare("SELECT id, stock FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['stock'] >= $quantity) {
            $stmt = $db->prepare("SELECT id, total FROM orders WHERE user_id = ? AND status = 'pending'");
            $stmt->execute([$userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                $stmt = $db->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, 0, 'pending')");
                $stmt->execute([$userId]);
                $orderId = $db->lastInsertId();
            } else {
                $orderId = $order['id'];
            }

            $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ? AND product_id = ?");
            $stmt->execute([$orderId, $productId]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingItem) {
                $stmt = $db->prepare("UPDATE order_items SET quantity = quantity + ? WHERE order_id = ? AND product_id = ?");
                $stmt->execute([$quantity, $orderId, $productId]);
            } else {
                $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $productId, $quantity, $product['price']]);
            }

            self::updateOrderTotal($db, $orderId);
        } else {
            throw new Exception('Produit en rupture de stock ou quantité invalide.');
        }
    }

    public static function updateOrderTotal($db, $orderId) {
        $stmt = $db->prepare("SELECT SUM(price * quantity) AS total FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $stmt = $db->prepare("UPDATE orders SET total = ? WHERE id = ?");
        $stmt->execute([$total, $orderId]);
    }

    public static function getCart($db, $userId) {
        $stmt = $db->prepare("SELECT oi.product_id, p.name, p.price, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id IN (SELECT id FROM orders WHERE user_id = ? AND status = 'pending')");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function removeItemFromCart($db, $userId, $productId) {
        $stmt = $db->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?) AND product_id = ?");
        $stmt->execute([$userId, $productId]);
    
        return $stmt->rowCount() > 0;
    }
    
    public static function calculateTotal($db, $cartId) {
        $stmt = $db->prepare("SELECT price, quantity FROM order_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    public static function getGuestCart($db) {
        // On récupère le panier d'un invité, supposons qu'on utilise une session ou un identifiant unique pour chaque invité
        $sessionId = session_id();  // ou tout autre identifiant unique de l'invité
        
        $stmt = $db->prepare("
            SELECT oi.product_id, oi.quantity, p.name, p.price 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.guest_id = :guest_id AND o.status = 'pending'
        ");
        $stmt->execute(['guest_id' => $sessionId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createGuestCart($db, $sessionId, $total) {
        $stmt = $db->prepare("INSERT INTO orders (guest_id, total, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$sessionId, $total]);
        return $db->lastInsertId();  // Retourne l'ID de la nouvelle commande
    }
    
    
}
