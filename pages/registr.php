<?php
include '../includes/header.php';
?>

<section class="reg">
    <div class="reg-container">
        <h3 class="form-h3">Регистрация</h3>
        <form action="process_registration.php" method="post" id="registrationForm">
            <div class="form-group">
                <input type="text" name="first_name" placeholder="Имя">
            </div>
            <div class="form-group">
                <input type="text" name="last_name" placeholder="Фамилия">
            </div>
            <div class="form-group">
                <input type="tel" name="phone" id="phone" placeholder="Телефон">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин">
            </div>
            <div class="form-group password-container">
                <input type="password" name="password" id="password" class="password-field" placeholder="Пароль">
                <span onclick="showPassword('password')" class="eye-icon"> </span> <!-- Глазик для показа пароля -->
            </div>
            <div class="form-group password-container">
                <input type="password" name="confirm_password" id="confirm_password" class="password-field" placeholder="Повторите пароль">
                <span onclick="showPassword('confirm_password')" class="eye-icon"> </span> <!-- Глазик для показа пароля -->
            </div>
            <div class="btn-container">
                <button class="button-reg" type="submit">Авторизоваться</button>
                <button class="button" type="submit">Зарегестрироваться</button>
            </div>
        </form>
    </div>
</section>

<script>
    $(document).ready(function(){
        $('#phone').mask('+7 (999) 999-99-99');
    });

    function showPassword(inputId) {
        var passwordField = document.getElementById(inputId);
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
</script>

<?php
include '../includes/footer.php';
?>
