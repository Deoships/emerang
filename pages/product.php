<?php
session_start(); // Начинаем сессию пользователя

// Проверяем, был ли пользователь авторизован
if (isset($_SESSION['user_id'])) {
    // Здесь можно добавить код, связанный с установкой user_id в сессию
    // Например, что-то вроде:
    $user_id = $_SESSION['user_id'];
} else {
    // Если пользователь не авторизован, можно присвоить user_id значение по умолчанию
    // Например, что-то вроде:
    $user_id = 0; // Значение по умолчанию для неавторизованных пользователей
}

// Включаем файлы header.php и footer.php

include '../includes/header.php';
include '../config/db.php';
// Получение идентификатора товара из запроса
$product_id = isset($_GET['id']) ? $_GET['id'] : null;

// Получение цвета товара из запроса
$product_color = isset($_GET['color']) ? $_GET['color'] : null;

// Проверка наличия идентификатора товара
if ($product_id) {
    // Запрос информации о товаре из базы данных
    $stmt = $pdo->prepare("SELECT p.id_product, p.name, p.price, p.description, t.name as type_name, c.name as color_name, i.url
    FROM product p
    JOIN type t ON p.id_type = t.id_type
    JOIN img i ON p.id_product = i.id_product
    JOIN color c ON i.id_color = c.id_color
    WHERE p.id_product = :product_id");    
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверка наличия информации о товаре
    if ($product) {
        // Получение изображений товара
        $stmt_images = $pdo->prepare("SELECT i.url, c.name as color FROM img i JOIN color c ON i.id_color = c.id_color WHERE i.id_product = :product_id");
        $stmt_images->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_images->execute();
        $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

        // Запрос характеристик товара
        $stmt_characteristics = $pdo->prepare("SELECT name, value FROM characteristic WHERE id_product = :product_id");
        $stmt_characteristics->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_characteristics->execute();
        $characteristics = $stmt_characteristics->fetchAll(PDO::FETCH_ASSOC);

        // Запрос похожих товаров
        $stmt = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, MIN(i.url) as url
        FROM product p
        JOIN type t ON p.id_type = t.id_type
        JOIN img i ON p.id_product = i.id_product
        JOIN color c ON i.id_color = c.id_color
        WHERE p.id_product != :product_id
        GROUP BY p.id_product, c.id_color
        LIMIT 4");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!isset($_SESSION['previously_viewed_products'])) {
            $_SESSION['previously_viewed_products'] = array();
        }
        
        // Добавляем товар и его цвет в начало списка просмотренных
        array_unshift($_SESSION['previously_viewed_products'], array('id' => $product_id, 'color' => $product_color));
        
        // Обрезаем список просмотренных товаров до 4 элементов
        $_SESSION['previously_viewed_products'] = array_slice($_SESSION['previously_viewed_products'], 0, 4);
        
        $stmt_category = $pdo->prepare("SELECT id_type FROM product WHERE id_product = :product_id");
$stmt_category->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt_category->execute();
$category = $stmt_category->fetch(PDO::FETCH_ASSOC)['id_type'];

// Запрос похожих товаров той же категории, исключая выбранный товар
$stmt_similar_products = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, MIN(i.url) as url
FROM product p
JOIN type t ON p.id_type = t.id_type
JOIN img i ON p.id_product = i.id_product
JOIN color c ON i.id_color = c.id_color
WHERE p.id_type = :category AND p.id_product != :product_id
GROUP BY p.id_product, c.id_color
LIMIT 4");
$stmt_similar_products->bindParam(':category', $category, PDO::PARAM_INT);
$stmt_similar_products->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt_similar_products->execute();
$similar_products = $stmt_similar_products->fetchAll(PDO::FETCH_ASSOC);

        ?>

<section>
    <div class="wrap">
        <button onclick="goBack()" class="back-btn">Назад</button>
        <div class="product-details">
            <div class="slider-container">
                <div class="main-slider">
                    <img id="main-slider-image" src="<?= $product['url'] ?>" alt="<?= $product['name'] ?>">
                </div>
                <div class="thumbnails-and-arrows">
                    <div class="slider-nav prev" onclick="prevSlide()">&#10094;</div>
                    <div class="thumbnail-images">
                        <?php foreach ($images as $image): ?>
                            <div class="thumbnail-slide" data-color="<?= $image['color'] ?>">
                                <img src="<?= $image['url'] ?>" alt="<?= $product['name'] ?>" onclick="changeMainImage(this)">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="slider-nav next" onclick="nextSlide()">&#10095;</div>
                </div>
            </div>
            <div class="product-info">
                <div class="product-char">
                <h2 class="product-char-h2"><?= $product['name'] ?></h2>
                <h1 class="price-h1"><?= $product['price'] ?> р.</h1>
                <p class="color-unlock">Доступные цвета:</p>
                <div class="colors">
                    <?php
                    $stmt_colors = $pdo->prepare("SELECT DISTINCT c.name FROM color c JOIN img i ON c.id_color = i.id_color WHERE i.id_product = :product_id");
                    $stmt_colors->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $stmt_colors->execute();
                    $colors = $stmt_colors->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($colors as $color): ?>
                        <button class="color-btn" style="background-color: <?= $color['name'] ?>" onclick="changeMainImageByColor('<?= $color['name'] ?>')"></button>
                    <?php endforeach; ?>
                </div>
                <div class="characteristics">
                    <h3 class="characteristics-h3">Характеристики:</h3>
                    <ul class="characteristics-ul">
                        <?php foreach ($characteristics as $char): ?>
                            <li><?= $char['name'] ?>: <?= $char['value'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button class="buy-btn add-to-cart-btn" data-product-id="<?= $product['id_product'] ?>">Приобрести</button>
            </div>
            </div>
        </div>
    </div>
    <script src="../js/slider.js"></script>

    <div class="block-2">
        <div class="block-2-desc">
    <h2 class="index-h2">Описание</h2>
    <p class="index-p"><?= nl2br($product['description']) ?></p></div>
</div>


    <?php
// Запрос категории выбранного товара
$stmt_category = $pdo->prepare("SELECT id_type FROM product WHERE id_product = :product_id");
$stmt_category->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt_category->execute();
$category = $stmt_category->fetch(PDO::FETCH_ASSOC)['id_type'];

// Запрос похожих товаров той же категории, исключая выбранный товар
$stmt_similar_products = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, MIN(i.url) as url
FROM product p
JOIN type t ON p.id_type = t.id_type
JOIN img i ON p.id_product = i.id_product
JOIN color c ON i.id_color = c.id_color
WHERE p.id_type = :category AND p.id_product = :product_id
GROUP BY p.id_product, c.id_color
LIMIT 4");
$stmt_similar_products->bindParam(':category', $category, PDO::PARAM_INT);
$stmt_similar_products->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt_similar_products->execute();
$similar_products = $stmt_similar_products->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="block-2">
    <h2 class="index-h2">Похожие товары</h2>
    <div class="product-cards">
    <?php foreach ($similar_products as $similar_product): ?>
    <div class="product-card">
        <a href="../pages/product.php?id=<?= $similar_product['id_product'] ?>&color=<?= urlencode($similar_product['color_name']) ?>" class="product-card-link">
            <img src="<?= $similar_product['url'] ?>" alt="<?= $similar_product['name'] ?>">
            <h3><?= $similar_product['name'] ?></h3>
            <div class="product-info">
                <p class="price"><?= $similar_product['price'] ?> р.</p>
           
        </a>
        <button class="add-to-cart-btn" data-product-id="<?= $product['id_product'] ?>"></button>
         </div>
    </div>
<?php endforeach; ?>

    </div>
</div>

    <?php
// Проверяем, есть ли просмотренные товары в сессии
if (!empty($_SESSION['previously_viewed_products'])) {
?>
<div class="block-2">
    <h2 class="index-h2">Вы смотрели ранее</h2>
    <div class="product-cards">
        <?php
        // Выводим карточки просмотренных товаров того же цвета, на который нажали
        foreach ($_SESSION['previously_viewed_products'] as $previously_viewed_product):
            $previously_viewed_product_id = $previously_viewed_product['id'];
            $previously_viewed_product_color = $previously_viewed_product['color'];

            $stmt_product = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, i.url
                FROM product p
                JOIN type t ON p.id_type = t.id_type
                JOIN img i ON p.id_product = i.id_product
                JOIN color c ON i.id_color = c.id_color
                WHERE p.id_product = :product_id AND c.name = :color");
            $stmt_product->bindParam(':product_id', $previously_viewed_product_id, PDO::PARAM_INT);
            $stmt_product->bindParam(':color', $previously_viewed_product_color, PDO::PARAM_STR);
            $stmt_product->execute();
            $previously_viewed_product_info = $stmt_product->fetch(PDO::FETCH_ASSOC);

            if ($previously_viewed_product_info): ?>
                <div class="product-card">
                    <a href="../pages/product.php?id=<?= $previously_viewed_product_info['id_product'] ?>&color=<?= urlencode($previously_viewed_product_info['color_name']) ?>" class="product-card-link">
                        <img src="<?= $previously_viewed_product_info['url'] ?>" alt="<?= $previously_viewed_product_info['name'] ?>">
                        <h3><?= $previously_viewed_product_info['name'] ?></h3>
                        <div class="product-info">
                            <p class="price"><?= $previously_viewed_product_info['price'] ?> р.</p>
                      
                    </a>
                      
                    <button class="add-to-cart-btn" data-product-id="<?= $previously_viewed_product_info['id_product'] ?>"></button>
                </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php
}
?>
</section>
<script src="../js/add_to_cart.js"></script>
<script>
    function goBack() {
        window.history.back();
    }
</script>

<?php
    } else {
        echo "Товар не найден";
    }
} else {
    echo "Не указан id товара";
}

include '../includes/footer.php';
?>
