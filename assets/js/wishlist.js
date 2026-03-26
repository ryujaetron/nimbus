/**
 * Wishlist JavaScript Functions
 */

// Toggle wishlist status
async function toggleWishlist(productId, button) {
    const isInWishlist = button.classList.contains('in-wishlist');
    const action = isInWishlist ? 'remove' : 'add';

    const formData = new FormData();
    formData.append('action', action);
    formData.append('product_id', productId);

    try {
        const response = await fetch('../handlers/wishlist_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Update button state
            updateWishlistButton(button, !isInWishlist);

            // Update wishlist count in navbar
            updateWishlistCount(isInWishlist ? -1 : 1);

            // Show notification
            showWishlistNotification(data.message, 'success');
        } else {
            showWishlistNotification(data.message || 'Failed to update wishlist', 'error');
        }
    } catch (error) {
        console.error('Error toggling wishlist:', error);
        showWishlistNotification('Failed to update wishlist', 'error');
    }
}

// Update wishlist button appearance
function updateWishlistButton(button, isInWishlist) {
    const icon = button.querySelector('i');

    if (isInWishlist) {
        button.classList.add('in-wishlist');
        icon.className = 'bi bi-heart-fill text-danger';
        button.title = 'Remove from wishlist';
    } else {
        button.classList.remove('in-wishlist');
        icon.className = 'bi bi-heart text-danger';
        button.title = 'Add to wishlist';
    }
}

// Update wishlist count in navbar
function updateWishlistCount(change) {
    const wishlistLink = document.querySelector('a[href="wishlist.php"]');
    if (!wishlistLink) return;

    let wishlistBadge = wishlistLink.querySelector('.badge');

    if (wishlistBadge) {
        const currentCount = parseInt(wishlistBadge.textContent) || 0;
        const newCount = Math.max(0, currentCount + change);

        if (newCount > 0) {
            wishlistBadge.textContent = newCount;
            wishlistBadge.style.display = 'inline';
        } else {
            wishlistBadge.remove();
        }
    } else if (change > 0) {
        // Create badge if it doesn't exist
        const badge = document.createElement('span');
        badge.className = 'badge bg-danger';
        badge.textContent = '1';
        wishlistLink.appendChild(badge);
    }
}

// Check if products are in wishlist and update buttons
async function checkWishlistStatus(productIds) {
    if (!productIds.length) return;

    for (const productId of productIds) {
        try {
            const formData = new FormData();
            formData.append('action', 'check');
            formData.append('product_id', productId);

            const response = await fetch('../handlers/wishlist_handler.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.in_wishlist) {
                const button = document.querySelector(`.wishlist-btn[onclick*="toggleWishlist(${productId}"]`);
                if (button) {
                    updateWishlistButton(button, true);
                }
            }
        } catch (error) {
            console.error('Error checking wishlist status:', error);
        }
    }
}

// Show wishlist notification
function showWishlistNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.wishlist-notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} wishlist-notification position-fixed`;
    notification.style.cssText = 'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 280px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    notification.innerHTML = `<i class="bi bi-${type === 'error' ? 'x-circle' : 'heart-fill'}"></i> ${message}`;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 2500);
}

// Initialize wishlist functionality on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get all wishlist buttons on the page
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    const productIds = [];

    wishlistButtons.forEach(btn => {
        const onclick = btn.getAttribute('onclick');
        if (onclick) {
            const match = onclick.match(/toggleWishlist\((\d+)/);
            if (match) {
                productIds.push(parseInt(match[1]));
            }
        }
    });

    // Check wishlist status for all products
    if (productIds.length > 0) {
        checkWishlistStatus(productIds);
    }
});
