<?php
require_once '../src/config/Database.php';
require '../src/models/ProductModel.php';

/**
 * @OA\Get(
 *     path="/products",
 *     summary="Get all products",
 *     @OA\Response(
 *         response=200,
 *         description="List of products",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     )
 * )
 */
class ProductController {
    public function getProducts() {
        $db = Database::connect();
        $products = ProductModel::getAllProducts($db);
        echo json_encode(['products' => $products]);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product found",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number", format="float")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="product_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update product details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", nullable=true),
     *             @OA\Property(property="price", type="number", format="float", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
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
