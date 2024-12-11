<?php
/* namespace App\Models;

use App\Config\Database; */
require_once '../config/Database.php';
class ProductModel {
    public static function getAllProducts($db) {
        $stmt = $db->query("SELECT id, name, description, price, stock FROM products");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getProductById($db, $id) {
        $stmt = $db->prepare("SELECT id, name, description, price, stock FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function createProduct($db, $data) {
        $stmt = $db->prepare("INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['description'], $data['price'], $data['stock']]);
        return $db->lastInsertId();
    }

    public static function updateProduct($db, $id, $data) {
        $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['description'], $data['price'], $data['stock'], $id]);
        return $stmt->rowCount() > 0;
    }

    public static function deleteProduct($db, $id) {
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
