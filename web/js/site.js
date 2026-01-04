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
    let typewriterInterval = null;
    let isUserTyping = false;

    if (heroSearchInput && heroSearchSuggestions) {
        // Typewriter effect
        const searchWord = 'Sopladora';
        let currentCharIndex = 0;
        let isDeleting = false;
        let typewriterPaused = false;
        
        function typewriterEffect() {
            if (isUserTyping || typewriterPaused) {
                return;
            }
            
            if (!isDeleting && currentCharIndex < searchWord.length) {
                // Escribiendo
                heroSearchInput.value = searchWord.substring(0, currentCharIndex + 1);
                currentCharIndex++;
                typewriterInterval = setTimeout(typewriterEffect, 100);
            } else if (isDeleting && currentCharIndex > 0) {
                // Borrando
                heroSearchInput.value = searchWord.substring(0, currentCharIndex - 1);
                currentCharIndex--;
                typewriterInterval = setTimeout(typewriterEffect, 50);
            } else if (!isDeleting && currentCharIndex === searchWord.length) {
                // Pausa después de escribir
                typewriterInterval = setTimeout(function() {
                    isDeleting = true;
                    typewriterEffect();
                }, 2000);
            } else if (isDeleting && currentCharIndex === 0) {
                // Pausa después de borrar y reiniciar
                isDeleting = false;
                typewriterInterval = setTimeout(typewriterEffect, 500);
            }
        }
        
        // Detectar cuando el usuario empieza a escribir
        heroSearchInput.addEventListener('focus', function() {
            isUserTyping = true;
            typewriterPaused = true;
            if (typewriterInterval) {
                clearTimeout(typewriterInterval);
                typewriterInterval = null;
            }
        });
        
        // Detectar cuando el usuario deja de escribir y sale del campo
        heroSearchInput.addEventListener('blur', function() {
            if (this.value === '') {
                isUserTyping = false;
                typewriterPaused = false;
                currentCharIndex = 0;
                isDeleting = false;
                this.value = '';
                // Reiniciar el efecto después de un breve delay
                setTimeout(function() {
                    if (!heroSearchInput.matches(':focus')) {
                        typewriterEffect();
                    }
                }, 1000);
            }
        });
        
        heroSearchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                isUserTyping = true;
                typewriterPaused = true;
                if (typewriterInterval) {
                    clearTimeout(typewriterInterval);
                    typewriterInterval = null;
                }
            } else {
                // Si el usuario borra todo, puede reiniciar el efecto
                isUserTyping = false;
            }
            
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
        
        // Iniciar el efecto typewriter después de un delay inicial
        setTimeout(function() {
            if (!heroSearchInput.matches(':focus') && heroSearchInput.value === '') {
                typewriterEffect();
            }
        }, 1000);

        function displaySuggestions(results) {
            if (results.length === 0) {
                heroSearchSuggestions.innerHTML = '<div class="search-suggestion-empty">No se encontraron resultados</div>';
                heroSearchSuggestions.classList.add('active');
                return;
            }

            let html = '';
            results.forEach(function(result) {
                let typeLabel = '';
                let typeIcon = '';
                
                if (result.type === 'product') {
                    typeLabel = 'Producto';
                    typeIcon = 'inventory_2';
                } else if (result.type === 'category') {
                    typeLabel = 'Categoría';
                    typeIcon = 'category';
                } else if (result.type === 'brand') {
                    typeLabel = 'Marca';
                    typeIcon = 'business';
                } else {
                    // Fallback for backward compatibility
                    typeLabel = 'Producto';
                    typeIcon = 'inventory_2';
                }
                
                html += '<a href="' + escapeHtml(result.url) + '" class="search-suggestion-item" data-type="' + (result.type || 'product') + '">';
                html += '<span class="search-suggestion-icon material-icons">' + typeIcon + '</span>';
                html += '<span class="search-suggestion-content">';
                html += '<span class="search-suggestion-name">' + escapeHtml(result.name) + '</span>';
                if (result.code) {
                    html += '<span class="search-suggestion-code">Código: ' + escapeHtml(result.code) + '</span>';
                }
                html += '<span class="search-suggestion-type">' + typeLabel + '</span>';
                html += '</span>';
                if (result.price) {
                    html += '<span class="search-suggestion-price">' + escapeHtml(result.price) + '</span>';
                }
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

