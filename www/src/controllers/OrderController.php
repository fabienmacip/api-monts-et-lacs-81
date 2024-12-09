<?php
require_once '../src/config/Database.php';
require '../src/models/OrderModel.php';
require '../src/utils/Validator.php';

/**
 * @OA\Post(
 *     path="/orders/guest",
 *     summary="Create a guest order",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="guest", type="object"),
 *             @OA\Property(property="items", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order successfully created",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="order_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid order data"
 *     )
 * )
 */
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

    /**
     * @OA\Post(
     *     path="/orders/user/{userId}",
     *     summary="Create a user order",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="order_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid order data"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/orders/{orderId}",
     *     summary="Get an order by ID",
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order found",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Get all orders",
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Order")
     *         )
     *     )
     * )
     */
    public function getAllOrders() {
        $db = Database::connect();
        $orders = OrderModel::getAllOrders($db);

        echo json_encode(['orders' => $orders]);
    }

    /**
     * @OA\Put(
     *     path="/orders/{orderId}",
     *     summary="Update an order",
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="items", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid order data"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/orders/{orderId}",
     *     summary="Delete an order by ID",
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
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
