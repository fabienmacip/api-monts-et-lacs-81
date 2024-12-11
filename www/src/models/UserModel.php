<?php
/* namespace App\Models;

use App\Config\Database; */
require_once '../config/Database.php';


require_once '../src/config/Database.php';
class UserModel {
    public static function getAllUsers() {
        $db = Database::connect();
        $stmt = $db->query("SELECT id, email, civility, firstname, lastname, phone, role, is_verified, last_login, created_at FROM users");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getUserById($id) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, email, civility, firstname, lastname, phone, role, is_verified, last_login, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function getUserByEmail($email) {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, email, civility, firstname, lastname, phone, role, is_verified, last_login, created_at FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public static function createUser($db, $data) {
        $email = $data['email'];
        $civility = $data['civility'];
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $phone = $data['phone'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'guest'; // Default to 'guest'
        $is_verified = false; // New users start as unverified

        $stmt = $db->prepare("INSERT INTO users (email, civility, firstname, lastname, phone, password, role, is_verified) VALUES (:email, :civility, :firstname, :lastname, :phone, :password, :role, :is_verified)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':civility', $civility);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':is_verified', $is_verified);
        try {
            if ($stmt->execute()) {
                $query = $db->prepare("SELECT id FROM users WHERE email = :email ORDER BY created_at DESC LIMIT 1");
                $query->bindParam(':email', $data['email']);
                $query->execute();
                $result = $query->fetch(\PDO::FETCH_ASSOC);
                return $result['id'];
            } else {
                throw new \Exception("L'insertion dans la table `users` a échoué.");
            }
        } catch (\Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }

    public static function updateUser($db, $id, $data) {
        $stmt = $db->prepare("UPDATE users SET email = :email, civility = :civility, firstname = :firstname, lastname = :lastname, phone = :phone, role = :role, is_verified = :is_verified WHERE id = :id");
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':civility', $data['civility']);
        $stmt->bindParam(':firstname', $data['firstname']);
        $stmt->bindParam(':lastname', $data['lastname']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':is_verified', $data['is_verified']);
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function deleteUser($db, $id) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        return $stmt->execute();
    }
}


/* class UserModel {
    public static function getAllUsers($db) {
        $stmt = $db->query("SELECT id, email, civility, firstname, lastname, phone, created_at FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserById($db, $id) {
        $stmt = $db->prepare("SELECT id, email, civility, firstname, lastname, phone, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createUser($db, $data) {
        
        $email = $data['email'];
        $civility = $data['civility'];
        $firstname = $data['firstname'];
        $lastname = $data['lastname'];
        $phone = $data['phone'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("INSERT INTO users (email, civility, firstname, lastname, phone, password) VALUES (:email, :civility, :firstname, :lastname, :phone, :password)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':civility', $civility);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);
        try {
            if ($stmt->execute()) {
                $query = $db->prepare("SELECT id FROM users WHERE email = :email ORDER BY created_at DESC LIMIT 1");
                $query->bindParam(':email', $data['email']);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                return $result['id'];
            } else {
                throw new Exception("L'insertion dans la table `users` a échoué.");
            }
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
            return null;
        }
    }

    public static function updateUser($db, $id, $data) {
        $stmt = $db->prepare("UPDATE users SET email = :email, civility = :civility, firstname = :firstname, lastname = :lastname, phone = :phone WHERE id = :id");
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':civility', $data['civility']);
        $stmt->bindParam(':firstname', $data['firstname']);
        $stmt->bindParam(':lastname', $data['lastname']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public static function deleteUser($db, $id) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        return $stmt->execute();
    }
} */
