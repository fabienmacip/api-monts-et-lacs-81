<?php
/* namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\CartModel;
use App\Config\Database;
use App\Utils\Validator; */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../utils/Validator.php';

class OrderController {
    public function createGuestOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (empty($data['guest']['name']) || empty($data['guest']['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Nom et email sont requis pour une commande d\'invité.']);
            return;
        }
    
        // Assurez-vous que le panier existe (par exemple, récupérez les produits dans le panier)
        $db = Database::connect();
        $total = CartModel::calculateTotal($db, $data['guest']['cart_id']); // Calcul du total du panier
        $orderId = OrderModel::createGuestOrder($db, $data['guest'], $data['guest']['cart_id'], $total);
    
        echo json_encode(['success' => true, 'order_id' => $orderId]);
    }
    

 
    public function createUserOrder($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $validation = Validator::validateUserOrder($data);

        if (!$validation['valid']) {
            http_response_code(400);
            echo json_encode(['error' => $validation['message']]);
            return;
        }

        $db = Database::connect();
        $orderId = OrderModel::createUserOrder($db, $userId, $data['items']);

        echo json_encode(['success' => true, 'order_id' => $orderId]);
    }

    public function getOrder($orderId) {
        $db = Database::connect();
        $order = OrderModel::getOrderById($db, $orderId);

        if ($order) {
            echo json_encode($order);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Commande non trouvée.']);
        }
    }

    public function getAllOrders() {
        $db = Database::connect();
        $orders = OrderModel::getAllOrders($db);

        echo json_encode(['orders' => $orders]);
    }

    public function updateOrder($orderId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $db = Database::connect();
        $updated = OrderModel::updateOrder($db, $orderId, $data);

        if ($updated) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Échec de la mise à jour de la commande.']);
        }
    }

    public function deleteOrder($orderId) {
        $db = Database::connect();
        $deleted = OrderModel::deleteOrder($db, $orderId);

        if ($deleted) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Commande non trouvée.']);
        }
    }
}
