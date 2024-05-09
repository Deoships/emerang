<?php
// Подключение к базе данных
include '../config/db.php';

session_start(); // Начинаем сессию пользователя

// Проверяем, был ли пользователь авторизован
if (isset($_SESSION['user_id'])) {
    // Здесь можно добавить код, связанный с установкой user_id в сессию
    // Например, что-то вроде:
    $user_id = $_SESSION['user_id'];
} else {
    // Если пользователь не авторизован, можно присвоить user_id значение по умолчанию
    // Например, что-то вроде:
    $user_id = 0; // Значение по умолчанию для неавторизованных пользователей
}

var_dump($_SESSION['user_id']);

// Создаем новую корзину, если у пользователя еще нет активной корзины
$createCartStmt = $pdo->prepare("
    INSERT INTO cart (id_user, created_at) 
    SELECT ?, NOW() 
    FROM dual 
    WHERE NOT EXISTS (SELECT * FROM cart WHERE id_user = ?)
    ON DUPLICATE KEY UPDATE created_at = NOW()
");
$createCartStmt->execute([$user_id, $user_id]);

// Проверяем, был ли передан идентификатор товара через POST-запрос
if (isset($_POST['product_id'])) {
    // Проверяем, установлен ли пользователь в сессии
    if (!isset($_SESSION['user_id'])) {
        // Если сессия не установлена, возвращаем ошибку
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Ошибка: Пользователь не авторизован']);
        exit; // Прерываем выполнение скрипта
    }

    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id']; // Получаем ID пользователя из сессии

    // Получаем ID корзины пользователя
    $cartIdStmt = $pdo->prepare("SELECT id_cart FROM cart WHERE id_user = ?");
    $cartIdStmt->execute([$userId]);
    $cartId = $cartIdStmt->fetchColumn();

    // Проверяем, есть ли уже такой товар в корзине
    $checkItemStmt = $pdo->prepare("SELECT * FROM cart_item WHERE id_cart = ? AND id_product = ?");
    $checkItemStmt->execute([$cartId, $productId]);
    $existingItem = $checkItemStmt->fetch();

    if ($existingItem) {
        // Если товар уже есть в корзине, обновляем количество
        $updateQuantityStmt = $pdo->prepare("UPDATE cart_item SET quantity = quantity + 1 WHERE id_cart = ? AND id_product = ?");
        if ($updateQuantityStmt->execute([$cartId, $productId])) {
            // Если запрос выполнен успешно, возвращаем успешный статус
            http_response_code(200);
            echo json_encode(['message' => 'Товар успешно добавлен в корзину']);
        } else {
            // Если возникла ошибка при выполнении запроса, возвращаем ошибку
            http_response_code(500);
            echo json_encode(['message' => 'Ошибка: не удалось добавить товар в корзину']);
        }
    } else {
        // Если товара еще нет в корзине, добавляем его
        $addItemStmt = $pdo->prepare("INSERT INTO cart_item (id_cart, id_product, quantity) VALUES (?, ?, 1)");
        if ($addItemStmt->execute([$cartId, $productId])) {
            // Если запрос выполнен успешно, возвращаем успешный статус
            http_response_code(200);
            echo json_encode(['message' => 'Товар успешно добавлен в корзину']);
        } else {
            // Если возникла ошибка при выполнении запроса, возвращаем ошибку
            http_response_code(500);
            echo json_encode(['message' => 'Ошибка: не удалось добавить товар в корзину']);
        }
    }
}
?>
