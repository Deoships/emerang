document.addEventListener('DOMContentLoaded', function() {
    var addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var productId = button.dataset.productId;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../actions/add_to_cart.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Товар успешно добавлен в корзину');
                    } else {
                        console.error('Произошла ошибка при добавлении товара в корзину');
                    }
                }
            };
            xhr.send('product_id=' + productId);
        });
    });
});
