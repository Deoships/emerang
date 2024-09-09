const mainSliderImage = document.getElementById('main-slider-image');
const thumbnailSlides = document.querySelectorAll('.thumbnail-slide');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');

let imagesByColor = {}; // Объект для хранения массива изображений по цветам

function changeMainImage(imageUrl) {
    mainSliderImage.src = imageUrl;
}

function changeMainImageByColor(color) {
    const images = imagesByColor[color];
    if (images && images.length > 0) {
        changeMainImage(images[0].src);
    }
}

function filterImagesByColor(color) {
    thumbnailSlides.forEach(function(slide) {
        if (slide.dataset.color === color) {
            slide.style.display = 'block'; // Отображаем только нужные изображения
        } else {
            slide.style.display = 'none'; // Скрываем остальные изображения
        }
    });
}

function createImagesByColor() {
    thumbnailSlides.forEach(function(slide) {
        const color = slide.dataset.color;
        if (!imagesByColor[color]) {
            imagesByColor[color] = [];
        }
        imagesByColor[color].push(slide.querySelector('img'));
    });
}

const urlParams = new URLSearchParams(window.location.search);
const selectedColor = urlParams.get('color');

if (selectedColor) {
    filterImagesByColor(selectedColor);
    changeMainImageByColor(selectedColor);
}

thumbnailSlides.forEach(function(thumbnail) {
    thumbnail.addEventListener('click', function() {
        const imageUrl = thumbnail.querySelector('img').src;
        changeMainImage(imageUrl);
        thumbnailSlides.forEach(thumb => thumb.classList.remove('active'));
        thumbnail.classList.add('active');

        const activeColor = thumbnail.dataset.color;
        filterImagesByColor(activeColor);
    });
});

prevButton.addEventListener('click', function() {
    const currentIndex = Array.from(thumbnailSlides).findIndex(thumbnail => thumbnail.classList.contains('active'));
    let prevIndex = (currentIndex - 1 + thumbnailSlides.length) % thumbnailSlides.length;

    while (thumbnailSlides[prevIndex].style.display === 'none') {
        prevIndex = (prevIndex - 1 + thumbnailSlides.length) % thumbnailSlides.length;
        if (prevIndex === currentIndex) return; 
    }

    const prevImageUrl = thumbnailSlides[prevIndex].querySelector('img').src;
    changeMainImage(prevImageUrl);
    thumbnailSlides[currentIndex].classList.remove('active');
    thumbnailSlides[prevIndex].classList.add('active');

    const activeColor = document.querySelector('.thumbnail-slide.active').dataset.color;
    filterImagesByColor(activeColor);
});

nextButton.addEventListener('click', function() {
    const currentIndex = Array.from(thumbnailSlides).findIndex(thumbnail => thumbnail.classList.contains('active'));
    let nextIndex = (currentIndex + 1) % thumbnailSlides.length;

    while (thumbnailSlides[nextIndex].style.display === 'none') {
        nextIndex = (nextIndex + 1) % thumbnailSlides.length;
        if (nextIndex === currentIndex) return; 
    }

    const nextImageUrl = thumbnailSlides[nextIndex].querySelector('img').src;
    changeMainImage(nextImageUrl);
    thumbnailSlides[currentIndex].classList.remove('active');
    thumbnailSlides[nextIndex].classList.add('active');

    const activeColor = document.querySelector('.thumbnail-slide.active').dataset.color;
    filterImagesByColor(activeColor);
});

document.addEventListener('DOMContentLoaded', function() {
    createImagesByColor(); 
    var colorButtons = document.querySelectorAll('.color-btn');

    colorButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var color = button.style.backgroundColor;
            filterImagesByColor(color);
            changeMainImageByColor(color); 
        });
    });
});
