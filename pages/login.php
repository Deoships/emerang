<?php
include '../includes/header.php';
?>

<section class="auth">
<div class="auth-container">
        <h3>Авторизация</h3>
        <form action="process_login.php" method="post">
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Пароль">
            </div>
            <div class="btn-container">
                <button type="submit">Зарегистрироваться</button>
                <button type="submit">Войти</button>
            </div>
        </form>
    </div>
</section>



<?php
include '../includes/footer.php';
?>