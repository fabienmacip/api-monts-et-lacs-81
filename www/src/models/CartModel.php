<?php

class CartModel {
    public static function getCartByUserId($db, $userId) {
        $stmt = $db->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addToCart($db, $userId, $productId, $quantity) {
        $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $productId, $quantity]);
    }

    public static function updateCartItem($db, $cartId, $quantity) {
        $stmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$quantity, $cartId]);
    }

    public static function removeFromCart($db, $cartId) {
        $stmt = $db->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cartId]);
    }

    public static function calculateCartTotal($db, $userId) {
        $stmt = $db->prepare("
            SELECT SUM(p.price * c.quantity) AS total
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
