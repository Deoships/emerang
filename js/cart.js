// В файле popup.js

document.addEventListener('DOMContentLoaded', function() {
    const cartIcon = document.getElementById('cart-icon');
    const cartPopup = document.getElementById('cart-popup');
    const closePopup = document.querySelector('.close-popup');

    if (cartIcon && cartPopup && closePopup) {
        cartIcon.addEventListener('click', function() {
            cartPopup.style.display = 'block';
        });

        closePopup.addEventListener('click', function() {
            closeCartPopup();
        });
    } else {
        console.error('Element not found');
    }
});

