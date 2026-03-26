/**
 * Wishlist JavaScript Functions
 * Enhanced with better notifications and badge updates
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
            updateWishlistBadge(isInWishlist ? -1 : 1);

            // Show notification
            const icon = isInWishlist ? 'heart' : 'heart-fill';
            showWishlistNotification(data.message, 'success', icon);
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

// Update wishlist badge with animation
function updateWishlistBadge(change, animate = true) {
    const wishlistLink = document.querySelector('a[href="wishlist.php"]');
    if (!wishlistLink) return;

    let badge = wishlistLink.querySelector('.badge');

    if (badge) {
        const currentCount = parseInt(badge.textContent) || 0;
        const newCount = Math.max(0, currentCount + change);

        if (newCount > 0) {
            badge.textContent = newCount;
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
            badge.remove();
        }
    } else if (change > 0) {
        // Create badge if it doesn't exist
        const newBadge = document.createElement('span');
        newBadge.className = 'badge bg-danger';
        newBadge.textContent = '1';
        wishlistLink.appendChild(newBadge);

        // Add bounce animation
        if (animate) {
            newBadge.style.transform = 'scale(1.3)';
            newBadge.style.transition = 'transform 0.2s ease';
            setTimeout(() => {
                newBadge.style.transform = 'scale(1)';
            }, 200);
        }
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

// Enhanced wishlist notification with icons
function showWishlistNotification(message, type = 'info', icon = 'heart') {
    // Remove existing wishlist notifications
    const existing = document.querySelectorAll('.wishlist-notification');
    existing.forEach(el => el.remove());

    // Determine colors
    let bgColor, iconClass;
    switch(type) {
        case 'success':
            bgColor = 'bg-danger'; // Use red/pink for wishlist (heart theme)
            iconClass = `bi-${icon}`;
            break;
        case 'error':
            bgColor = 'bg-secondary';
            iconClass = 'bi-x-circle';
            break;
        default:
            bgColor = 'bg-info';
            iconClass = 'bi-info-circle';
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `wishlist-notification position-fixed d-flex align-items-center gap-2 text-white px-4 py-3 rounded-3 shadow-lg ${bgColor}`;
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

    // Auto remove after 2.5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(-50%) translateY(-20px)';
        notification.style.opacity = '0';
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
