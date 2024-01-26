<?php
include '../includes/header.php';
include '../config/db.php';

$product_id = isset($_GET['id']) ? $_GET['id'] : null;
$product_color = isset($_GET['color']) ? $_GET['color'] : null;

if ($product_id) {
    $stmt = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, i.url
        FROM product p
        JOIN type t ON p.id_type = t.id_type
        JOIN img i ON p.id_product = i.id_product
        JOIN color c ON i.id_color = c.id_color
        WHERE p.id_product = :product_id
        GROUP BY p.id_product, c.id_color
    ");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $stmt_images = $pdo->prepare("SELECT i.url, c.name as color FROM img i JOIN color c ON i.id_color = c.id_color WHERE i.id_product = :product_id");
        $stmt_images->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_images->execute();
        $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

        // Запрос характеристик
        $stmt_characteristics = $pdo->prepare("SELECT name, value FROM characteristic WHERE id_product = :product_id");
        $stmt_characteristics->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_characteristics->execute();
        $characteristics = $stmt_characteristics->fetchAll(PDO::FETCH_ASSOC);
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
                <button class="buy-btn" onclick="addToCart(<?= $product['id_product'] ?>, '<?= $product['name'] ?>', <?= $product['price'] ?>)">
    Приобрести
</button>
            </div>
            </div>
        </div>
    </div>
    <script src="../js/slider.js"></script>
</section>

<script>
function loadCartContent() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                document.getElementById('cart-content').innerHTML = xhr.responseText;
            } else {
                console.error('Произошла ошибка при загрузке содержимого корзины');
            }
        }
    };
    xhr.open('GET', '../includes/popup.php', true); // Обновленный путь к файлу popup.php
    xhr.send();
}

</script>



<?php
    } else {
        echo "Товар не найден.";
    }
} else {
    echo "Не указан id товара.";
}

include '../includes/footer.php';
?>
