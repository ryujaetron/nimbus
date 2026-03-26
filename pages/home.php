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
$page_title = 'Home';
$current_page = 'home';

// Get featured products
$sql = "SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1
        ORDER BY p.is_featured DESC, p.created_at DESC
        LIMIT 8";
$result = $conn->query($sql);

// Include header
include __DIR__ . '/../includes/header.php';

// Include navbar
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Welcome to CartHive!</h1>
                <p class="lead mb-4">Your one-stop shop for motorcycle parts, riding gear, and accessories.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p class="mb-4">Hello, <strong><?= e($_SESSION['user_name']) ?></strong>! Start shopping for the best motorcycle gear.</p>
                <?php else: ?>
                    <p class="mb-4">Discover our collection of premium motorcycle gear and accessories.</p>
                <?php endif; ?>

                <!-- Search Box -->
                <form action="search.php" method="GET" class="search-form">
                    <div class="input-group input-group-lg shadow-lg">
                        <input type="text"
                               name="q"
                               class="form-control form-control-lg"
                               placeholder="Search for helmets, parts, gear, accessories..."
                               required
                               style="border-right: none;">
                        <button class="btn btn-light" type="submit" style="border-left: none; background: white;">
                            <i class="bi bi-search" style="color: #20c997; font-size: 1.2rem;"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-lg-4 text-center">
                <i class="bi bi-bicycle" style="font-size: 150px; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Products Section -->
<div class="container mb-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Check out our latest and most popular motorcycle gear</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card position-relative">
                        <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                            <div class="product-img">
                                <?php if ($product['image']): ?>
                                    <img src="../uploads/products/<?= e($product['image']) ?>" class="card-img-top" alt="<?= e($product['name']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-image" style="font-size: 80px; color: #dee2e6;"></i>
                                <?php endif; ?>
                                <?php if ($product['is_featured']): ?>
                                    <span class="badge badge-featured position-absolute" style="top: 10px; left: 10px;">Featured</span>
                                <?php endif; ?>
                            </div>
                        </a>

                        <!-- Wishlist Heart Button -->
                        <button class="btn btn-sm wishlist-btn position-absolute"
                                style="top: 10px; right: 10px; z-index: 10; background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(0, 0, 0, 0.1); border-radius: 50%; width: 35px; height: 35px; backdrop-filter: blur(5px);"
                                onclick="toggleWishlist(<?= $product['id'] ?>, this)"
                                title="Add to wishlist">
                            <i class="bi bi-heart text-danger"></i>
                        </button>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted"><?= e($product['category_name']) ?></small>
                            </div>
                            <h5 class="card-title">
                                <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark"><?= e($product['name']) ?></a>
                            </h5>
                            <p class="card-text text-truncate" style="max-height: 48px;">
                                <?= e($product['description']) ?>
                            </p>
                            <div class="mb-3">
                                <?php if (is_on_sale($product)): ?>
                                    <span class="price-old"><?= format_price($product['price']) ?></span>
                                    <span class="price-sale fs-5"><?= format_price($product['sale_price']) ?></span>
                                    <span class="badge bg-danger ms-1"><?= get_discount_percentage($product['price'], $product['sale_price']) ?>% OFF</span>
                                <?php else: ?>
                                    <span class="fs-5 fw-bold text-success"><?= format_price($product['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm" onclick="addToCart(<?= $product['id'] ?>)">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No products available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Close database connection
$conn->close();

// Include footer
include __DIR__ . '/../includes/footer.php';
?>
