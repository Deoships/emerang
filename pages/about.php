<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/logo_ico.png" type="image/x-icon">
    <link rel="stylesheet" href="../scss/about.css">
    <title>О нас</title>
</head>
<body>
<?php
include '../includes/header.php';
?>
 <div class="about-container">
        <div class="contact-block">
            <h2>Контакты</h2>
            <div class="line"></div>
                <ul class="contact-flex">
                    <li><img src="../img/mail.png" alt="адрес"><p>Беларусь, г. Брест, ул. Московская, 123</p></li>
                    <li><img src="../img/mail.png" alt="телефон"><p>+7 123 456 78 90</p></li>
                    <li><img src="../img/mail.png" alt="почта"><p>market@mail.ru</p></li>
                    <li><img src="../img/mail.png" alt="расписание"><p>Время работы: с 8:00 до 20:00</p></li>
                 </ul>
        </div>
        <div class="location-block">
            <h2>Наше расположение</h2>
            <div class="line-bottom"></div>
            <div class="map"></div>
        </div>
    </div>
<?php
include '../includes/footer.php';
?>
</body>
</html>