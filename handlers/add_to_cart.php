<?php
session_start();

// Получаем данные о товаре из POST-запроса
$product_id = isset($_POST['id']) ? $_POST['id'] : null;
$product_name = isset($_POST['name']) ? $_POST['name'] : null;
$product_price = isset($_POST['price']) ? $_POST['price'] : null;
$product_quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

// Проверяем, что все необходимые данные получены
if ($product_id && $product_name && $product_price) {
    // Формируем информацию о товаре
    $product_info = array(
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => $product_quantity
    );

    // Добавляем товар в корзину (используем сессии для примера)
    $_SESSION['cart'][] = $product_info;

    // Возвращаем успешный статус
    http_response_code(200);
    echo "Товар успешно добавлен в корзину!";
} else {
    // Возвращаем статус ошибки
    http_response_code(400);
    echo "Ошибка: Недостаточно данных для добавления товара в корзину.";
}
?>
