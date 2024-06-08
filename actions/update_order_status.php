<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['id_order'];
    $status = $_POST['status'];

    // Обновление статуса заказа
    $updateQuery = "UPDATE orders SET status = ? WHERE id_order = ?";
    $stmt = $pdo->prepare($updateQuery);
    
    try {
        $stmt->execute([$status, $orderId]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
}
?>
