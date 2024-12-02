<?php

require '../src/controllers/OrderController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['route'] === 'orders/guest') {
    (new OrderController())->createGuestOrder();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route non trouvée']);
}
