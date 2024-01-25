document.addEventListener("DOMContentLoaded", function() {
    const menuItems = document.querySelectorAll('.menu-item');

    // Обработчик клика для каждого элемента
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Удаляем класс 'active' у всех элементов
            menuItems.forEach(otherItem => {
                otherItem.classList.remove('active');
            });

            // Добавляем класс 'active' текущему элементу
            this.classList.add('active');
        });
    });
});
