<?php

class UserModel {
    public static function getAllUsers($db) {
        $stmt = $db->query("SELECT id, email, name, phone, created_at FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
