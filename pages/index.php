<?php
include '../includes/header.php';
include '../config/db.php';

$stmt = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, MIN(i.url) as url
FROM product p
JOIN type t ON p.id_type = t.id_type
JOIN img i ON p.id_product = i.id_product
JOIN color c ON i.id_color = c.id_color
GROUP BY p.id_product, c.id_color
LIMIT 4
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
<div class="block">
        <div class="left-content">
            <h1 class="index-h1">Galaxy Z Flip5</h1>
            <h2 class="index-h2">Продолжение вашего тела</h2>
            <button class="button">Подробнее</button>
        </div>
        <div class="right-content">
            <div class="content-png"></div>
        </div>
    </div>

    <div class="block-2">
        <h2 class="index-h2">Популярные категории</h2>
        <div class="brand-cards">
            <div class="brand-card">
                <img src="../img/samsung.png" alt="Бренд 1">
                <p class="index-p">SAMSUNG</p>
            </div>
            <div class="brand-card">
                <img src="../img/apple.png" alt="Бренд 2">
                <p class="index-p">Apple</p>
            </div>
            <div class="brand-card">
                <img src="../img/XIAOMI.png" alt="Бренд 1">
                <p class="index-p">XIAOMI</p>
            </div>
            <div class="brand-card">
                <img src="../img/lg.png" alt="Бренд 2">
                <p class="index-p">LG</p>
            </div>
            <div class="brand-card">
                <img src="../img/asus.png" alt="Бренд 1">
                <p class="index-p">ASUS</p>
            </div>
            <div class="brand-card">
                <img src="../img/huawei.png" alt="Бренд 2">
                <p class="index-p">Huawei</p>
            </div>
            
    </div>

    <div class="block-2">
    <h2 class="index-h2">Популярные товары</h2>
    <div class="product-cards">
            <?php foreach ($products as $product): ?>
                <?php
                // Получаем цвет товара для передачи в URL-адресе
                $color = urlencode($product['color_name']);
                ?>
                <a href="../pages/product.php?id=<?= $product['id_product'] ?>&color=<?= $color ?>" class="product-card">
                    <img src="<?= $product['url'] ?>" alt="<?= $product['name'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <div class="product-info">
                        <p class="price"><?= $product['price'] ?> р.</p>
                        <button class="add-to-cart-btn" data-product-id="<?= $product['id_product'] ?>"></button>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="block reversed">
        <div class="left-content-1">
        <h1 class="index-h1">Скидки до 15% на все смарт часы</h1>
            <h2 class="index-h2">Самое время приобрести то что вы так давно хотели</h2>
            <button class="button">Подробнее</button>
        </div>
        <div class="right-content">
        <div class="content-png-1"></div>
        </div>
    </div>
    <div class="block-2">
    <h2 class="index-h2">Новые поступления</h2>
    <div class="product-cards">
            <?php foreach ($products as $product): ?>
                <?php
                // Получаем цвет товара для передачи в URL-адресе
                $color = urlencode($product['color_name']);
                ?>
                <a href="../pages/product.php?id=<?= $product['id_product'] ?>&color=<?= $color ?>" class="product-card">
                    <img src="<?= $product['url'] ?>" alt="<?= $product['name'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <div class="product-info">
                        <p class="price"><?= $product['price'] ?> р.</p>
                        <button class="add-to-cart-btn" data-product-id="<?= $product['id_product'] ?>"></button>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
include '../includes/footer.php';
?>