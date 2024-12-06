<?php

require '../src/controllers/OrderController.php';
require '../src/controllers/UserController.php';

header('Content-Type: application/json');

echo "coucou";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['route'] === 'orders/guest') {
    (new OrderController())->createGuestOrder();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['route'] === 'users') {
    (new UserController())->getUsers();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route non trouvée']);
}
