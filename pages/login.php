<?php
include '../includes/header.php';
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
