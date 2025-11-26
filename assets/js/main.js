// ==================== Pharmacy Management System - Main JavaScript ====================

// ==================== Theme Toggle ====================
function toggleTheme() {
    const html = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    const currentTheme = html.getAttribute('data-theme');

    if (currentTheme === 'dark') {
        html.setAttribute('data-theme', 'light');
        if (themeIcon) {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        }
        localStorage.setItem('theme', 'light');
    } else {
        html.setAttribute('data-theme', 'dark');
        if (themeIcon) {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        }
        localStorage.setItem('theme', 'dark');
    }
}

// ==================== Load Saved Theme ====================
function loadTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = document.getElementById('themeIcon');

    document.documentElement.setAttribute('data-theme', savedTheme);

    if (themeIcon) {
        if (savedTheme === 'dark') {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        }
    }
}

// ==================== Sidebar Toggle ====================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    if (sidebar) sidebar.classList.toggle('collapsed');
    if (mainContent) mainContent.classList.toggle('expanded');

    // On mobile, toggle show class
    if (window.innerWidth <= 768 && sidebar) {
        sidebar.classList.toggle('show');
    }
}

// ==================== Profile Dropdown Toggle ====================
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
}

// ==================== Module Navigation (Multi-page Support) ====================
// This replaces the old SPA logic. It runs on every page load.
document.addEventListener('DOMContentLoaded', function () {
    // 1. Activate the module content present on this page
    const moduleContent = document.querySelector('.module-content');
    if (moduleContent) {
        moduleContent.classList.add('active');
    }

    // 2. Highlight the active sidebar link based on current URL
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-item');

    sidebarLinks.forEach(link => {
        // Remove active class from all first
        link.classList.remove('active');

        // Get the href attribute (e.g., "./dashboard.php")
        const href = link.getAttribute('href');

        // Check if the current path ends with the link's filename
        if (href && href !== '#') {
            // Remove ./ and .php to match loosely if needed, or match exact filename
            const cleanHref = href.replace('./', '');
            if (currentPath.includes(cleanHref)) {
                link.classList.add('active');
            }
        }
    });

    // 3. Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 4. Load Theme
    loadTheme();

    // 5. Initialize Speech Toggle Icon
    const speechIcon = document.getElementById('speechToggleIcon');
    if (speechIcon) {
        if (notificationSpeechEnabled) {
            speechIcon.classList.remove('bi-volume-mute-fill');
            speechIcon.classList.add('bi-volume-up-fill');
        } else {
            speechIcon.classList.remove('bi-volume-up-fill');
            speechIcon.classList.add('bi-volume-mute-fill');
        }
    }

    // 6. Handle Resize
    handleResize();

    // 7. Initialize Money Formatting
    initMoneyFormatting();
});

// Legacy function kept for compatibility if onclicks exist
function showModule(moduleId, element) {
    // No-op for multi-page setup
}

// ==================== Close Dropdown When Clicking Outside ====================
document.addEventListener('click', (e) => {
    const profileDropdown = document.getElementById('profileDropdown');
    const avatar = document.querySelector('.avatar');

    if (profileDropdown && avatar) {
        if (!profileDropdown.contains(e.target) && !avatar.contains(e.target)) {
            profileDropdown.style.display = 'none';
        }
    }
});

// ==================== Responsive Sidebar ====================
function handleResize() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    if (sidebar && mainContent) {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
        }
    }
}

window.addEventListener('resize', handleResize);

// ==================== Close Sidebar on Mobile When Clicking Outside ====================
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('[onclick="toggleSidebar()"]');

        if (sidebar && toggleBtn) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                if (!sidebar.classList.contains('collapsed')) {
                    // Sidebar is open, close it
                    sidebar.style.transform = 'translateX(-100%)';
                }
            }
        }
    }
});

// ==================== Smooth Scroll ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();
            document.querySelector(href).scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// ==================== Form Validation Feedback ====================
// Removed global interceptor to allow PHP form submissions
// const forms = document.querySelectorAll('form');
// forms.forEach(form => {
//     form.addEventListener('submit', (e) => {
//         e.preventDefault();
//         // Show success notification
//         showNotification('Success!', 'Changes saved successfully.', 'success');
//     });
// });

