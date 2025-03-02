document.addEventListener("DOMContentLoaded", function () {
    // Mobile menu toggle
    const menuToggle = document.querySelector(".menu-toggle");
    const menu = document.querySelector(".menu");

    if (menuToggle && menu) {
        menuToggle.addEventListener("click", function () {
            menu.classList.toggle("active");
        });
    }

    // Simple carousel/slider
    const sliders = document.querySelectorAll(".carousel");
    sliders.forEach(slider => {
        let currentIndex = 0;
        const slides = slider.querySelectorAll(".carousel-slide");
        const totalSlides = slides.length;
        const nextButton = slider.querySelector(".carousel-next");
        const prevButton = slider.querySelector(".carousel-prev");

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.display = i === index ? "block" : "none";
            });
        }

        if (nextButton) {
            nextButton.addEventListener("click", function () {
                currentIndex = (currentIndex + 1) % totalSlides;
                showSlide(currentIndex);
            });
        }

        if (prevButton) {
            prevButton.addEventListener("click", function () {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                showSlide(currentIndex);
            });
        }

        showSlide(currentIndex);
    });
});
