<?php
session_start();

// Проверяем, если пользователь нажал на кнопку "Выход"
if (isset($_POST['logout'])) {
    // Уничтожаем сессию
    session_destroy();

    // Перенаправляем пользователя на страницу авторизации
    header("Location: ../pages/login.php");
    exit();
}

include '../includes/header.php';
include '../config/db.php';

// Проверяем, если пользователь не авторизован, перенаправляем на страницу логина
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Получаем данные пользователя из сессии
$user = $_SESSION['user'];

// Проверяем, что у пользователя есть имя и фамилия
$first_name = isset($user['first_name']) ? $user['first_name'] : '';
$last_name = isset($user['last_name']) ? $user['last_name'] : '';
$email = isset($user['email']) ? $user['email'] : '';
$telephone = isset($user['telephone']) ? $user['telephone'] : '';
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
                <button class="button-edit" type="button">Изменить пароль</button>
            </div>
            <form method="post">
                <button class="back-btn" name="logout">Выход</button>
            </form>
        </div>
        <!-- Карточка товара -->
        <div class="account-cart">
            <div class="account">
                <div class="account-foto"></div>
                <div class="account-details">
                    <div class="account-name">Название товара</div>
                    <div class="account-price">Цена: $10.00</div>
                </div>
                <div class="account-count">
                    <button class="count-btn">-</button>
                    <span class="count-value">1</span>
                    <button class="count-btn">+</button>
                </div>
                <div class="account-delete">
                    <!-- Кнопка удаления товара -->
                    <svg width="24.000000" height="24.000000" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <defs>
                            <clipPath id="clip17_4531">
                                <rect id="иконка-минус" width="24.000000" height="24.000000" transform="translate(0.000000 -0.500000)" fill="white" fill-opacity="0"/>
                            </clipPath>
                        </defs>
                        <g clip-path="url(#clip17_4531)">
                            <path id="Vector" d="M4.32 6.65L5.63 16.62C5.93 18.86 6.65 20.5 9.31 20.5L14.92 20.5C17.81 20.5 18.24 18.93 18.57 16.76L20.13 6.65M2 6.19L4.87 5.93C9.76 5.49 14.69 5.5 19.58 5.96L22 6.19M7.11 5.57C7.11 4.09 8.24 2.85 9.72 2.72L10.61 2.64C11.69 2.54 12.77 2.54 13.84 2.64L14.74 2.72C16.21 2.85 17.34 4.09 17.34 5.57" stroke="#EF1212" stroke-opacity="1.000000" stroke-width="1.500000"/>
                        </g>
                    </svg>
                </div>
                
            </div>
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
            <h2><b>$10.00</b></h2>
            <button class="button">Оплатить</button>
        </div>
    </div>
      </div>
    </div>
</section>

<?php
include '../includes/footer.php';
?>
