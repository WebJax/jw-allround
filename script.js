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

    /**
     * Smooth scroll for anker-links
     */
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Offset for fixed header
                    behavior: 'smooth'
                });
            }
        });
    });

    /**
     * Tilføj aktivt-klasse til navigationsmenuen baseret på nuværende side
     */
    const currentPath = window.location.pathname;
    document.querySelectorAll('.wp-block-navigation a').forEach(navLink => {
        const navLinkPath = new URL(navLink.href).pathname;
        if (currentPath === navLinkPath || (currentPath.includes(navLinkPath) && navLinkPath !== '/')) {
            navLink.classList.add('current-page');
        }
    });

    /**
     * Tilføj scroll-effekt til header
     */
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    /**
     * Lazy-load effekt for billeder 
     */
    function handleLazyImages() {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.classList.add('loaded');
                        observer.unobserve(image);
                    }
                });
            });
            
            lazyImages.forEach(img => {
                imageObserver.observe(img);
                // Tilføj fade-in klasse
                img.classList.add('lazy-image');
            });
        }
    }
    
    handleLazyImages();
    
    /**
     * Fade-in animation ved scroll
     */
    function handleScrollAnimations() {
        const animatedElements = document.querySelectorAll('.service-card, .step-card, .testimonial-card');
        
        if ('IntersectionObserver' in window) {
            const elementObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                    }
                });
            }, {
                threshold: 0.1
            });
            
            animatedElements.forEach(element => {
                elementObserver.observe(element);
                // Tilføj base animation klasse
                element.classList.add('animate-on-scroll');
            });
        }
    }
    
    handleScrollAnimations();
    
    /**
     * Mobile menu toggle
     */
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            const mobileMenu = document.querySelector('.wp-block-navigation__responsive-container');
            if (mobileMenu) {
                mobileMenu.classList.toggle('is-menu-open');
                document.body.classList.toggle('mobile-menu-open');
            }
        });
    }
});
