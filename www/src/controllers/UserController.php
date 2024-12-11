<?php

/* namespace App\Controllers;

use App\Models\UserModel;
use App\Config\Database; */
//require_once '../config/Database.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/UserModel.php';
class UserController {
    public function getUsers() {
        $users = UserModel::getAllUsers();
        foreach ($users as &$user) {
            $user['firstname'] = html_entity_decode($user['firstname'], ENT_QUOTES, 'UTF-8');
            $user['lastname'] = html_entity_decode($user['lastname'], ENT_QUOTES, 'UTF-8');
        }
        echo json_encode(['users' => $users], JSON_UNESCAPED_UNICODE);
    }

    public function createUser() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email et mot de passe sont requis.']);
            return;
        }
        $db = Database::connect();
        $userId = UserModel::createUser($db, $data);
        echo json_encode(['success' => true, 'user_id' => $userId]);
    }

    public function getUser($id) {
        $db = Database::connect();
/*         echo "<pre>**********";
        var_dump($id);
        echo "</pre>***********"; */
        $user = UserModel::getUserById($id);
        
        if ($user) {
            $user['firstname'] = html_entity_decode($user['firstname'], ENT_QUOTES, 'UTF-8');
            $user['lastname'] = html_entity_decode($user['lastname'], ENT_QUOTES, 'UTF-8');
            echo json_encode($user, JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Utilisateur non trouvé.']);
        }
    }

    

    public function updateUser($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $db = Database::connect();
        $updated = UserModel::updateUser($db, $id, $data);
        if ($updated) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Échec de la mise à jour.']);
        }
    }

    public function deleteUser($id) {
        $db = Database::connect();
        $deleted = UserModel::deleteUser($db, $id);
        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Utilisateur non trouvé.']);
        }
    }
}
