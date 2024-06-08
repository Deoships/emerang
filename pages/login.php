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

    $stmt = $pdo->prepare("SELECT * FROM user WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Сохраняем данные пользователя в сессию
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user'] = $user;
        
        // Перенаправляем пользователя на страницу аккаунта
        redirectToAccount();
    } else {
        // Если пользователь не найден или пароль не совпадает, выводим ошибку
        echo "<script>alert('Неправильный логин или пароль');</script>";
    }
}

function redirectToAccount() {
    // Получаем информацию о пользователе из сессии
    $user = $_SESSION['user'];

    // Проверяем роль пользователя и перенаправляем в зависимости от роли
    if ($user['id_role'] == 1) {
        // Перенаправляем на страницу администратора
        echo "<script>window.location.href = '../admin/admin.php';</script>";
    } elseif ($user['id_role'] == 2) {
        // Перенаправляем на страницу менеджера
        echo "<script>window.location.href = '../pages/manager.php';</script>";
    } else {
        // По умолчанию перенаправляем на страницу аккаунта
        echo "<script>window.location.href = '../pages/account.php';</script>";
    }
    exit();
}
?>

<section class="auth">
    <div class="auth-container">
        <h3 class="form-h3">Авторизация</h3>
        <form method="post">
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль" required>
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
?>
