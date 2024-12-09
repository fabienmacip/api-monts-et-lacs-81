<?php
require_once '../src/config/Database.php';
require '../src/models/CartModel.php';

/**
 * @OA\Info(title="API Monts et Lacs", version="1.0")
 */

/**
 * @OA\Get(
 *     path="/cart/{userId}",
 *     summary="Get the cart for a user",
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart retrieved successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Cart")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
class CartController {
    public function getCart($userId) {
        $db = Database::connect();
        $cart = CartModel::getCartByUserId($db, $userId);
        echo json_encode(['cart' => $cart]);
    }

    /**
     * @OA\Post(
     *     path="/cart/{userId}/add",
     *     summary="Add a product to the user's cart",
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
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product added to cart"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data"
     *     )
     * )
     */
    public function addToCart($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['product_id']) || empty($data['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Produit et quantité sont requis.']);
            return;
        }

        $db = Database::connect();
        $added = CartModel::addToCart($db, $userId, $data['product_id'], $data['quantity']);

        if ($added) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Échec de l\'ajout au panier.']);
        }
    }

    /**
     * @OA\Post(
     *     path="/cart/{userId}/remove",
     *     summary="Remove a product from the user's cart",
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
     *             @OA\Property(property="product_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product removed from cart"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid product"
     *     )
     * )
     */
    public function removeFromCart($userId) {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['product_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Produit requis.']);
            return;
        }

        $db = Database::connect();
        $removed = CartModel::removeFromCart($db, $userId, $data['product_id']);

        if ($removed) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Échec de la suppression du produit.']);
        }
    }

    /**
     * @OA\Post(
     *     path="/cart/{userId}/clear",
     *     summary="Clear the user's cart",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Failed to clear cart"
     *     )
     * )
     */
    public function clearCart($userId) {
        $db = Database::connect();
        $cleared = CartModel::clearCart($db, $userId);

        if ($cleared) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Échec de la suppression du panier.']);
        }
    }
}
