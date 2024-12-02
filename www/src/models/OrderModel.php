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

  public static function calculateTotal($items) {
      $total = 0;
      foreach ($items as $item) {
          $total += $item['price'] * $item['quantity']; // Ajoutez une validation ici si nécessaire.
      }
      return $total;
  }
}
