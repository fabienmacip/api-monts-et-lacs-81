<?php
require_once __DIR__ . '/../../vendor/autoload.php'; 

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$key = "votre_clé_secrète";
$payload = array(
    "iat" => time(),
    "exp" => time() + 3600,  // Le token expirera dans 1 heure
    "userId" => 1  // Identifiant de l'utilisateur
);

// Générer le token JWT
$jwt = JWT::encode($payload, $key, 'HS256');  // Le 3ème argument spécifie l'algorithme
echo "Token généré : " . $jwt . "\n";

// Décodage du token
try {
    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    echo "Token décodé : \n";
    print_r($decoded);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
