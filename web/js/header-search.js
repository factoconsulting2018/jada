document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('header-search-input');
    const suggestions = document.getElementById('header-search-suggestions');
    let searchTimeout;
    let currentFocus = -1;

    if (!searchInput || !suggestions) return;

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !suggestions.contains(event.target)) {
            suggestions.style.display = 'none';
            currentFocus = -1;
        }
    });

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            suggestions.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(function() {
            fetch('/site/search?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    displaySuggestions(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    suggestions.style.display = 'none';
                });
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
            suggestions.innerHTML = '<div class="header-search-suggestion-item">No se encontraron productos</div>';
            suggestions.style.display = 'block';
            return;
        }

        let html = '';
        results.forEach(function(product) {
            html += `
                <a href="${escapeHtml(product.url)}" class="header-search-suggestion-item">
                    <span class="suggestion-name">${escapeHtml(product.name)}</span>
                    <span class="suggestion-price">${escapeHtml(product.price)}</span>
                </a>
            `;
        });

        suggestions.innerHTML = html;
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
});

