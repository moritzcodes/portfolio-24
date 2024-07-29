document.addEventListener("DOMContentLoaded", function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        if (!img.classList.contains('lazyload') && !img.classList.contains('eager')) {
            img.setAttribute('data-src', img.src);
            img.removeAttribute('src');
            img.classList.add('lazyload');
        }
    });
});