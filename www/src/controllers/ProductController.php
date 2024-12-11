<?php
/* namespace App\Controllers;

use App\Models\ProductModel;
use App\Config\Database; */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ProductController {
    public function getProducts() {
        $db = Database::connect();
        $products = ProductModel::getAllProducts($db);
        echo json_encode(['products' => $products]);
    }

    public function getProduct($id) {
        $db = Database::connect();
        $product = ProductModel::getProductById($db, $id);

        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Produit non trouvé.']);
        }
    }

    public function createProduct() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || empty($data['price'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nom et prix sont requis.']);
            return;
        }
        $db = Database::connect();
        $productId = ProductModel::createProduct($db, $data);
        echo json_encode(['success' => true, 'product_id' => $productId]);
    }

    public function updateProduct($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $db = Database::connect();
        $updated = ProductModel::updateProduct($db, $id, $data);

        if ($updated) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Échec de la mise à jour du produit.']);
        }
    }

    public function deleteProduct($id) {
        $db = Database::connect();
        $deleted = ProductModel::deleteProduct($db, $id);

        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Produit non trouvé.']);
        }
    }
}
