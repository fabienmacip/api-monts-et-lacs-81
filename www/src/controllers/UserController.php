<?php
require_once '../src/config/Database.php';
require '../src/models/UserModel.php';

/**
 * @OA\Get(
 *     path="/users",
 *     summary="Get all users",
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/User")
 *         )
 *     )
 * )
 */
class UserController {
    public function getUsers() {
        $db = Database::connect();
        $users = UserModel::getAllUsers($db);
        echo json_encode(['users' => $users]);
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getUser($id) {
        $db = Database::connect();
        $user = UserModel::getUserById($db, $id);
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Utilisateur non trouvé.']);
        }
    }

    
    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update user details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", nullable=true),
     *             @OA\Property(property="password", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
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
