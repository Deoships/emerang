<?php
session_start();
include '../config/db.php';
include '../includes/header.php';

// Проверяем, если пользователь уже авторизован, перенаправляем на страницу аккаунта
if (isset($_SESSION['user'])) {
    redirectToAccount();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'], $_POST['password'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Проверка логина и пароля в базе данных
    $stmt = $pdo->prepare("SELECT * FROM user WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Сохраняем данные пользователя в сессии
        $_SESSION['user'] = $user;

        // Перенаправляем пользователя на страницу аккаунта
        if (isset($_SESSION['redirect'])) {
            $redirect = $_SESSION['redirect'];
            unset($_SESSION['redirect']);
            redirectTo($redirect);
        } else {
            redirectToAccount();
        }
    } else {
        // Если пользователь не найден или пароль не совпадает, выводим ошибку
        echo "<script>alert('Неправильный логин или пароль');</script>";
    }
}

function redirectToAccount() {
    echo "<script>window.location.href = '../pages/account.php';</script>";
    exit();
}

function redirectTo($url) {
    echo "<script>window.location.href = '$url';</script>";
    exit();
}
?>

<section class="auth">
    <div class="auth-container">
        <h3 class="form-h3">Авторизация</h3>
        <form method="post">
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль">
            </div>
            <div class="btn-container">
                <button class="button-log" onclick="redirectToRegistrPage()" type="button">Зарегистрироваться</button>
                <button class="button" type="submit">Войти</button>
            </div>
        </form>
    </div>
</section>

<script>
    function redirectToRegistrPage() {
        window.location.href = "../pages/registr.php";
    }
</script>

<?php
include '../includes/footer.php';
ob_end_flush();
?>
