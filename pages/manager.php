<?php
include '../includes/header.php';
include '../config/db.php';

// SQL-запрос для получения данных о заказах и пользователях
$query = "
    SELECT o.id_order, u.first_name, u.email, o.total_price, o.status
    FROM orders o
    JOIN user u ON o.id_user = u.id_user
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
<div class="wrap">
    <h1 class="catalog-h1">Обработка заявок</h1>
    <div class="manager-panel">
        <div class="table-header">
            <div class="table-row">
                <div class="table-cell">ID</div>
                <div class="table-cell">Имя</div>
                <div class="table-cell">Почта</div>
                <div class="table-cell">Сумма</div>
                <div class="table-cell">Статус</div>
            </div>
        </div>
        <div class="table-body">
            <?php foreach ($orders as $order): ?>
                <div class="table-row" data-order-id="<?php echo htmlspecialchars($order['id_order']); ?>">
                    <div class="table-cell"><?php echo htmlspecialchars($order['id_order']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($order['first_name']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($order['email']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($order['total_price']); ?> р.</div>
                    <div class="table-cell">
                        <?php if ($order['status'] === 'ждёт подтверждения'): ?>
                            <button class="button approve-button">Одобрить</button>
                        <?php elseif ($order['status'] === 'отклонено'): ?>
                            <button class="button declined-button" disabled>Отклонено</button>
                        <?php else: ?>
                            <button class="button approved-button" disabled>Одобрено</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Обработка кликов на кнопки "Одобрить"
    document.querySelectorAll(".approve-button").forEach(function(button) {
        button.addEventListener("click", function(event) {
            event.preventDefault();

            // Находим идентификатор заказа
            var orderId = button.closest(".table-row").dataset.orderId;

            // Создаем новый экземпляр объекта XMLHttpRequest
            var xhr = new XMLHttpRequest();

            // Устанавливаем метод и адрес URL для запроса
            xhr.open("POST", "../actions/approve_order.php", true);

            // Устанавливаем заголовок запроса
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Обработка ответа от сервера
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Обновляем кнопку и статус заказа на странице
                        button.disabled = true;
                        button.classList.add("approved-button");
                        button.textContent = "Одобрено";
                    } else {
                        console.error("Произошла ошибка при одобрении заказа");
                    }
                }
            };

            // Отправляем запрос на сервер с идентификатором заказа
            xhr.send("id_order=" + orderId);
        });
    });

    // Обработка кликов на строки таблицы для перехода на страницу деталей заказа
    document.querySelectorAll(".table-row").forEach(function(row) {
        row.addEventListener("click", function(event) {
            // Проверяем, что клик был не по кнопке "Одобрить"
            if (!event.target.classList.contains("approve-button")) {
                var orderId = row.dataset.orderId;
                window.location.href = "order.php?id_order=" + orderId;
            }
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>
