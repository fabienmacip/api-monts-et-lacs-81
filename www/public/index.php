<?php
session_start();

require_once '../../vendor/autoload.php';
/* use App\Router;
use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;
use App\Middlewares\RoleMiddleware;
use App\Config\Database; */
require_once __DIR__ . '/../src/config/Database.php';
require __DIR__ . '/../src/controllers/UserController.php';
require __DIR__ . '/../src/controllers/OrderController.php';
require __DIR__ . '/../src/controllers/ProductController.php';
require __DIR__ . '/../src/controllers/CartController.php';
require __DIR__ . '/../src/controllers/AuthController.php';
require __DIR__ . '/../src/middlewares/RoleMiddleware.php'; 
require __DIR__ . '/../src/Router.php';

header('Content-Type: application/json');

// Créer une instance du routeur
$router = new Router();

// Ajouter les routes

$router->addRoute('POST', 'auth/login', [new AuthController(), 'login']);
$router->addRoute('POST', 'auth/register', [new AuthController(), 'register']);
$router->addRoute('DELETE', 'auth/delete/{id}', [new UserController(), 'deleteUser'])
       ->addMiddleware(new RoleMiddleware(['user','admin', 'superadmin']));


// TEST TEST TEST Routes protégées par RoleMiddleware
/* $router->addRoute('GET', 'admin/dashboard', [new AdminController(), 'dashboard'])
       ->addMiddleware(new RoleMiddleware(['admin', 'superadmin']));
 */
$router->addRoute('POST', 'orders/guest', [new OrderController(), 'createGuestOrder']);
$router->addRoute('POST', 'orders/{userId}', [new OrderController(), 'createUserOrder']);
$router->addRoute('GET', 'orders/{orderId}', [new OrderController(), 'getOrder']);
$router->addRoute('GET', 'orders', [new OrderController(), 'getAllOrders']);
$router->addRoute('PUT', 'orders/{orderId}', [new OrderController(), 'updateOrder']);
$router->addRoute('DELETE', 'orders/{orderId}', [new OrderController(), 'deleteOrder']);

$router->addRoute('GET', 'users', [new UserController(), 'getUsers']);
       //->addMiddleware(new RoleMiddleware(['admin', 'superadmin']));
$router->addRoute('POST', 'users', [new UserController(), 'createUser']);
$router->addRoute('GET', 'users/{id}', [new UserController(), 'getUser']);
$router->addRoute('PUT', 'users/{id}', [new UserController(), 'updateUser']);
$router->addRoute('DELETE', 'users/{id}', [new UserController(), 'deleteUser']);

$router->addRoute('GET', 'products', [new ProductController(), 'getProducts'])
       ->addMiddleware(new RoleMiddleware(['user', 'admin', 'superadmin']));
$router->addRoute('GET', 'products/{id}', [new ProductController(), 'getProduct']);
$router->addRoute('POST', 'products', [new ProductController(), 'createProduct']);
$router->addRoute('PUT', 'products/{id}', [new ProductController(), 'updateProduct']);
$router->addRoute('DELETE', 'products/{id}', [new ProductController(), 'deleteProduct']);

$router->addRoute('POST', 'cart/{userId}/add', [new CartController(), 'addToCart']);
$router->addRoute('GET', 'cart/{userId}', [new CartController(), 'getCart']);
$router->addRoute('PUT', 'cart/{userId}/update', [new CartController(), 'updateCart']);
$router->addRoute('DELETE', 'cart/{userId}/remove/{productId}', [new CartController(), 'removeFromCart']);



// Vérifier si la route existe
$controllerAction = $router->match($_SERVER['REQUEST_METHOD'], $_GET['route']);

if ($controllerAction) {
    call_user_func_array($controllerAction['controllerAction'], array_values($controllerAction['params']));
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route non trouvée']);
}

