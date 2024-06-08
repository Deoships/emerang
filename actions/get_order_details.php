<?php
include '../config/db.php';

if (isset($_GET['id_order'])) {
    $orderId = $_GET['id_order'];

    // SQL-запрос для получения данных о заказе
    $query = "
        SELECT o.id_order, u.first_name, u.email, u.phone, o.total_price, o.status, o.payment_method, o.created_at
        FROM orders o
        JOIN user u ON o.id_user = u.id_user
        WHERE o.id_order = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        // SQL-запрос для получения товаров в заказе
        $itemsQuery = "
            SELECT p.name, oi.quantity, oi.price
            FROM order_items oi
            JOIN product p ON oi.id_product = p.id_product
            WHERE oi.id_order = ?
        ";
        $itemsStmt = $pdo->prepare($itemsQuery);
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        $order['items'] = $items;
        echo json_encode($order);
    } else {
        echo json_encode(['error' => 'Order not found']);
    }
} else {
    echo json_encode(['error' => 'Order ID is missing']);
}
?>
