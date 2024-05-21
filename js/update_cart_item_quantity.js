document.addEventListener("DOMContentLoaded", function() {
    // Находим все кнопки увеличения и уменьшения количества товара
    var incrementButtons = document.querySelectorAll(".increment");
    var decrementButtons = document.querySelectorAll(".decrement");

    // Добавляем обработчики событий для кнопок
    incrementButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var cartItemId = button.dataset.cartItemId;
            var quantityElement = button.parentElement.querySelector(".count-value");
            var quantity = parseInt(quantityElement.textContent);
            quantity++;
            updateCartItemQuantity(cartItemId, quantity);
            quantityElement.textContent = quantity;

            // Обновляем итоговую цену при увеличении количества товара
            updateTotalPrice();
        });
    });

    decrementButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var cartItemId = button.dataset.cartItemId;
            var quantityElement = button.parentElement.querySelector(".count-value");
            var quantity = parseInt(quantityElement.textContent);
            if (quantity > 1) {
                quantity--;
                updateCartItemQuantity(cartItemId, quantity);
                quantityElement.textContent = quantity;

                // Обновляем итоговую цену при уменьшении количества товара
                updateTotalPrice();
            }
        });
    });

    // Функция для обновления итоговой цены
    function updateTotalPrice() {
        // Находим все цены товаров и их количество
        var prices = document.querySelectorAll(".account-price");
        var quantities = document.querySelectorAll(".count-value");

        var totalPrice = 0;

        // Вычисляем общую стоимость
        for (var i = 0; i < prices.length; i++) {
            var price = parseFloat(prices[i].textContent.replace(" р.", ""));
            var quantity = parseInt(quantities[i].textContent);
            totalPrice += price * quantity;
        }

        // Находим элемент для отображения итоговой цены и обновляем его значение
        var totalPriceElement = document.querySelector(".total h2");
        totalPriceElement.textContent = totalPrice.toFixed(2) + " p.";
    }
});

// Остальной код оставляем без изменений
function updateCartItemQuantity(cartItemId, quantity) {
    // Создаем новый экземпляр объекта XMLHttpRequest
    var xhr = new XMLHttpRequest();

    // Устанавливаем метод и адрес URL для запроса
    xhr.open("POST", "../actions/update_cart_item_quantity.php", true);

    // Устанавливаем заголовок запроса
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Отправляем запрос на сервер
    xhr.send("id_cart_item=" + cartItemId + "&quantity=" + quantity);

    // Обрабатываем ответ от сервера
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log("Количество товара успешно обновлено");
            } else {
                console.error("Произошла ошибка при обновлении количества товара");
            }
        }
    };
}
