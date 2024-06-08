<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['id_order'];

    // Обновление статуса заказа на "одобрено"
    $updateQuery = "UPDATE orders SET status = 'одобрено' WHERE id_order = ?";
    $stmt = $pdo->prepare($updateQuery);
    
    try {
        $stmt->execute([$orderId]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
