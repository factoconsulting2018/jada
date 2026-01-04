document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('header-search-input');
    const suggestions = document.getElementById('header-search-suggestions');
    let searchTimeout;
    let currentSearchRequest = null;
    let typewriterInterval = null;
    let isUserTyping = false;
    let currentFocus = -1;

    if (!searchInput || !suggestions) return;

    // Typewriter effect
    const searchWord = 'Sopladora'; // The word to be typed
    let currentCharIndex = 0;
    let isDeleting = false;
    let typewriterPaused = false;
    
    function typewriterEffect() {
        if (isUserTyping || typewriterPaused) {
            return;
        }
        
        if (!isDeleting && currentCharIndex < searchWord.length) {
            // Typing
            searchInput.value = searchWord.substring(0, currentCharIndex + 1);
            currentCharIndex++;
            typewriterInterval = setTimeout(typewriterEffect, 100);
        } else if (isDeleting && currentCharIndex > 0) {
            // Deleting
            searchInput.value = searchWord.substring(0, currentCharIndex - 1);
            currentCharIndex--;
            typewriterInterval = setTimeout(typewriterEffect, 50);
        } else if (!isDeleting && currentCharIndex === searchWord.length) {
            // Pause after typing
            typewriterInterval = setTimeout(function() {
                isDeleting = true;
                typewriterEffect();
            }, 2000);
        } else if (isDeleting && currentCharIndex === 0) {
            // Pause after deleting and restart
            isDeleting = false;
            typewriterInterval = setTimeout(typewriterEffect, 500);
        }
    }
    
    // Detect when the user starts typing
    searchInput.addEventListener('focus', function() {
        isUserTyping = true;
        typewriterPaused = true;
        if (typewriterInterval) {
            clearTimeout(typewriterInterval);
            typewriterInterval = null;
        }
    });
    
    // Detect when the user stops typing and leaves the field
    searchInput.addEventListener('blur', function() {
        if (this.value === '') {
            isUserTyping = false;
            typewriterPaused = false;
            currentCharIndex = 0;
            isDeleting = false;
            this.value = '';
            // Restart the effect after a brief delay
            setTimeout(function() {
                if (!searchInput.matches(':focus')) {
                    typewriterEffect();
                }
            }, 1000);
        }
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !suggestions.contains(event.target)) {
            suggestions.classList.remove('active');
            suggestions.style.display = 'none';
            currentFocus = -1;
        }
    });

    searchInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            isUserTyping = true;
            typewriterPaused = true;
            if (typewriterInterval) {
                clearTimeout(typewriterInterval);
                typewriterInterval = null;
            }
        } else {
            // If the user clears everything, the effect can restart
            isUserTyping = false;
        }

        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        // Cancel previous request if still pending
        if (currentSearchRequest) {
            currentSearchRequest.abort();
        }

        if (query.length < 2) {
            suggestions.innerHTML = '';
            suggestions.classList.remove('active');
            suggestions.style.display = 'none';
            return;
        }

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
                            suggestions.innerHTML = '';
                            suggestions.classList.remove('active');
                            suggestions.style.display = 'none';
                        }
                    }
                }
            };
            xhr.send();
        }, 300);
    });

    searchInput.addEventListener('keydown', function(e) {
        const items = suggestions.querySelectorAll('.header-search-suggestion-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentFocus++;
            setActive(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentFocus--;
            setActive(items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentFocus > -1 && items[currentFocus]) {
                items[currentFocus].click();
            } else if (items.length > 0) {
                items[0].click();
            }
        } else if (e.key === 'Escape') {
            suggestions.classList.remove('active');
            suggestions.style.display = 'none';
            this.blur();
        }
    });

    function setActive(items) {
        if (!items.length) return;
        removeActive(items);
        if (currentFocus >= items.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = items.length - 1;
        items[currentFocus].classList.add('active');
    }

    function removeActive(items) {
        items.forEach(item => item.classList.remove('active'));
    }

    function displaySuggestions(results) {
        if (!results || results.length === 0) {
            suggestions.innerHTML = '<div class="header-search-suggestion-empty">No se encontraron resultados</div>';
            suggestions.classList.add('active');
            suggestions.style.display = 'block';
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
            
            html += '<a href="' + escapeHtml(result.url) + '" class="header-search-suggestion-item" data-type="' + (result.type || 'product') + '">';
            html += '<span class="header-search-suggestion-icon material-icons">' + typeIcon + '</span>';
            html += '<span class="header-search-suggestion-content">';
            html += '<span class="header-search-suggestion-name">' + escapeHtml(result.name) + '</span>';
            if (result.code) {
                html += '<span class="header-search-suggestion-code">Código: ' + escapeHtml(result.code) + '</span>';
            }
            html += '<span class="header-search-suggestion-type">' + typeLabel + '</span>';
            html += '</span>';
            if (result.price) {
                html += '<span class="header-search-suggestion-price">' + escapeHtml(result.price) + '</span>';
            }
            html += '</a>';
        });

        suggestions.innerHTML = html;
        suggestions.classList.add('active');
        suggestions.style.display = 'block';
        currentFocus = -1;

        // Add click handlers
        suggestions.querySelectorAll('.header-search-suggestion-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
        });
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

    // Start the typewriter effect after an initial delay
    setTimeout(function() {
        if (!searchInput.matches(':focus') && searchInput.value === '') {
            typewriterEffect();
        }
    }, 1000);
});
