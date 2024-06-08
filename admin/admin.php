<?php
include '../config/db.php';
include '../includes/header.php';

$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $login = $_POST['login'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        
        $query = "INSERT INTO user (login, password, first_name, last_name, telephone, email) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$login, $password, $first_name, $last_name, $telephone, $email]);
        $success_message = 'Пользователь успешно добавлен';
    }

    if (isset($_POST['update_user'])) {
        $id = $_POST['id'];
        $login = $_POST['login'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        
        $query = "UPDATE user SET login = ?, password = ?, first_name = ?, last_name = ?, telephone = ?, email = ? WHERE id_user = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$login, $password, $first_name, $last_name, $telephone, $email, $id]);
        $success_message = 'Пользователь успешно обновлен';
    }

    if (isset($_POST['delete_user'])) {
        $id = $_POST['id'];
        
        $query = "DELETE FROM user WHERE id_user = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $success_message = 'Пользователь успешно удален';
    }
}
?>

<section class="admin">

<h3 class="form-h3">  
<?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        </h3> 
    <h3 class="form-h3">Админ-панель</h3>    
    <div class="admin-container">
        <form method="post">
            <h3 class="form-h3">Добавление</h3>    
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин" required>
            </div>
            <div class="form-group password-container">
                <input type="password" name="password" id="password" class="password-field" placeholder="Пароль" required>
            </div>
            <div class="form-group">
                <input type="text" name="first_name" placeholder="Имя" required>
            </div>
            <div class="form-group">
                <input type="text" name="last_name" placeholder="Фамилия" required>
            </div>
            <div class="form-group">
                <input type="tel" name="telephone" id="telephone" placeholder="Телефон">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="btn-container">
                <button class="button" type="submit" name="add_user">Зарегистрироваться</button>
            </div>
        </form>

        <form method="post">
            <h3 class="form-h3">Изменение</h3> 
            <div class="form-group">
                <input type="text" name="id" placeholder="ID" required>
            </div>
            <div class="form-group">
                <input type="text" name="login" placeholder="Логин" required>
            </div>
            <div class="form-group password-container">
                <input type="password" name="password" id="password" class="password-field" placeholder="Пароль" required>
            </div>
            <div class="form-group">
                <input type="text" name="first_name" placeholder="Имя" required>
            </div>
            <div class="form-group">
                <input type="text" name="last_name" placeholder="Фамилия" required>
            </div>
            <div class="form-group">
                <input type="tel" name="telephone" id="telephone" placeholder="Телефон">
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="btn-container">
                <button class="button" type="submit" name="update_user">Обновить</button>
            </div>
        </form>

        <form method="post">
            <h3 class="form-h3">Удаление</h3> 
            <div class="form-group">
                <input type="text" name="id" placeholder="ID" required>
            </div>
            <div class="btn-container">
                <button class="button" type="submit" name="delete_user">Удалить</button>
            </div>
        </form>
    </div>
</section>

<?php
include '../includes/footer.php';
?>
