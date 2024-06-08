<?php
include '../includes/header.php';
include '../config/db.php';

// Получение идентификатора заказа из запроса
$orderId = isset($_GET['id_order']) ? intval($_GET['id_order']) : 0;

if ($orderId > 0) {
    // SQL-запрос для получения данных о заказе и пользователе
    $query = "
        SELECT o.id_order, o.total_price, o.status, o.payment_method, o.created_at,
               u.first_name, u.email, u.telephone
        FROM orders o
        JOIN user u ON o.id_user = u.id_user
        WHERE o.id_order = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // SQL-запрос для получения данных о товарах в заказе
    $queryItems = "
        SELECT p.name, p.price, oi.quantity, pi.url
        FROM order_items oi
        JOIN product p ON oi.id_product = p.id_product
        LEFT JOIN (SELECT id_product, MIN(url) as url FROM img GROUP BY id_product) pi ON p.id_product = pi.id_product
        WHERE oi.id_order = ?
    ";
    $stmtItems = $pdo->prepare($queryItems);
    $stmtItems->execute([$orderId]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
} else {
    $order = false;
    $items = [];
}

// Отладочное сообщение для проверки, получены ли данные о заказе
if (!$order) {
    error_log("Order with ID $orderId not found.");
} else {
    error_log("Order found: " . print_r($order, true));
}
?>
<section>
    <div class="wrap-order">
        <h1 class="catalog-h1">Обработка заявок</h1>
        <button onclick="goBack()" class="back-btn">Назад</button>    
    </div>
    <div class="order-details-page">
        <?php if ($order): ?>
            <div class="order-info">
                <h2 class="catalog-h2">Данные</h2>
                <div class="info-item">
                    <div class="info-label">ID</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['id_order']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Имя</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['first_name']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Почта</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['email']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Телефон</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['telephone']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Сумма</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['total_price']); ?> р.</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Статус</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['status']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Способ оплаты</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Дата создания</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['created_at']); ?></div>
                </div>
            </div>

            <div class="order-items">
                <h2>Товары</h2>
                <?php if ($items): ?>
                    <?php foreach ($items as $item): ?>
                        <?php for ($i = 0; $i < $item['quantity']; $i++): ?>
                            <div class="item">
                                <img src="<?php echo htmlspecialchars($item['url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="item-info">
                                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                </div>
                                <div class="item-info">
                                    <div class="item-price"><?php echo htmlspecialchars($item['price']); ?> р.</div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Нет товаров.</p>
                <?php endif; ?>

                <div class="buttons">
                    <?php if ($order['status'] === 'ждёт подтверждения'): ?>
                        <button class="button decline" onclick="updateOrderStatus(<?php echo $orderId; ?>, 'отклонено')">Отклонить</button>
                        <button class="button approve" onclick="updateOrderStatus(<?php echo $orderId; ?>, 'одобрено')">Одобрить</button>
                    <?php else: ?>
                        <button class="button status" disabled><?php echo htmlspecialchars(ucfirst($order['status'])); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p>Заказ не найден.</p>
        <?php endif; ?>
    </div>
</section>

<script>
function goBack() {
    window.history.back();
}

function updateOrderStatus(orderId, status) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../actions/update_order_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            alert('Статус заказа обновлен');
            window.location.reload();
        }
    };
    xhr.send("id_order=" + orderId + "&status=" + status);
}
</script>

<?php
include '../includes/footer.php';
?>
