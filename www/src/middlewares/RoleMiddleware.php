<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RoleMiddleware
{
    private array $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function handle()
    {
        // Récupération du token depuis les en-têtes HTTP
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Token manquant']);
            return false; // Middleware échoue
        }

        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $token = str_replace('Bearer ', '', $authorizationHeader);

        try {
            // Décoder le token JWT
            $key = new Key('votre_clé_secrète', 'HS256');
            $decoded = JWT::decode($token, $key);

            // Vérifier le rôle de l'utilisateur
            if (!property_exists($decoded, 'role') || !in_array($decoded->role, $this->roles, true)) {
                http_response_code(403);
                echo json_encode(['error' => 'Accès interdit : rôle insuffisant']);
                return false; // Middleware échoue
            }
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Token invalide', 'details' => $e->getMessage()]);
            return false; // Middleware échoue
        }

        return true; // Middleware réussi
    }
}
