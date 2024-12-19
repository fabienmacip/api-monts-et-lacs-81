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
        // Vérification et récupération du token
        $authController = new AuthController();
        $decoded = $authController->verifyToken();

        if ($decoded === null) {
            // Le token est invalide ou manquant
            return false;
        }

        // Vérification du rôle de l'utilisateur
        if (!property_exists($decoded, 'role') || !in_array($decoded->role, $this->roles, true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès interdit : rôle insuffisant']);
            return false; // Middleware échoue
        }

        return true; // Middleware réussi
    }
}

