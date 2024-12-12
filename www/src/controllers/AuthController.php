<?php
//namespace App\Controllers;

//require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;  
//use App\Models\UserModel;
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    public function formatMsg($msg) {
        return html_entity_decode($msg, ENT_QUOTES, 'UTF-8');
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400); 
            echo json_encode(['error' => 'Email et mot de passe sont requis']);
        }
    
        $user = UserModel::getUserIncludingPasswordByEmail($data['email']);
        
        if ($user && password_verify($data['password'], $user['password'])) {
            $jwt = $this->generateJwt($user['id'], $user['role']);
            http_response_code(200);
            echo json_encode(['token' => $jwt]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Identifiants incorrects']);
        }
    }

    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $existingUser = UserModel::getUserEmailOnlyByEmail($data['email']);
        if ($existingUser) {
            http_response_code(400);
            echo json_encode(['error' => 'Cet email est déjà utilisé']);
        }
    
        $userId = UserModel::createUser($data);
    
        if ($userId) {
            http_response_code(201);
            echo json_encode(['message' => 'Utilisateur créé', 'userId' => $userId]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la création de l\'utilisateur']);
        }
    }
    
    private function generateJwt($userId, $role) {
        //$key = new Key("votre_clé_secrète", 'HS256'); // Utilisation de la classe Key
        $key = "votre_clé_secrète";
        $payload = [
            'iat' => time(), // Temps de création du token
            'exp' => time() + 3600, // Expiration dans 1 heure
            'userId' => $userId,
            'role' => $role
        ];
        return JWT::encode($payload, $key, 'HS256');  // Passer l'objet Key au lieu de la clé en string
    }

    public function verifyToken() {

        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token non fourni."]);
            return null;
        }
    
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    
        try {
            $decoded = JWT::decode($token, new Key("votre_clé_secrète", 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["message" => "Token invalide : " . $e->getMessage()]);
            return null;
        }
    }
    

    public function deleteUser($args) {
        $data = json_decode(file_get_contents('php://input'), true);
        $userIdToDelete = $args['id']; 
    
        $user = UserModel::getUserById($userIdToDelete);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Utilisateur non trouvé']);
        }
    
        // Vérifier les autorisations (admin ou si c'est l'utilisateur lui-même)
        $decoded = $this->verifyToken();  // Méthode qui décode le token JWT
        if ($decoded->userId !== $userIdToDelete && 
            !in_array($decoded->role, ['admin', 'superadmin'])) 
            {
                http_response_code(403);
                echo json_encode(['error' => 'Accès interdit']);
            }
    
        // Supprimer l'utilisateur
        UserModel::deleteUser($userIdToDelete);
    
        http_response_code(200);
        echo json_encode(['message' => 'Utilisateur supprimé']);
    }
    
}


/* class AuthController {

    private $secretKey = "votre_clé_secrète";

    // Vérifie si le token JWT est valide
    public function verifyToken() {
        // Récupérer le token depuis les en-têtes
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["message" => "Token non fourni."]);
            return;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            // Décoder le token
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded; // Retourne le payload décodé
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["message" => "Token invalide : " . $e->getMessage()]);
            return;
        }
    }

    public function checkUserRole($userId, $roleRequired) {
      // Vérifier le rôle de l'utilisateur dans la base de données
      $user = UserModel::getUserById($userId);
      if ($user && $user['role'] === $roleRequired) {
          return true;
      }
      return false;
    }
    // Générer un token JWT lors de la connexion
    public function generateToken($userId) {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // Le token expire après 1 heure
            'userId' => $userId
        ];

        $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
        return $jwt;
    }
} */
