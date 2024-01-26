document.addEventListener('DOMContentLoaded', function() {
    const cartPopup = document.getElementById('cart-popup');
    const cartIcon = document.getElementById('cart-icon');

    // Открытие поп-ап окна при клике на иконку корзины
    cartIcon.addEventListener('click', function() {
        cartPopup.classList.add('show');
    });

    // Закрытие поп-ап окна при клике на крестик
    const closePopup = document.querySelector('.close-popup');
    closePopup.addEventListener('click', function() {
        cartPopup.classList.remove('show');
    });

    // Закрытие поп-ап окна при клике вне него
    window.addEventListener('click', function(event) {
        if (!cartPopup.contains(event.target) && event.target !== cartIcon) {
            cartPopup.classList.remove('show');
        }
    });
});
