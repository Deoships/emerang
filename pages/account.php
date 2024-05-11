<?php
session_start();

// Проверяем, если пользователь нажал на кнопку "Выход"
if (isset($_POST['logout'])) {
    // Уничтожаем сессию
    session_destroy();

    // Перенаправляем пользователя на страницу авторизации
    echo '<script>window.location.href = "../pages/login.php";</script>';
    exit();
}

include '../includes/header.php';
include '../config/db.php';

// Проверяем, если пользователь не авторизован, перенаправляем на страницу логина
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.href = "../pages/login.php";</script>';
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $_SESSION['user'];

// Проверяем, что у пользователя есть имя и фамилия
$first_name = isset($user['first_name']) ? $user['first_name'] : '';
$last_name = isset($user['last_name']) ? $user['last_name'] : '';
$email = isset($user['email']) ? $user['email'] : '';
$telephone = isset($user['telephone']) ? $user['telephone'] : '';

// Проверяем, если был отправлен новый пароль
if (isset($_POST['new-password'])) {
    $new_password = $_POST['new-password'];
    
    // Хешируем новый пароль перед сохранением в базу данных
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Подготавливаем SQL-запрос для обновления пароля пользователя
    $update_password_query = "UPDATE user SET password = ? WHERE id_user = ?";
    $stmt = $pdo->prepare($update_password_query);
    
    // Выполняем запрос с учетом параметров
    $stmt->execute([$hashed_password, $user_id]);
    
    // Перенаправляем пользователя на страницу профиля после сохранения пароля
    echo '<script>window.location.href = "../pages/account.php";</script>';
    exit();
}

// Запрос для получения информации о товарах в корзине пользователя
$stmt = $pdo->prepare("
SELECT ci.quantity, p.name AS product_name, p.price, img.url AS image_url
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
                <form method="post"> <!-- Добавлен атрибут method="post" -->
                    <div id="change-password" class="form-group-eddit" style="display: none;">
                        <input type="password" class="form-group-input" id="new-password" name="new-password" placeholder="Введите новый пароль">
                        <button id="save-password" class="button-save" type="submit">Сохранить пароль</button> <!-- Изменен тип кнопки на submit -->
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
                <!-- Карточка товара -->
                <div class="account-1">
                            <div class="account-foto"><img src="<?= $item['image_url'] ?>" alt="<?= $item['product_name'] ?>"></div>
                    <div class="account-wrap">
            
                    <div class="account-details">
                        <div class="account-name"><b><?= $item['product_name'] ?></b></div>
                        
                    </div>
                    <div class="account-count">
                        <div class="account-price"><?= $item['price'] ?> р.</div>
                    <div class="count">
                        <button class="count-btn">-</button>
                        <span class="count-value"><?= $item['quantity'] ?></span>
                        <button class="count-btn">+</button>
                    </div>
                    <div class="account-delete">
                        <!-- Кнопка удаления товара -->
                        <svg width="21.000000" height="20.000000" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">

	<path id="Vector" d="M2.32 4.65L3.63 14.62C3.93 16.86 4.65 18.5 7.31 18.5L12.92 18.5C15.81 18.5 16.24 16.93 16.57 14.76L18.13 4.65M0 4.19L2.87 3.93C7.76 3.49 12.69 3.5 17.58 3.96L20 4.19M5.11 3.57C5.11 2.09 6.24 0.85 7.72 0.72L8.61 0.64C9.69 0.54 10.77 0.54 11.84 0.64L12.74 0.72C14.21 0.85 15.34 2.09 15.34 3.57" stroke="#EF1212" stroke-opacity="1.000000" stroke-width="1.500000"/>
</svg>
                    </div>
                </div>
                </div>
                    
                </div>
            <?php endforeach; ?>
            <!-- Добавьте код для комментариев и блока оплаты здесь -->
            <textarea class="comment-field" placeholder="Ваш комментарий к заказу"></textarea>
            <div class="account-pay">
                <div class="payment-radio">
                    <p class="catalog-p"><b>Выбор оплаты:</b></p> 
                    <input type="radio" id="card-payment" name="payment-method" value="card">
                    <label for="card-payment">Карточкой через терминал VISA, Master Card, Belcard</label><br>
                    <input type="radio" id="cash-payment" name="payment-method" value="cash">
                    <label for="cash-payment">Оплата наличными</label>
                </div>
                <div class="total">
                    <p><b>Итого к оплате:</b></p>
                    <!-- Добавьте вывод общей суммы заказа -->
                    <h2><b>$10.00</b></h2>
                    <button class="button">Оплатить</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Находим кнопку "Изменить пароль"
    var changePasswordButton = document.querySelector(".button-edit");

    // Находим поле для ввода нового пароля
    var changePasswordDiv = document.getElementById("change-password");

    // Находим кнопку "Сохранить пароль"
    var savePasswordButton = document.getElementById("save-password");

    // При клике на кнопку "Изменить пароль"
    changePasswordButton.addEventListener("click", function() {
        // Показываем поле для ввода нового пароля
        changePasswordDiv.style.display = "block";
    });

    // При клике на кнопку "Сохранить пароль"
    savePasswordButton.addEventListener("click", function() {
        // Скрываем поле для ввода нового пароля после сохранения
        changePasswordDiv.style.display = "none";
        // TODO: Добавьте код для сохранения нового пароля на сервере
    });
});
</script>


<?php
include '../includes/footer.php';
?>
