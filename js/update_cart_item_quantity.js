document.addEventListener("DOMContentLoaded", function() {
    var incrementButtons = document.querySelectorAll(".increment");
    var decrementButtons = document.querySelectorAll(".decrement");

    incrementButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var cartItemId = button.dataset.cartItemId;
            var quantityElement = button.parentElement.querySelector(".count-value");
            var quantity = parseInt(quantityElement.textContent);
            quantity++;
            updateCartItemQuantity(cartItemId, quantity);
            quantityElement.textContent = quantity;
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
                updateTotalPrice();
            }
        });
    });

    function updateTotalPrice() {
        var prices = document.querySelectorAll(".account-price");
        var quantities = document.querySelectorAll(".count-value");
        var totalPrice = 0;

        for (var i = 0; i < prices.length; i++) {
            var price = parseFloat(prices[i].textContent.replace(" р.", ""));
            var quantity = parseInt(quantities[i].textContent);
            totalPrice += price * quantity;
        }

        var totalPriceElement = document.querySelector(".total h2");
        totalPriceElement.textContent = totalPrice.toFixed(2) + " p.";
    }
});

function updateCartItemQuantity(cartItemId, quantity) {
    var xhr = new XMLHttpRequest();

    xhr.open("POST", "../actions/update_cart_item_quantity.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("id_cart_item=" + cartItemId + "&quantity=" + quantity);
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
