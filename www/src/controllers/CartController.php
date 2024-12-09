<?php

class CartController {
    public function addToCart($userId) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['product_id']) || empty($data['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Produit et quantité requis']);
            return;
        }

        try {
            $db = Database::connect();
            CartModel::addToCart($db, $userId, $data['product_id'], $data['quantity']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getCart($userId) {
        $db = Database::connect();
        
        // Vérification si l'utilisateur est authentifié ou invité (sans ID)
        if ($userId) {
            $cartItems = CartModel::getCart($db, $userId);
        } else {
            // Gérer le cas des utilisateurs invités (sans ID)
            $cartItems = CartModel::getGuestCart($db);  // Méthode à définir pour les invités
        }
    
        echo json_encode(['cart' => $cartItems]);
    }
    
    public function updateCart($userId) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['product_id']) || empty($data['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Produit et quantité requis']);
            return;
        }

        try {
            $db = Database::connect();
            CartModel::addToCart($db, $userId, $data['product_id'], $data['quantity']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function removeFromCart($userId, $productId) {
        $db = Database::connect();
        $deleted = CartModel::removeItemFromCart($db, $userId, $productId);
    
        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Produit non trouvé dans le panier.']);
        }
    }
    
}
