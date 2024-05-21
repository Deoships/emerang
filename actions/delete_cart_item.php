<?php
// Подключаемся к базе данных
include '../config/db.php';

// Проверяем, был ли отправлен идентификатор товара для удаления
if (isset($_POST['id_cart_item'])) {
    // Получаем идентификатор товара из запроса
    $cartItemId = $_POST['id_cart_item'];

    // Подготавливаем SQL-запрос для удаления товара из корзины
    $deleteCartItemQuery = "DELETE FROM cart_item WHERE id_cart_item = ?";
    $stmt = $pdo->prepare($deleteCartItemQuery);

    // Выполняем запрос с учетом параметров
    $stmt->execute([$cartItemId]);

    // Возвращаем успешный статус
    http_response_code(200);
} else {
    // Если идентификатор товара не был отправлен, возвращаем ошибку
    http_response_code(400);
    echo "Ошибка: Не удалось найти идентификатор товара для удаления";
}
?>
