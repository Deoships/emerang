<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключение к базе данных
    include '../config/db.php';

    // Получение данных из POST-запроса
    $cartItemId = $_POST["id_cart_item"]; // Исправление здесь
    $quantity = $_POST["quantity"];

    // Обновление количества товара в базе данных
    $stmt = $pdo->prepare("UPDATE cart_item SET quantity = ? WHERE id_cart_item = ?");
    $stmt->execute([$quantity, $cartItemId]); // И здесь

    // Возвращаем успешный статус HTTP
    http_response_code(200);
} else {
    // Возвращаем ошибку, если запрос не был отправлен методом POST
    http_response_code(405);
}
?>