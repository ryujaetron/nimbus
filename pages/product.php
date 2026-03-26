<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../config/db.php';

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: home.php');
    exit;
}

// Get product details
$sql = "SELECT p.*, c.name as category_name, c.url as category_url
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ? AND p.is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: home.php');
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

// Get product reviews
$review_sql = "SELECT r.*, u.first_name, u.last_name
               FROM reviews r
               JOIN users u ON r.user_id = u.id
               WHERE r.product_id = ?
               ORDER BY r.created_at DESC";
$review_stmt = $conn->prepare($review_sql);
$review_stmt->bind_param("i", $product_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();

// Calculate average rating
$avg_rating = 0;
$total_reviews = $reviews->num_rows;
if ($total_reviews > 0) {
    $temp_reviews = $reviews->fetch_all(MYSQLI_ASSOC);
    $sum = array_sum(array_column($temp_reviews, 'rating'));
    $avg_rating = round($sum / $total_reviews, 1);
    // Reset reviews for display
    $reviews->data_seek(0);
}

$review_stmt->close();

// Get related products (same category)
$related_sql = "SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
                LIMIT 4";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_products = $related_stmt->get_result();
$related_stmt->close();

// Set page variables
$page_title = e($product['name']);
$current_page = 'products';

// Extra CSS for product page
$extra_css = "
<style>
    .product-image {
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        margin-bottom: 20px;
    }
    .product-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .stock-badge {
        font-size: 0.9rem;
    }
    .rating-stars {
        color: #ffc107;
    }
    .related-product-img {
        height: 150px;
        object-fit: cover;
        background: #f8f9fa;
    }
</style>
";

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
            <li class="breadcrumb-item"><a href="products.php?category=<?= e($product['category_url']) ?>"><?= e($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
        </ol>
    </nav>
</div>

<!-- Product Detail Section -->
<div class="container mb-5">
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6">
            <div class="product-image">
                <?php if ($product['image']): ?>
                    <img src="../uploads/products/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                <?php else: ?>
                    <i class="bi bi-image" style="font-size: 120px; color: #dee2e6;"></i>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-6">
            <div class="mb-2">
                <span class="badge bg-secondary"><?= e($product['category_name']) ?></span>
                <?php if ($product['is_featured']): ?>
                    <span class="badge bg-warning text-dark">Featured</span>
                <?php endif; ?>
            </div>

            <h1 class="mb-3"><?= e($product['name']) ?></h1>

            <!-- Rating -->
            <div class="mb-3">
                <?php if ($total_reviews > 0): ?>
                    <span class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= floor($avg_rating)): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php elseif ($i - $avg_rating < 1 && $i - $avg_rating > 0): ?>
                                <i class="bi bi-star-half"></i>
                            <?php else: ?>
                                <i class="bi bi-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </span>
                    <span class="text-muted"><?= $avg_rating ?> (<?= $total_reviews ?> <?= $total_reviews == 1 ? 'review' : 'reviews' ?>)</span>
                <?php else: ?>
                    <span class="text-muted">No reviews yet</span>
                <?php endif; ?>
            </div>

            <!-- Price -->
            <div class="mb-4">
                <?php if (is_on_sale($product)): ?>
                    <h3 class="text-success mb-1"><?= format_price($product['sale_price']) ?></h3>
                    <p class="text-muted mb-0">
                        <span class="text-decoration-line-through"><?= format_price($product['price']) ?></span>
                        <span class="badge bg-danger ms-2"><?= get_discount_percentage($product['price'], $product['sale_price']) ?>% OFF</span>
                    </p>
                <?php else: ?>
                    <h3 class="text-success"><?= format_price($product['price']) ?></h3>
                <?php endif; ?>
            </div>

            <!-- Stock Status -->
            <div class="mb-4">
                <?php if ($product['stock'] > 0): ?>
                    <span class="badge bg-success stock-badge">
                        <i class="bi bi-check-circle"></i> In Stock (<?= $product['stock'] ?> available)
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger stock-badge">
                        <i class="bi bi-x-circle"></i> Out of Stock
                    </span>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h5>Description</h5>
                <p><?= nl2br(e($product['description'])) ?></p>
            </div>

            <!-- Quantity & Add to Cart -->
            <div class="mb-4">
                <div class="row g-2">
                    <div class="col-auto">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>" style="width: 80px;">
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex">
                <?php if ($product['stock'] > 0): ?>
                    <button class="btn btn-primary btn-lg" onclick="addToCartWithQuantity(<?= $product['id'] ?>)">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                    <button class="btn btn-outline-secondary btn-lg" id="wishlist-btn-<?= $product['id'] ?>" onclick="toggleWishlist(<?= $product['id'] ?>)">
                        <i class="bi bi-heart"></i> <span id="wishlist-text-<?= $product['id'] ?>">Add to Wishlist</span>
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg" disabled>
                        <i class="bi bi-x-circle"></i> Out of Stock
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Customer Reviews</h3>

            <?php if ($total_reviews > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1"><?= e($review['first_name'] . ' ' . $review['last_name']) ?></h6>
                                    <span class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $review['rating']): ?>
                                                <i class="bi bi-star-fill"></i>
                                            <?php else: ?>
                                                <i class="bi bi-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </span>
                                </div>
                                <small class="text-muted"><?= format_datetime($review['created_at']) ?></small>
                            </div>
                            <?php if ($review['comment']): ?>
                                <p class="mb-0"><?= nl2br(e($review['comment'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No reviews yet. Be the first to review this product!
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Products -->
    <?php if ($related_products->num_rows > 0): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
            </div>

            <?php while ($related = $related_products->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card product-card h-100">
                        <a href="product.php?id=<?= $related['id'] ?>" class="text-decoration-none text-dark">
                            <div class="related-product-img">
                                <?php if ($related['image']): ?>
                                    <img src="../uploads/products/<?= e($related['image']) ?>" class="card-img-top" alt="<?= e($related['name']) ?>">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="bi bi-image" style="font-size: 60px; color: #dee2e6;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <small class="text-muted"><?= e($related['category_name']) ?></small>
                                <h6 class="card-title"><?= e($related['name']) ?></h6>
                                <div class="mt-2">
                                    <?php if (is_on_sale($related)): ?>
                                        <span class="text-muted text-decoration-line-through small"><?= format_price($related['price']) ?></span>
                                        <span class="text-success fw-bold"><?= format_price($related['sale_price']) ?></span>
                                    <?php else: ?>
                                        <span class="text-success fw-bold"><?= format_price($related['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Add to cart with quantity
function addToCartWithQuantity(productId) {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    addToCart(productId, quantity);
}

// Toggle wishlist
async function toggleWishlist(productId) {
    const btn = document.getElementById('wishlist-btn-' + productId);
    const text = document.getElementById('wishlist-text-' + productId);
    const icon = btn.querySelector('i');

    // Check if already in wishlist
    const isInWishlist = icon.classList.contains('bi-heart-fill');

    const formData = new FormData();
    formData.append('action', isInWishlist ? 'remove' : 'add');
    formData.append('product_id', productId);

    try {
        const response = await fetch('../handlers/wishlist_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            if (isInWishlist) {
                // Removed from wishlist
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-secondary');
                text.textContent = 'Add to Wishlist';
            } else {
                // Added to wishlist
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-danger');
                text.textContent = 'In Wishlist';
            }
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Failed to update wishlist', 'error');
    }
}

// Check if product is in wishlist on page load
async function checkWishlist(productId) {
    const formData = new FormData();
    formData.append('action', 'check');
    formData.append('product_id', productId);

    try {
        const response = await fetch('../handlers/wishlist_handler.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success && data.in_wishlist) {
            const btn = document.getElementById('wishlist-btn-' + productId);
            const text = document.getElementById('wishlist-text-' + productId);
            const icon = btn.querySelector('i');

            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-danger');
            text.textContent = 'In Wishlist';
        }
    } catch (error) {
        console.error('Error checking wishlist:', error);
    }
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

// Check wishlist status on page load
document.addEventListener('DOMContentLoaded', function() {
    const productId = <?= $product['id'] ?>;
    checkWishlist(productId);
});
</script>

<?php
// Close database connection
$conn->close();

// Include footer
include __DIR__ . '/../includes/footer.php';
?>
