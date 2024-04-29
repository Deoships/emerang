<?php
$host = "localhost";
$dbname = "emerang1";
$user = "root";
$password = "123";


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
} catch (PDOException $e) {
    die('Подключение не удалось: ' . $e->getMessage());
}
?>
