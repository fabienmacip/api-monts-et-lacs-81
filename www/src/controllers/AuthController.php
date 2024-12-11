<?php
//namespace App\Controllers;

//require_once '../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;  
//use App\Models\UserModel;
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    public function login($request, $response) {
        $data = $request->getParsedBody();

        // Vérification des identifiants utilisateur
        $user = UserModel::getUserByEmail($data['email']);
        
        if ($user && password_verify($data['password'], $user['password'])) {
            // Générer le token JWT
            $jwt = $this->generateJwt($user['id'], $user['role']);
            return $response->withJson(['token' => $jwt]);
        } else {
            return $response->withJson(['error' => 'Identifiants incorrects'], 401);
        }
    }

    private function generateJwt($userId, $role) {
        $key = new Key("votre_clé_secrète", 'HS256'); // Utilisation de la classe Key
        $payload = [
            'iat' => time(), // Temps de création du token
            'exp' => time() + 3600, // Expiration dans 1 heure
            'userId' => $userId,
            'role' => $role
        ];
        return JWT::encode($payload, $key, 'HS256');  // Passer l'objet Key au lieu de la clé en string
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
