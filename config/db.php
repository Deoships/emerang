<?php
$host = "localhost";
$dbname = "emerang";
$user = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
} catch (PDOException $e) {
    die('Подключение не удалось: ' . $e->getMessage());
}
?>
