/**
 * CartHive - Shopping Cart JavaScript
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
            // Update cart count badge
            updateCartCount(data.cart_count);

            // Show success notification
            showNotification(data.message, 'success');

            // Open cart sidebar
            openCart();

            // Reload cart items
            loadCartItems();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Failed to add to cart', 'error');
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
            updateCartCount(data.cart_count);
            showNotification(data.message, 'success');
            loadCartItems();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showNotification('Failed to remove from cart', 'error');
    }
}

// Update cart quantity
async function updateCartQuantity(cartId, quantity) {
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
            updateCartCount(data.cart_count);
            loadCartItems();
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showNotification('Failed to update cart', 'error');
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

// Update cart count badge
function updateCartCount(count) {
    const badges = document.querySelectorAll('.navbar-nav .badge.bg-danger');
    badges.forEach(badge => {
        const parent = badge.parentElement;
        if (parent && parent.title === 'Cart') {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        }
    });
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

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.textContent = message;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count
    loadCartItems();
});
