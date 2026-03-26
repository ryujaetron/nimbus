/**
 * CartHive - Shopping Cart JavaScript
 * Enhanced with better notifications and badge updates
 */

// Add product to cart
async function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('../handlers/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update cart count badge with animation
            updateCartBadge(data.cart_count);

            // Show success notification
            showCartNotification(data.message, 'success', 'cart-plus');

            // Open cart sidebar
            openCart();

            // Reload cart items
            loadCartItems();
        } else {
            showCartNotification(data.message || 'Failed to add to cart', 'error', 'x-circle');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showCartNotification('Failed to add to cart', 'error', 'x-circle');
    }
}

// Remove product from cart
async function removeFromCart(cartId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('cart_id', cartId);

    try {
        const response = await fetch('../handlers/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            updateCartBadge(data.cart_count);
            showCartNotification(data.message, 'success', 'trash');
            loadCartItems();
        } else {
            showCartNotification(data.message || 'Failed to remove item', 'error', 'x-circle');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showCartNotification('Failed to remove from cart', 'error', 'x-circle');
    }
}

// Update cart quantity
async function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) {
        removeFromCart(cartId);
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);

    try {
        const response = await fetch('../handlers/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            updateCartBadge(data.cart_count);
            loadCartItems();
        } else {
            showCartNotification(data.message || 'Failed to update quantity', 'error', 'x-circle');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showCartNotification('Failed to update cart', 'error', 'x-circle');
    }
}

// Load cart items
async function loadCartItems() {
    const formData = new FormData();
    formData.append('action', 'get');

    try {
        const response = await fetch('../handlers/cart_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            renderCartItems(data.items, data.total);
            // Also update badge from server data
            if (data.cart_count !== undefined) {
                updateCartBadge(data.cart_count, false);
            }
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

// Render cart items in sidebar
function renderCartItems(items, total) {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotalElement = document.getElementById('cartTotal');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    const cartFooter = document.getElementById('cartFooter');

    if (!cartItemsContainer) return;

    if (items.length === 0) {
        cartItemsContainer.innerHTML = '';
        emptyCartMessage.classList.remove('d-none');
        cartFooter.classList.add('d-none');
    } else {
        emptyCartMessage.classList.add('d-none');
        cartFooter.classList.remove('d-none');

        let html = '';
        items.forEach(item => {
            const imageSrc = item.image ? `../uploads/products/${item.image}` : '../assets/images/placeholder.png';
            html += `
                <div class="cart-item mb-3 pb-3 border-bottom">
                    <div class="d-flex gap-3">
                        <img src="${imageSrc}" alt="${item.name}" class="cart-item-img">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.name}</h6>
                            <p class="text-success mb-2">₱${Number(item.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.cart_id}, ${item.quantity - 1})">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="px-2">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.cart_id}, ${item.quantity + 1})" ${item.quantity >= item.stock ? 'disabled' : ''}>
                                    <i class="bi bi-plus"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger ms-auto" onclick="removeFromCart(${item.cart_id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = html;
        cartTotalElement.textContent = '₱' + Number(total).toLocaleString('en-PH', {minimumFractionDigits: 2});
    }
}

// Update cart badge with animation
function updateCartBadge(count, animate = true) {
    // Find cart link by looking for the cart icon or onclick handler
    const cartLink = document.querySelector('a[onclick*="openCart"]') ||
                     document.querySelector('a[title="Cart"]');

    if (!cartLink) return;

    let badge = cartLink.querySelector('.badge');

    if (count > 0) {
        if (!badge) {
            // Create badge if it doesn't exist
            badge = document.createElement('span');
            badge.className = 'badge bg-danger';
            cartLink.appendChild(badge);
        }

        badge.textContent = count;
        badge.style.display = 'inline';

        // Add bounce animation
        if (animate) {
            badge.style.transform = 'scale(1.3)';
            badge.style.transition = 'transform 0.2s ease';
            setTimeout(() => {
                badge.style.transform = 'scale(1)';
            }, 200);
        }
    } else {
        if (badge) {
            badge.style.display = 'none';
        }
    }
}

// Open cart sidebar
function openCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartOverlay');

    if (cartSidebar) {
        cartSidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        loadCartItems();
    }
}

// Close cart sidebar
function closeCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartOverlay');

    if (cartSidebar) {
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Enhanced cart notification with icons
function showCartNotification(message, type = 'info', icon = 'info-circle') {
    // Remove existing cart notifications
    const existing = document.querySelectorAll('.cart-notification');
    existing.forEach(el => el.remove());

    // Determine colors and icons
    let bgColor, iconClass;
    switch(type) {
        case 'success':
            bgColor = 'bg-success';
            iconClass = `bi-${icon}`;
            break;
        case 'error':
            bgColor = 'bg-danger';
            iconClass = 'bi-x-circle';
            break;
        default:
            bgColor = 'bg-info';
            iconClass = 'bi-info-circle';
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `cart-notification position-fixed d-flex align-items-center gap-2 text-white px-4 py-3 rounded-3 shadow-lg ${bgColor}`;
    notification.style.cssText = `
        top: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(-100px);
        z-index: 10000;
        min-width: 280px;
        max-width: 400px;
        opacity: 0;
        transition: all 0.3s ease;
    `;

    notification.innerHTML = `
        <i class="bi ${iconClass}" style="font-size: 1.25rem;"></i>
        <span style="flex: 1;">${message}</span>
        <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.75rem;" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(notification);

    // Animate in
    requestAnimationFrame(() => {
        notification.style.transform = 'translateX(-50%) translateY(0)';
        notification.style.opacity = '1';
    });

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(-50%) translateY(-20px)';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Legacy notification function (for compatibility)
function showNotification(message, type = 'info') {
    showCartNotification(message, type);
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count
    loadCartItems();
});
