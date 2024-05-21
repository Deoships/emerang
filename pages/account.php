<?php
session_start();

// Проверяем, если пользователь нажал на кнопку "Выход"
if (isset($_POST['logout'])) {
    session_destroy();
    echo '<script>window.location.href = "../pages/login.php";</script>';
    exit();
}

include '../includes/header.php';
include '../config/db.php';

// Проверяем, если пользователь не авторизован
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../pages/login.php";</script>';
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $_SESSION['user'];

$first_name = isset($user['first_name']) ? $user['first_name'] : '';
$last_name = isset($user['last_name']) ? $user['last_name'] : '';
$email = isset($user['email']) ? $user['email'] : '';
$telephone = isset($user['telephone']) ? $user['telephone'] : '';

// Проверяем, если был отправлен новый пароль
if (isset($_POST['new-password'])) {
    $new_password = $_POST['new-password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update_password_query = "UPDATE user SET password = ? WHERE id_user = ?";
    $stmt = $pdo->prepare($update_password_query);
    $stmt->execute([$hashed_password, $user_id]);

    echo '<script>window.location.href = "../pages/account.php";</script>';
    exit();
}

// Запрос для получения информации о товарах в корзине пользователя
$stmt = $pdo->prepare("
SELECT ci.id_cart_item, ci.quantity, p.id_product, p.name AS product_name, p.price, img.url AS image_url
FROM cart_item ci 
INNER JOIN product p ON ci.id_product = p.id_product 
INNER JOIN cart c ON ci.id_cart = c.id_cart 
INNER JOIN (
    SELECT id_product, MIN(url) AS url
    FROM img
    GROUP BY id_product
) img ON img.id_product = p.id_product
WHERE c.id_user = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_price = 0; 

foreach ($cart_items as $item) {
    $item_price = $item['price'] * $item['quantity'];
    $total_price += $item_price;
}

// Обработка данных заказа
if (isset($_POST['submit-order'])) {
    $comment = $_POST['comment'];
    $payment_method = $_POST['payment-method'];

    // Вставляем заказ в таблицу orders
    $order_query = "INSERT INTO orders (id_user, total_price, comment, payment_method, status) VALUES (?, ?, ?, ?, 'ждёт подтверждения')";
    $stmt = $pdo->prepare($order_query);

    try {
        $pdo->beginTransaction(); // Начинаем транзакцию
        $stmt->execute([$user_id, $total_price, $comment, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // Вставляем каждый товар из корзины в таблицу order_items
        $order_item_query = "INSERT INTO order_items (id_order, id_product, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($order_item_query);

        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['id_product'], $item['quantity'], $item['price']]);
        }

        // Очищаем корзину пользователя
        $clear_cart_query = "DELETE FROM cart_item WHERE id_cart IN (SELECT id_cart FROM cart WHERE id_user = ?)";
        $stmt = $pdo->prepare($clear_cart_query);
        $stmt->execute([$user_id]);

        $pdo->commit(); // Завершаем транзакцию

        echo '<script>alert("Ваш заказ успешно отправлен!"); window.location.href = "../pages/account.php";</script>';
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // Откатываем транзакцию в случае ошибки
        echo '<script>alert("Ошибка при обработке заказа: ' . $e->getMessage() . '");</script>';
    }
}
?>

<section>
    <div class="wrap">
        <h1 class="catalog-h1">Ваш профиль</h1>
        <div class="account">
            <div class="account-img">
                <svg width="130.000000" height="130.000000" viewBox="0 0 130 130" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs/>
                    <rect id="Icon" width="130.000000" height="130.000000" fill="#FFFFFF" fill-opacity="0"/>
                    <circle id="Ellipse 11" cx="65.000000" cy="65.000000" r="51.500000" stroke="#0B8063" stroke-opacity="1.000000" stroke-width="5.000000"/>
                    <path id="Icon" d="M65 56.5C57.89 56.5 52.14 50.79 52.14 43.75C52.14 36.7 57.89 31 65 31C72.1 31 77.85 36.7 77.85 43.75C77.85 50.79 72.1 56.5 65 56.5ZM35 99L35 86.24C35 79.2 40.75 73.5 47.85 73.5L82.14 73.5C89.24 73.5 95 79.2 95 86.25L94.99 99" stroke="#0B8063" stroke-opacity="1.000000" stroke-width="5.000000" stroke-linejoin="round" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="account-info">
                <h2 class="index-h2"><?php echo $first_name . " " . $last_name; ?></h2>
                <p class="contact-p">Почта: <b><?php echo $email; ?></b></p>
                <p class="contact-p">Телефон: <b><?php echo $telephone; ?></b></p>
                <form method="post"> 
                    <div id="change-password" class="form-group-eddit" style="display: none;">
                        <input type="password" class="form-group-input" id="new-password" name="new-password" placeholder="Введите новый пароль">
                        <button id="save-password" class="button-save" type="submit">Сохранить пароль</button> 
                    </div>
                </form>
                <button class="button-edit" type="button">Изменить пароль</button>
            </div>
            <form method="post">
                <button class="back-btn" name="logout">Выход</button>
            </form>
        </div>
        <div class="account-cart">
            <?php foreach ($cart_items as $item): ?>
                <div class="account-1">
                    <div class="account-foto"><img src="<?= $item['image_url'] ?>" alt="<?= $item['product_name'] ?>"></div>
                    <div class="account-wrap">
                        <div class="account-details">
                            <div class="account-name"><b><?= $item['product_name'] ?></b></div>
                        </div>
                        <div class="account-count">
                            <div class="account-price"><?= $item['price'] ?> р.</div>
                            <div class="count">
                                <button class="count-btn decrement" data-cart-item-id="<?= $item['id_cart_item'] ?>">-</button>
                                <span class="count-value" data-cart-item-id="<?= $item['id_cart_item'] ?>"><?= $item['quantity'] ?></span>
                                <button class="count-btn increment" data-cart-item-id="<?= $item['id_cart_item'] ?>">+</button>
                            </div>
                            <form method="post" action="../actions/delete_cart_item.php">
                                <input type="hidden" name="id_cart_item" value="<?= $item['id_cart_item'] ?>">
                                <button class="account-delete" type="submit">
                                    <svg width="21.000000" height="20.000000" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <path id="Vector" d="M2.32 4.65L3.63 14.62C3.93 16.86 4.65 18.5 7.31 18.5L12.92 18.5C15.81 18.5 16.24 16.93 16.57 14.76L18.13 4.65M0 4.19L2.87 3.93C7.76 3.49 12.69 3.5 17.58 3.96L20 4.19M5.11 3.57C5.11 2.09 6.24 0.85 7.72 0.72L8.61 0.64C9.69 0.54 10.77 0.54 11.84 0.64L12.74 0.72C14.21 0.85 15.34 2.09 15.34 3.57" stroke="#EF1212" stroke-opacity="1.000000" stroke-width="1.500000"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <form method="post">
                <textarea class="comment-field" name="comment" placeholder="Ваш комментарий к заказу"></textarea>
                <div class="account-pay">
                    <div class="payment-radio">
                        <p class="catalog-p"><b>Выбор оплаты:</b></p> 
                        <input type="radio" id="card-payment" name="payment-method" value="card">
                        <label for="card-payment">Карточкой через терминал VISA, Master Card, Belcard</label><br>
                        <input type="radio" id="cash-payment" name="payment-method" value="cash">
                        <label for="cash-payment">Оплата наличными</label>
                    </div>
                    <div class="total">
                        <h3><b>Итого к оплате:</b></h3>
                        <h2><b><?= number_format($total_price, 2) ?> р.</b></h2>
                        <button class="button" type="submit" name="submit-order">Отправить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script src="../js/update_cart_item_quantity.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var deleteButtons = document.querySelectorAll(".account-delete");
    var changePasswordButton = document.querySelector(".button-edit");
    var changePasswordDiv = document.getElementById("change-password");
    var savePasswordButton = document.getElementById("save-password");

    changePasswordButton.addEventListener("click", function() {
        changePasswordDiv.style.display = "block";
    });

    savePasswordButton.addEventListener("click", function() {
        changePasswordDiv.style.display = "none";
    });

    deleteButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var cartItemId = button.dataset.cartItemId;
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../actions/delete_cart_item.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("id_cart_item=" + cartItemId);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        window.location.reload();
                    } else {
                        console.error("Произошла ошибка при удалении товара из корзины");
                    }
                }
            };
        });
    });
});
</script>

<?php
include '../includes/footer.php';
?>
