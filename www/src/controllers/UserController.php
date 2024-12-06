<?php
require_once '../src/config/Database.php';
require '../src/models/UserModel.php';

class UserController {
    public function getUsers() {
        $db = Database::connect();
        $users = UserModel::getAllUsers($db);

        echo json_encode(['users' => $users]);
    }
}

