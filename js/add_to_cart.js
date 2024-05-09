document.addEventListener('DOMContentLoaded', function() {
    // Находим все кнопки добавления товара в корзину
    var addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    // Перебираем найденные кнопки и назначаем обработчик клика на каждую из них
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Получаем идентификатор товара из атрибута data-product-id кнопки
            var productId = button.dataset.productId;

            // Отправляем AJAX-запрос на сервер для добавления товара в корзину
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../actions/add_to_cart.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Здесь можешь добавить обработку успешного добавления товара в корзину
                        console.log('Товар успешно добавлен в корзину');
                    } else {
                        // Здесь можешь добавить обработку ошибки добавления товара в корзину
                        console.error('Произошла ошибка при добавлении товара в корзину');
                    }
                }
            };
            xhr.send('product_id=' + productId);
        });
    });
});