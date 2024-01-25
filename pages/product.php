<!-- Верхняя часть страницы, до начала PHP кода -->
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
                <h2><?= $product['name'] ?></h2>
                <p class="price"><?= $product['price'] ?> р.</p>
                <p class="type">Тип: <?= $product['type_name'] ?></p>
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
                <button class="buy-btn" onclick="addToCart(<?= $product['id_product'] ?>)">
                    Приобрести
                </button>
            </div>
        </div>
    </div>
    <script src="../js/slider.js"></script>
</section>


<?php
    } else {
        echo "Товар не найден.";
    }
} else {
    echo "Не указан id товара.";
}

include '../includes/footer.php';
?>