// ==================== Notification System ====================
// ==================== Text-to-Speech for Notifications ====================
let notificationSpeechEnabled = localStorage.getItem('notificationSpeech') !== 'false'; // Enabled by default

function toggleNotificationSpeech() {
    notificationSpeechEnabled = !notificationSpeechEnabled;
    localStorage.setItem('notificationSpeech', notificationSpeechEnabled);

    const icon = document.getElementById('speechToggleIcon');
    if (icon) {
        if (notificationSpeechEnabled) {
            icon.classList.remove('bi-volume-mute-fill');
            icon.classList.add('bi-volume-up-fill');
        } else {
            icon.classList.remove('bi-volume-up-fill');
            icon.classList.add('bi-volume-mute-fill');
        }
    }

    showNotification(
        notificationSpeechEnabled ? 'Speech Enabled' : 'Speech Disabled',
        notificationSpeechEnabled ? 'Notifications will be spoken' : 'Notifications will be silent',
        'info'
    );
}

function showNotification(title, message, type) {
    const notification = document.createElement('div');
    notification.className = 'notification';

    let icon = 'bi-check-circle-fill';
    let color = '#10b981';

    if (type === 'error' || type === 'danger') {
        icon = 'bi-x-circle-fill';
        color = '#ef4444';
    } else if (type === 'warning') {
        icon = 'bi-exclamation-triangle-fill';
        color = '#f59e0b';
    } else if (type === 'info') {
        icon = 'bi-info-circle-fill';
        color = '#06b6d4';
    }

    notification.innerHTML = `
        <div class="d-flex align-items-center gap-3">
            <i class="${icon}" style="color: ${color}; font-size: 24px;"></i>
            <div>
                <strong>${title}</strong>
                <p class="mb-0 text-muted">${message}</p>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    // Speak the notification
    speakNotification(type, title, message);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function speakNotification(type, title, message) {
    if (!notificationSpeechEnabled) return;

    if ('speechSynthesis' in window) {
        // Cancel any current speech
        window.speechSynthesis.cancel();

        const text = `${title}. ${message}`;
        const utterance = new SpeechSynthesisUtterance(text);

        // Select a voice if available (optional)
        // const voices = window.speechSynthesis.getVoices();
        // utterance.voice = voices[0]; 

        window.speechSynthesis.speak(utterance);
    }
}

// ==================== Console Welcome Message ====================
console.log('%c🏥 HealthPlus Pharmacy Management System', 'color: #0A7EBA; font-size: 20px; font-weight: bold;');
console.log('%cBuilt with HTML5, CSS3, and Bootstrap 5', 'color: #06B6A8; font-size: 14px;');
console.log('%c© 2025 All Rights Reserved', 'color: #64748b; font-size: 12px;');

// ==================== POS/Cart Functionality ====================

// Cart array to store items
let cart = [];

// Add item to cart
function addToCart(productName, price, stock) {
    const existingItem = cart.find(item => item.name === productName);

    if (existingItem) {
        if (existingItem.quantity < stock) {
            existingItem.quantity++;
            updateCart();
            showNotification('Added to Cart', `${productName} quantity increased`, 'success');
        } else {
            showNotification('Stock Limit', `Only ${stock} units available`, 'warning');
        }
    } else {
        cart.push({
            name: productName,
            price: price,
            quantity: 1,
            stock: stock
        });
        updateCart();
        showNotification('Added to Cart', `${productName} added to cart`, 'success');
    }
}

// Remove item from cart
function removeFromCart(productName) {
    cart = cart.filter(item => item.name !== productName);
    updateCart();
    showNotification('Removed', `${productName} removed from cart`, 'info');
}

// Update cart quantity
function updateCartQuantity(productName, newQuantity) {
    const item = cart.find(item => item.name === productName);

    if (item) {
        if (newQuantity <= 0) {
            removeFromCart(productName);
        } else if (newQuantity <= item.stock) {
            item.quantity = newQuantity;
            updateCart();
        } else {
            showNotification('Stock Limit', `Only ${item.stock} units available`, 'warning');
        }
    }
}

// Calculate cart totals
function calculateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discountPercent = parseFloat(document.getElementById('discountInput')?.value || 0);
    const discount = (subtotal * discountPercent) / 100;
    const total = subtotal - discount;

    return { subtotal, discount, total };
}

// Update cart display
function updateCart() {
    const cartContainer = document.getElementById('cartItems');

    if (!cartContainer) return;

    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="text-muted text-center">Cart is empty</p>';
    } else {
        cartContainer.innerHTML = cart.map(item => `
            <div class="p-3 mb-2" style="background: rgba(10, 126, 186, 0.05); border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <p class="mb-1" style="font-weight: 600;">${item.name}</p>
                        <small class="text-muted">Qty: ${item.quantity} × UGX ${item.price.toLocaleString()}</small>
                    </div>
                    <button class="btn btn-link text-danger p-0" onclick="removeFromCart('${item.name}')">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <strong style="color: var(--primary);">UGX ${(item.price * item.quantity).toLocaleString()}</strong>
            </div>
        `).join('');
    }

    // Update totals
    const { subtotal, discount, total } = calculateTotals();

    const subtotalEl = document.getElementById('subtotalAmount');
    const discountEl = document.getElementById('discountAmount');
    const totalEl = document.getElementById('totalAmount');

    if (subtotalEl) subtotalEl.textContent = `UGX ${subtotal.toLocaleString()}`;
    if (discountEl) discountEl.textContent = `UGX ${discount.toLocaleString()}`;
    if (totalEl) totalEl.textContent = `UGX ${total.toLocaleString()}`;
}

// Clear cart
function clearCart() {
    cart = [];
    updateCart();
    showNotification('Cart Cleared', 'All items removed from cart', 'info');
}

// Checkout
function checkout() {
    if (cart.length === 0) {
        showNotification('Empty Cart', 'Please add items to cart before checkout', 'warning');
        return;
    }

    const { total } = calculateTotals();
    const paymentMethod = document.getElementById('paymentMethod')?.value || 'Cash';

    // Use absolute path for fetch
    const basePath = '/pharmacy_ms';
    fetch(`${basePath}/actions/sales.php?action=create`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items: cart, payment_method: paymentMethod, total })
    })
        .then(async r => {
            const text = await r.text();
            console.log('Response status:', r.status);
            console.log('Response body:', text);

            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was:', text);
                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
            }
        })
        .then(res => {
            if (res && res.ok && res.sale_id) {
                showNotification('Sale Complete', 'Opening receipt...', 'success');

                // Open receipt in new tab immediately
                window.open(`${basePath}/receipt?sale_id=${res.sale_id}`, '_blank');

                // Clear cart after a short delay to allow the UI to update
                setTimeout(() => {
                    clearCart();
                }, 1000);
            } else {
                const errorMsg = res && res.error ? res.error : 'Please try again';
                showNotification('Checkout Failed', errorMsg, 'danger');
            }
        })
        .catch((err) => {
            console.error('Checkout error:', err);
            showNotification('Network Error', err.message || 'Unable to process checkout', 'danger');
        });
}

// ==================== Search Functionality ====================
function searchProducts(searchTerm) {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        const productName = card.querySelector('p')?.textContent.toLowerCase() || '';

        if (productName.includes(searchTerm.toLowerCase())) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Generic search function for tables
function searchTable(searchTerm, tableSelector) {
    const table = document.querySelector(tableSelector);
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    return visibleCount;
}

// Generic search function for card grids
function searchCards(searchTerm, cardSelector) {
    const cards = document.querySelectorAll(cardSelector);
    let visibleCount = 0;

    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    return visibleCount;
}

// Generic filter function for tables by column
function filterTableByColumn(filterValue, tableSelector, columnIndex) {
    if (!filterValue || filterValue.toLowerCase().includes('all')) {
        // Show all rows if "All" is selected
        const table = document.querySelector(tableSelector);
        if (table) {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => row.style.display = '');
        }
        return;
    }

    const table = document.querySelector(tableSelector);
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells[columnIndex]) {
            const cellText = cells[columnIndex].textContent.toLowerCase();
            if (cellText.includes(filterValue.toLowerCase())) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }
    });

    return visibleCount;
}

// Generic filter function for cards
function filterCards(filterValue, cardSelector) {
    const cards = document.querySelectorAll(cardSelector);

    if (!filterValue || filterValue.toLowerCase().includes('all')) {
        // Show all cards if "All" is selected
        cards.forEach(card => card.style.display = '');
        return;
    }

    let visibleCount = 0;

    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(filterValue.toLowerCase())) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    return visibleCount;
}

// ==================== Money Input Formatting ====================
// Format number with commas (e.g., 1000000 -> 1,000,000)
function formatMoney(value) {
    // Remove all non-digit characters except decimal point
    let cleanValue = value.replace(/[^\d.]/g, '');

    // Prevent multiple decimal points
    const decimalCount = (cleanValue.match(/\./g) || []).length;
    if (decimalCount > 1) {
        const parts = cleanValue.split('.');
        cleanValue = parts[0] + '.' + parts.slice(1).join('');
    }

    // Split into integer and decimal parts
    const parts = cleanValue.split('.');
    let integerPart = parts[0];
    const decimalPart = parts[1];

    // Add commas to integer part
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // Reconstruct the number
    return decimalPart !== undefined ? `${integerPart}.${decimalPart}` : integerPart;
}

// Remove commas from formatted money (for form submission)
function unformatMoney(value) {
    return value.replace(/,/g, '');
}

// Apply money formatting to an input field
function applyMoneyFormatting(input) {
    // Change input type to text to allow commas
    input.setAttribute('type', 'text');
    input.setAttribute('inputmode', 'decimal');
    input.setAttribute('pattern', '[0-9,]*\\.?[0-9]*');

    input.addEventListener('input', function (e) {
        const cursorPosition = this.selectionStart;
        const oldValue = this.value;
        const oldLength = oldValue.length;

        // Format the value
        const formatted = formatMoney(this.value);
        this.value = formatted;

        // Adjust cursor position after formatting
        const newLength = formatted.length;
        const diff = newLength - oldLength;
        const newCursorPos = cursorPosition + diff;
        this.setSelectionRange(newCursorPos, newCursorPos);
    });

    // Remove commas before form submission
    const form = input.closest('form');
    if (form) {
        // Remove existing listener if any
        form.removeEventListener('submit', form._moneyFormatHandler);

        // Create new handler
        form._moneyFormatHandler = function (e) {
            // Find all money inputs in this form
            const moneyInputs = form.querySelectorAll(
                'input[name*="amount"], input[name*="price"], input[name*="total"], ' +
                'input[name*="cost"], input[name*="salary"], input[name*="payment"], ' +
                '.money-input, .currency-input'
            );

            moneyInputs.forEach(inp => {
                if (inp.value) {
                    inp.value = unformatMoney(inp.value);
                }
            });
        };

        form.addEventListener('submit', form._moneyFormatHandler);
    }
}

// Initialize money formatting on all money input fields
function initMoneyFormatting() {
    // Find all inputs with money-related names or classes
    const moneyInputs = document.querySelectorAll(
        'input[name*="amount"], input[name*="price"], input[name*="total"], ' +
        'input[name*="cost"], input[name*="salary"], input[name*="payment"], ' +
        '.money-input, .currency-input'
    );

    moneyInputs.forEach(input => {
        // Skip inputs from selling-units forms (they need to stay as number inputs)
        const form = input.closest('form');
        if (form && (
            form.id === 'assignUnitForm' ||
            form.id === 'editMedicineUnitForm' ||
            form.id === 'addUnitForm' ||
            form.id === 'editUnitForm'
        )) {
            return; // Skip this input
        }

        applyMoneyFormatting(input);
    });
}

// ==================== Export Functions for Global Use ====================
window.toggleTheme = toggleTheme;
window.toggleSidebar = toggleSidebar;
window.showModule = showModule;
window.showNotification = showNotification;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.clearCart = clearCart;
window.checkout = checkout;
window.searchProducts = searchProducts;
window.searchTable = searchTable;
window.searchCards = searchCards;
window.filterTableByColumn = filterTableByColumn;
window.filterCards = filterCards;
window.formatMoney = formatMoney;
window.unformatMoney = unformatMoney;
