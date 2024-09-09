document.addEventListener("DOMContentLoaded", function() {
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            menuItems.forEach(otherItem => {
                otherItem.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
});
