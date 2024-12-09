<?php

class OrderModel {
    public static function createGuestOrder($db, $guestData, $items) {
        $stmt = $db->prepare(
            "INSERT INTO orders (guest_name, guest_email, guest_phone, total, status) 
            VALUES (?, ?, ?, ?, 'pending')"
        );
        $total = self::calculateTotal($items);
        $stmt->execute([
            $guestData['name'], $guestData['email'], $guestData['phone'] ?? null, $total
        ]);
        $orderId = $db->lastInsertId();

        foreach ($items as $item) {
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $item['product_id'], $item['quantity']]);
        }
        return $orderId;
    }

    public static function createUserOrder($db, $userId, $items) {
        $stmt = $db->prepare(
            "INSERT INTO orders (user_id, total, status) 
            VALUES (?, ?, 'pending')"
        );
        $total = self::calculateTotal($items);
        $stmt->execute([$userId, $total]);
        $orderId = $db->lastInsertId();

        foreach ($items as $item) {
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$orderId, $item['product_id'], $item['quantity']]);
        }
        return $orderId;
    }

    public static function getOrderById($db, $orderId) {
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllOrders($db) {
        $stmt = $db->query("SELECT * FROM orders");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateOrder($db, $orderId, $data) {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$data['status'], $orderId]);
    }

    public static function deleteOrder($db, $orderId) {
        $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$orderId]);
    }

    private static function calculateTotal($items) {
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
