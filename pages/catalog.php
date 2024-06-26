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

function getCategoryClass($category) {
    $currentCategory = isset($_GET['category']) ? $_GET['category'] : '1';
    return ($category == $currentCategory) ? 'active' : '';
}

include '../includes/header.php';
include '../config/db.php'; 

$pdo->exec("set names utf8");
$pdo->exec("set character_set_client='utf8'");
$pdo->exec("set character_set_results='utf8'");
$pdo->exec("set collation_connection='utf8_general_ci'");

// Остальной код остается без изменений

$category = isset($_GET['category']) ? $_GET['category'] : '1';

$stmt = $pdo->prepare("SELECT p.id_product, p.name, p.price, t.name as type_name, c.name as color_name, MIN(i.url) as url
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
                <?php
                // Получаем цвет товара для передачи в URL-адресе
                $color = urlencode($product['color_name']);
                ?>
                <div class="product-card">
                <a href="../pages/product.php?id=<?= $product['id_product'] ?>&color=<?= $color ?>">
                    <img src="<?= $product['url'] ?>" alt="<?= $product['name'] ?>">
                    <h3><?= $product['name'] ?></h3>
                 
                    <div class="product-info">
                        <p class="price"><?= $product['price'] ?> р.</p>
                           </a> 
                        <button class="add-to-cart-btn" data-product-id="<?= $product['id_product'] ?>"></button>
                    </div>
               </div>
            <?php endforeach; ?>
        </div>
    </div>

<script src="../js/menu.js"></script>
<script src="../js/add_to_cart.js"></script>

</section>

<?php
include '../includes/footer.php';
?>
