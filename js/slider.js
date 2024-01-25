// JavaScript код
const mainSliderImage = document.getElementById('main-slider-image');
const thumbnailSlides = document.querySelectorAll('.thumbnail-slide');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');

let imagesByColor = {}; // Объект для хранения массива изображений по цветам

// Функция для замены большой картинки
function changeMainImage(imageUrl) {
    mainSliderImage.src = imageUrl;
}

// Функция для изменения основной картинки по выбранному цвету
function changeMainImageByColor(color) {
    const images = imagesByColor[color];
    if (images && images.length > 0) {
        changeMainImage(images[0].src);
    }
}

// Функция для фильтрации изображений по цвету
function filterImagesByColor(color) {
    thumbnailSlides.forEach(function(slide) {
        if (slide.dataset.color === color) {
            slide.style.display = 'block'; // Отображаем только нужные изображения
        } else {
            slide.style.display = 'none'; // Скрываем остальные изображения
        }
    });
}

// Функция для создания массива изображений для каждого цвета
function createImagesByColor() {
    thumbnailSlides.forEach(function(slide) {
        const color = slide.dataset.color;
        if (!imagesByColor[color]) {
            imagesByColor[color] = [];
        }
        imagesByColor[color].push(slide.querySelector('img'));
    });
}

// Получаем выбранный цвет из URL-адреса
const urlParams = new URLSearchParams(window.location.search);
const selectedColor = urlParams.get('color');

// Вызываем функцию для фильтрации изображений и отображения выбранного цвета при загрузке страницы
if (selectedColor) {
    filterImagesByColor(selectedColor);
    changeMainImageByColor(selectedColor);
}

// Обработчики событий для нажатия на маленькие картинки
thumbnailSlides.forEach(function(thumbnail) {
    thumbnail.addEventListener('click', function() {
        const imageUrl = thumbnail.querySelector('img').src;
        changeMainImage(imageUrl);
        thumbnailSlides.forEach(thumb => thumb.classList.remove('active'));
        thumbnail.classList.add('active');

        // Получаем текущий выбранный цвет и фильтруем изображения
        const activeColor = thumbnail.dataset.color;
        filterImagesByColor(activeColor);
    });
});

// Обработчики событий для нажатия на стрелки
prevButton.addEventListener('click', function() {
    const currentIndex = Array.from(thumbnailSlides).findIndex(thumbnail => thumbnail.classList.contains('active'));
    let prevIndex = (currentIndex - 1 + thumbnailSlides.length) % thumbnailSlides.length;

    // Проверяем доступность предыдущего слайда того же цвета
    while (thumbnailSlides[prevIndex].style.display === 'none') {
        prevIndex = (prevIndex - 1 + thumbnailSlides.length) % thumbnailSlides.length;
        if (prevIndex === currentIndex) return; // Если вернулись к текущему слайду, выходим из функции
    }

    const prevImageUrl = thumbnailSlides[prevIndex].querySelector('img').src;
    changeMainImage(prevImageUrl);
    thumbnailSlides[currentIndex].classList.remove('active');
    thumbnailSlides[prevIndex].classList.add('active');

    // Получаем текущий выбранный цвет и фильтруем изображения
    const activeColor = document.querySelector('.thumbnail-slide.active').dataset.color;
    filterImagesByColor(activeColor);
});

nextButton.addEventListener('click', function() {
    const currentIndex = Array.from(thumbnailSlides).findIndex(thumbnail => thumbnail.classList.contains('active'));
    let nextIndex = (currentIndex + 1) % thumbnailSlides.length;

    // Проверяем доступность следующего слайда того же цвета
    while (thumbnailSlides[nextIndex].style.display === 'none') {
        nextIndex = (nextIndex + 1) % thumbnailSlides.length;
        if (nextIndex === currentIndex) return; // Если вернулись к текущему слайду, выходим из функции
    }

    const nextImageUrl = thumbnailSlides[nextIndex].querySelector('img').src;
    changeMainImage(nextImageUrl);
    thumbnailSlides[currentIndex].classList.remove('active');
    thumbnailSlides[nextIndex].classList.add('active');

    // Получаем текущий выбранный цвет и фильтруем изображения
    const activeColor = document.querySelector('.thumbnail-slide.active').dataset.color;
    filterImagesByColor(activeColor);
});

// Обработчики событий для нажатия на кнопки с цветами
document.addEventListener('DOMContentLoaded', function() {
    createImagesByColor(); // Создаем массив изображений для каждого цвета
    var colorButtons = document.querySelectorAll('.color-btn');

    colorButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var color = button.style.backgroundColor;
            filterImagesByColor(color);
            changeMainImageByColor(color); // Обновляем основную картинку по выбранному цвету
        });
    });
});
