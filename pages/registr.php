<?php
include '../includes/header.php';
include '../config/db.php';

$errors = []; // Список ошибок

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, есть ли данные в массиве $_POST перед их использованием
    if(isset($_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['email'], $_POST['login'], $_POST['password'], $_POST['confirm_password'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $login = $_POST['login'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Проверка длины пароля (минимум 12 символов)
        if (strlen($password) < 12) {
            $errors[] = "Пароль должен содержать не менее 12 символов";
        }

        // Проверка сложности пароля (хотя бы одна заглавная буква, одна строчная буква, одна цифра и один специальный символ)
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{12,}$/', $password)) {
            $errors[] = "Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву, одну цифру и один специальный символ";
        }

        // Проверка совпадения паролей
        if ($password !== $confirm_password) {
            $errors[] = "Пароли не совпадают";
        }

        // Проверка уникальности логина
        $stmt = $pdo->prepare("SELECT * FROM user WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        if ($user) {
            $errors[] = "Логин уже занят";
        }

        // Проверка уникальности адреса электронной почты
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $errors[] = "Адрес электронной почты уже используется";
        }

        // Если нет ошибок, вставляем данные в таблицу user
        if (empty($errors)) {
            // Хеширование пароля с использованием bcrypt
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Вставляем данные в таблицу user с использованием параметризованного запроса
            $sql = "INSERT INTO user (login, password, first_name, last_name, telephone, email) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$login, $hashed_password, $first_name, $last_name, $phone, $email]);
            
        }
    } else {
        // В случае отсутствия данных в $_POST добавляем ошибку
        $errors[] = "Missing POST data";
    }
}
?>

<section class="reg">
    <div class="reg-container">
        <h3 class="form-h3">Регистрация</h3>
        <form method="post">
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
                <button class="button-reg" onclick="redirectToLoginPage()" type="button">Авторизоваться</button>
                <button class="button" type="submit">Зарегистрироваться</button>
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

    function redirectToLoginPage() {
        window.location.href = "../pages/login.php";
    }

</script>

<?php
include '../includes/footer.php';
?>
