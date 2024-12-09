<?php

class UserModel {
    public static function getAllUsers($db) {
        $stmt = $db->query("SELECT id, email, name, phone, created_at FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserById($db, $id) {
        $stmt = $db->prepare("SELECT id, email, name, phone, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createUser($db, $data) {
        $stmt = $db->prepare("INSERT INTO users (email, name, phone, password) VALUES (:email, :name, :phone, :password)");
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->execute();
        return $db->lastInsertId();
    }

    public static function updateUser($db, $id, $data) {
        $stmt = $db->prepare("UPDATE users SET email = :email, name = :name, phone = :phone WHERE id = :id");
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function deleteUser($db, $id) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
