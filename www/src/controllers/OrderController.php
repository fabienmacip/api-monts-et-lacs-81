<?php
require_once '../src/config/Database.php';
require '../src/models/OrderModel.php';
require '../src/utils/Validator.php';

class OrderController {
    public function createGuestOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        $validation = Validator::validateGuestOrder($data);

        if (!$validation['valid']) {
            http_response_code(400);
            echo json_encode(['error' => $validation['message']]);
            return;
        }

        $db = Database::connect();
        $orderId = OrderModel::createGuestOrder($db, $data['guest'], $data['items']);

        echo json_encode(['success' => true, 'order_id' => $orderId]);
    }
}
