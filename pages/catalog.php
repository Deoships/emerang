<?php
// В файле с функциями (например, functions.php)
function getCategoryClass($category) {
    $currentCategory = isset($_GET['category']) ? $_GET['category'] : '1';
    return ($category == $currentCategory) ? 'active' : '';
}

// В файле, где вы используете функцию
include '../includes/header.php';
include '../config/db.php'; 

$category = isset($_GET['category']) ? $_GET['category'] : '1';

$stmt = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, i.url
FROM product p
JOIN type t ON p.id_type = t.id_type
JOIN img i ON p.id_product = i.id_product
JOIN color c ON i.id_color = c.id_color
WHERE p.id_type = :selected_category
GROUP BY p.id_product, c.id_color
");
$stmt->bindParam(':selected_category', $category, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section>
    <div class="wrap">
        <h1 class="catalog-h1">Каталог</h1>
        
        <div class="menu-catalog">
            <a href="?category=1" class="<?= getCategoryClass(1) ?>">Смартфоны</a>
            <a href="?category=2" class="<?= getCategoryClass(2) ?>">Смарт-часы</a>
            <a href="?category=3" class="<?= getCategoryClass(3) ?>">Планшеты</a>
        </div>
        
        <div class="product-cards">
            <?php foreach ($products as $product): ?>
                <div class="product-card <?= getCategoryClass($product['id_type'] ?? '') ?>">
                    <img src="<?= $product['url'] ?>" alt="<?= $product['name'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <div class="product-info">
                        <p class="price"><?= $product['price'] ?> р.</p>
                        <button class="add-to-cart-btn" onclick="addToCart(<?= $product['id_product'] ?>)">
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="../js/menu.js"></script>
</section>

<?php
include '../includes/footer.php';
?>
