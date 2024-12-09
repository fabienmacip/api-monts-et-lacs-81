<?php

require_once '../src/config/Database.php';
require '../src/controllers/UserController.php';
require '../src/controllers/OrderController.php';
require '../src/controllers/ProductController.php';
require '../src/controllers/CartController.php';
require '../src/Router.php';

header('Content-Type: application/json');

// Créer une instance du routeur
$router = new Router();

// Ajouter les routes
$router->addRoute('POST', 'orders/guest', [new OrderController(), 'createGuestOrder']);
$router->addRoute('POST', 'orders/{userId}', [new OrderController(), 'createUserOrder']);
$router->addRoute('GET', 'orders/{orderId}', [new OrderController(), 'getOrder']);
$router->addRoute('GET', 'orders', [new OrderController(), 'getAllOrders']);
$router->addRoute('PUT', 'orders/{orderId}', [new OrderController(), 'updateOrder']);
$router->addRoute('DELETE', 'orders/{orderId}', [new OrderController(), 'deleteOrder']);

$router->addRoute('GET', 'users', [new UserController(), 'getUsers']);
$router->addRoute('POST', 'users', [new UserController(), 'createUser']);
$router->addRoute('GET', 'users/{id}', [new UserController(), 'getUser']);
$router->addRoute('PUT', 'users/{id}', [new UserController(), 'updateUser']);
$router->addRoute('DELETE', 'users/{id}', [new UserController(), 'deleteUser']);

$router->addRoute('GET', 'products', [new ProductController(), 'getProducts']);
$router->addRoute('GET', 'products/{id}', [new ProductController(), 'getProduct']);
$router->addRoute('POST', 'products', [new ProductController(), 'createProduct']);
$router->addRoute('PUT', 'products/{id}', [new ProductController(), 'updateProduct']);
$router->addRoute('DELETE', 'products/{id}', [new ProductController(), 'deleteProduct']);

$router->addRoute('GET', 'cart', [new CartController(), 'getCart']);
$router->addRoute('POST', 'cart', [new CartController(), 'addToCart']);
$router->addRoute('DELETE', 'cart/{productId}', [new CartController(), 'removeFromCart']);
$router->addRoute('PUT', 'cart/{productId}', [new CartController(), 'updateCart']);


// Vérifier si la route existe
$controllerAction = $router->match($_SERVER['REQUEST_METHOD'], $_GET['route']);

if ($controllerAction) {
    call_user_func_array($controllerAction['controllerAction'], array_values($controllerAction['params']));
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route non trouvée']);
}

