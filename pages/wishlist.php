<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../config/db.php';

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';

// Set page variables
$page_title = 'My Wishlist';
$current_page = 'wishlist';

$is_guest = !isset($_SESSION['user_id']);
$user_id = $is_guest ? null : $_SESSION['user_id'];

// Get wishlist items
$wishlist_items = [];

if ($is_guest) {
    // Guest wishlist - get from session
    if (!empty($_SESSION['guest_wishlist'])) {
        $product_ids = $_SESSION['guest_wishlist'];
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

        $sql = "SELECT p.id, p.name, p.description, p.price, p.sale_price, p.image, p.stock, p.is_active,
                       c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id IN ($placeholders)
                ORDER BY p.name ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $wishlist_items[] = $row;
        }
    }
} else {
    // Logged-in user - get from database
    $sql = "SELECT w.id as wishlist_id, w.created_at as added_at,
                   p.id, p.name, p.description, p.price, p.sale_price, p.image, p.stock, p.is_active,
                   c.name as category_name
            FROM wishlists w
            JOIN products p ON w.product_id = p.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $wishlist_items[] = $row;
    }
}

// Include header
include __DIR__ . '/../includes/header.php';

// Include navbar
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Breadcrumb -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
        </ol>
    </nav>
</div>

<!-- Wishlist Content -->
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold mb-2">
                <i class="bi bi-heart-fill text-danger"></i> My Wishlist
            </h1>
            <p class="text-muted">
                <?php
                $count = count($wishlist_items);
                echo $count . ' ' . ($count == 1 ? 'item' : 'items');
                ?> saved
            </p>
            <?php if ($is_guest && $count > 0): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> <strong>Guest Mode:</strong>
                    <a href="../auth/login.php">Login</a> to save your wishlist permanently.
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4 text-md-end">
            <?php if ($count > 0): ?>
                <button class="btn btn-outline-danger" onclick="clearWishlist()">
                    <i class="bi bi-trash"></i> Clear Wishlist
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (count($wishlist_items) > 0): ?>
        <div class="row g-4">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="col-md-6 col-lg-3" id="wishlist-item-<?= $item['id'] ?>">
                    <div class="card product-card h-100 position-relative">
                        <!-- Remove from Wishlist Button -->
                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                onclick="removeFromWishlist(<?= $item['id'] ?>)"
                                style="z-index: 10;">
                            <i class="bi bi-x-lg"></i>
                        </button>

                        <a href="product.php?id=<?= $item['id'] ?>" class="text-decoration-none">
                            <div class="product-img">
                                <?php if ($item['image']): ?>
                                    <img src="../uploads/products/<?= e($item['image']) ?>" class="card-img-top" alt="<?= e($item['name']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-image" style="font-size: 80px; color: #dee2e6;"></i>
                                <?php endif; ?>
                            </div>
                        </a>

                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <small class="text-muted"><?= e($item['category_name']) ?></small>
                            </div>
                            <h6 class="card-title">
                                <a href="product.php?id=<?= $item['id'] ?>" class="text-decoration-none text-dark">
                                    <?= e($item['name']) ?>
                                </a>
                            </h6>
                            <p class="card-text text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= e($item['description']) ?>
                            </p>
                            <div class="mt-auto">
                                <div class="mb-3">
                                    <?php if (is_on_sale($item)): ?>
                                        <span class="text-muted text-decoration-line-through small"><?= format_price($item['price']) ?></span>
                                        <span class="text-success fw-bold d-block"><?= format_price($item['sale_price']) ?></span>
                                        <span class="badge bg-danger"><?= get_discount_percentage($item['price'], $item['sale_price']) ?>% OFF</span>
                                    <?php else: ?>
                                        <span class="text-success fw-bold"><?= format_price($item['price']) ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($item['stock'] > 0 && $item['is_active']): ?>
                                    <button class="btn btn-primary btn-sm w-100 mb-2" onclick="addToCartFromWishlist(<?= $item['id'] ?>)">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm w-100 mb-2" disabled>
                                        <i class="bi bi-x-circle"></i> Out of Stock
                                    </button>
                                <?php endif; ?>

                                <?php if (!$is_guest && isset($item['added_at'])): ?>
                                    <small class="text-muted d-block text-center">
                                        Added <?= format_date($item['added_at']) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty Wishlist -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-heart" style="font-size: 100px; color: #dee2e6;"></i>
                    <h3 class="mt-4">Your wishlist is empty</h3>
                    <p class="text-muted mb-4">Save items you love so you don't lose sight of them.</p>
                    <a href="products.php" class="btn btn-primary">
                        <i class="bi bi-grid"></i> Browse Products
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Remove item from wishlist
async function removeFromWishlist(productId) {
    if (!confirm('Remove this item from your wishlist?')) return;

    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);

    try {
        const response = await fetch('../handlers/wishlist_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            // Remove the card from view
            const card = document.getElementById('wishlist-item-' + productId);
            if (card) {
                card.style.transition = 'opacity 0.3s';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();

                    // Check if wishlist is now empty
                    const remaining = document.querySelectorAll('[id^="wishlist-item-"]').length;
                    if (remaining === 0) {
                        location.reload();
                    }
                }, 300);
            }
            showNotification('Removed from wishlist', 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Failed to remove from wishlist', 'error');
    }
}

// Clear entire wishlist
async function clearWishlist() {
    if (!confirm('Are you sure you want to clear your entire wishlist?')) return;

    const productIds = Array.from(document.querySelectorAll('[id^="wishlist-item-"]')).map(el => {
        return el.id.replace('wishlist-item-', '');
    });

    let success = true;
    for (const productId of productIds) {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', productId);

        try {
            await fetch('../handlers/wishlist_handler.php', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            success = false;
        }
    }

    if (success) {
        showNotification('Wishlist cleared', 'success');
        setTimeout(() => location.reload(), 1000);
    } else {
        showNotification('Failed to clear wishlist', 'error');
    }
}

// Add to cart from wishlist
async function addToCartFromWishlist(productId) {
    await addToCart(productId, 1);
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<?php
// Close database connection
$conn->close();

// Include footer
include __DIR__ . '/../includes/footer.php';
?>
