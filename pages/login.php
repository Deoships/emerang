<?php
include '../includes/header.php';
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['login'], $_POST['password'])) {
        $login = $_POST['login'];
        $password = $_POST['password'];

        // Проверка логина и пароля в базе данных
        $stmt = $pdo->prepare("SELECT * FROM user WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Если пользователь найден и пароль совпадает, перенаправляем с помощью JavaScript
            echo "<script>window.location.href='../pages/account.php';</script>";
            exit();
        } else {
            // Если пользователь не найден или пароль не совпадает, выводим ошибку
            echo "Неправильный логин или пароль";
        }
    } else {
        // В случае отсутствия данных в $_POST добавляем ошибку
        echo "Missing POST data";
    }
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
?>
