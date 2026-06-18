<?php
// api/checkout.php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

// Turn off raw error output to protect our JSON structure
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Check if the user is authenticated (FR-02 Safeguard)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication required. Please log in before checking out.']);
    exit;
}

// Read raw incoming JSON string payload from our JavaScript frontend
$inputData = json_decode(file_get_contents('php://input'), true);
$cartItems = $inputData['cart'] ?? [];

if (empty($cartItems)) {
    echo json_encode(['status' => 'error', 'message' => 'Your shopping cart is completely empty.']);
    exit;
}

$userId = $_SESSION['user_id'];
$totalAmount = 0;

try {
    // Start a secure database transaction. If ANY single item fails, 
    // the whole order is canceled so data never becomes corrupt.
    $pdo->beginTransaction();

    // Phase A: Calculate true totals directly from database records (prevents tampering)
    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("SELECT price, stock_quantity FROM products WHERE id = ?");
        $stmt->execute([$item['id']]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Product ID " . $item['id'] . " does not exist in our catalog registry.");
        }

        // Check if there is enough physical inventory left to fulfill the order
        if ($product['stock_quantity'] < $item['quantity']) {
            throw new Exception("Insufficient stock for item. Only " . $product['stock_quantity'] . " remaining.");
        }

        $totalAmount += $product['price'] * $item['quantity'];
    }

    // Phase B: Create the primary Order Record
    $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Processing')");
    $orderStmt->execute([$userId, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // Phase C: Write individual records to order_items and deplete inventory
    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$item['id']]);
        $priceAtPurchase = $stmt->fetchColumn();

        // Write to order items layout linkage
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $priceAtPurchase]);

        // Deplete stock values cleanly (FR-12 / FR-14 compatibility tracking)
        $updateStockStmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $updateStockStmt->execute([$item['quantity'], $item['id']]);
    }

    // Commit all operations to the database safely
    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Order processed successfully! Your tracking ID is #' . $orderId]);

} catch (Exception $e) {
    // If anything fails, roll back everything like nothing happened
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Checkout failed: ' . $e->getMessage()]);
}
exit;
?>