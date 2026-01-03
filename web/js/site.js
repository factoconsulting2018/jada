// Initialize Material Design Components
document.addEventListener('DOMContentLoaded', function() {
    // Initialize MDC components if available
    if (typeof mdc !== 'undefined') {
        // Initialize buttons
        const buttons = document.querySelectorAll('.mdc-button');
        buttons.forEach(button => {
            mdc.ripple.MDCRipple.attachTo(button);
        });

        // Initialize cards
        const cards = document.querySelectorAll('.mdc-card');
        cards.forEach(card => {
            mdc.ripple.MDCRipple.attachTo(card);
        });
    }

    // Mobile menu toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Hero banner slider
    const heroSlides = document.querySelectorAll('.hero-slide');
    if (heroSlides.length > 0) {
        let currentSlide = 0;
        
        function showSlide(index) {
            heroSlides.forEach(slide => slide.classList.remove('active'));
            if (heroSlides[index]) {
                heroSlides[index].classList.add('active');
            }
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % heroSlides.length;
            showSlide(currentSlide);
        }

        // Auto-play slider
        if (heroSlides.length > 1) {
            setInterval(nextSlide, 5000);
        }
        
        showSlide(0);
    }

    // Product image gallery
    const productThumbnails = document.querySelectorAll('.product-thumbnail');
    const productMainImage = document.querySelector('.product-main-image');
    
    if (productThumbnails.length > 0 && productMainImage) {
        productThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                productThumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                productMainImage.src = this.src.replace('thumbnail', 'full') || this.src;
            });
        });
    }

    // Hero search autocomplete
    const heroSearchInput = document.getElementById('hero-search-input');
    const heroSearchSuggestions = document.getElementById('hero-search-suggestions');
    let searchTimeout = null;
    let currentSearchRequest = null;

    if (heroSearchInput && heroSearchSuggestions) {
        heroSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Cancel previous request if still pending
            if (currentSearchRequest) {
                currentSearchRequest.abort();
            }

            if (query.length < 2) {
                heroSearchSuggestions.innerHTML = '';
                heroSearchSuggestions.classList.remove('active');
                return;
            }

            // Debounce search
            searchTimeout = setTimeout(function() {
                // Create new request
                const xhr = new XMLHttpRequest();
                currentSearchRequest = xhr;

                xhr.open('GET', '/site/search?q=' + encodeURIComponent(query), true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        currentSearchRequest = null;
                        if (xhr.status === 200) {
                            try {
                                const results = JSON.parse(xhr.responseText);
                                displaySuggestions(results);
                            } catch (e) {
                                console.error('Error parsing search results:', e);
                                heroSearchSuggestions.innerHTML = '';
                                heroSearchSuggestions.classList.remove('active');
                            }
                        }
                    }
                };
                xhr.send();
            }, 300);
        });

        function displaySuggestions(results) {
            if (results.length === 0) {
                heroSearchSuggestions.innerHTML = '<div class="search-suggestion-empty">No se encontraron productos</div>';
                heroSearchSuggestions.classList.add('active');
                return;
            }

            let html = '';
            results.forEach(function(product) {
                html += '<a href="' + product.url + '" class="search-suggestion-item">';
                html += '<span class="search-suggestion-name">' + escapeHtml(product.name) + '</span>';
                html += '<span class="search-suggestion-price">' + escapeHtml(product.price) + '</span>';
                html += '</a>';
            });
            
            heroSearchSuggestions.innerHTML = html;
            heroSearchSuggestions.classList.add('active');
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(event) {
            if (!heroSearchInput.contains(event.target) && !heroSearchSuggestions.contains(event.target)) {
                heroSearchSuggestions.classList.remove('active');
            }
        });

        // Handle keyboard navigation
        heroSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                heroSearchSuggestions.classList.remove('active');
                this.blur();
            }
        });
    }
});

