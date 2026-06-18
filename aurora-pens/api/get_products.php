<?php
header('Content-Type: application/json');
require_once '../config/db.php';

try {
    // REMOVED the "WHERE stock_quantity > 0" to see if items appear
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id
    ");
    $products = $stmt->fetchAll();
    
    echo json_encode(['status' => 'success', 'data' => $products]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>