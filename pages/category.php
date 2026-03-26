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
$page_title = 'Categories';
$current_page = 'categories';

// Get all categories
$sql = "SELECT c.*, COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
        GROUP BY c.id
        ORDER BY c.name ASC";
$result = $conn->query($sql);

// Category icons mapping
$category_icons = [
    'Riding Gear' => 'bi-person-gear',
    'Motorcycle Parts' => 'bi-tools',
    'Accessories' => 'bi-bag-plus',
    'Maintenance' => 'bi-wrench-adjustable-circle',
    'Default' => 'bi-box-seam'
];

// Include header
include __DIR__ . '/../includes/header.php';

// Include navbar
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Browse by Category</h1>
        <p class="lead">Choose a motorcycle product type and find what you need</p>
    </div>
</div>

<!-- Categories Grid -->
<div class="container my-5">
    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($category = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card text-center p-4 h-100">
                        <div class="mb-3">
                            <?php
                            // Get icon for category
                            $icon = $category_icons[$category['name']] ?? $category_icons['Default'];
                            ?>
                            <i class="bi <?= $icon ?>" style="font-size: 60px; color: #20c997;"></i>
                        </div>
                        <h5 class="fw-bold mb-2"><?= e($category['name']) ?></h5>
                        <p class="text-muted mb-3">
                            <?= $category['product_count'] ?> <?= $category['product_count'] == 1 ? 'item' : 'items' ?>
                        </p>
                        <a href="products.php?category=<?= e($category['url']) ?>" class="btn btn-primary btn-sm mt-auto">
                            View Items <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No categories available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Featured Products Section -->
<div class="container mb-5">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Check out our most popular items across all categories</p>
        </div>
    </div>

    <?php
    // Get featured products
    $featured_sql = "SELECT p.*, c.name as category_name
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.is_active = 1 AND p.is_featured = 1
                     ORDER BY p.created_at DESC
                     LIMIT 4";
    $featured_result = $conn->query($featured_sql);
    ?>

    <div class="row g-4">
        <?php if ($featured_result->num_rows > 0): ?>
            <?php while ($product = $featured_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100 position-relative">
                        <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                            <div class="product-img">
                                <?php if ($product['image']): ?>
                                    <img src="../uploads/products/<?= e($product['image']) ?>" class="card-img-top" alt="<?= e($product['name']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-image" style="font-size: 80px; color: #dee2e6;"></i>
                                <?php endif; ?>
                                <span class="badge badge-featured position-absolute" style="top: 10px; left: 10px;">Featured</span>
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
                            <h6 class="card-title">
                                <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark"><?= e($product['name']) ?></a>
                            </h6>
                            <div class="mt-3">
                                <?php if (is_on_sale($product)): ?>
                                    <span class="text-muted text-decoration-line-through small"><?= format_price($product['price']) ?></span>
                                    <span class="text-success fw-bold d-block"><?= format_price($product['sale_price']) ?></span>
                                <?php else: ?>
                                    <span class="text-success fw-bold"><?= format_price($product['price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No featured products at the moment.
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
