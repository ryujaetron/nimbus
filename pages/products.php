<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../config/db.php';

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';

// Get category from URL if provided
$category_url = isset($_GET['category']) ? trim($_GET['category']) : null;
$category_name = 'All Products';
$category_id = null;

// If category is specified, get category details
if ($category_url) {
    $cat_sql = "SELECT id, name FROM categories WHERE url = ?";
    $cat_stmt = $conn->prepare($cat_sql);
    $cat_stmt->bind_param("s", $category_url);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();

    if ($cat_result->num_rows > 0) {
        $category = $cat_result->fetch_assoc();
        $category_id = $category['id'];
        $category_name = $category['name'];
    }
    $cat_stmt->close();
}

// Build product query
if ($category_id) {
    $sql = "SELECT p.*, c.name as category_name, c.url as category_url
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.category_id = ?
            ORDER BY p.is_featured DESC, p.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $sql = "SELECT p.*, c.name as category_name, c.url as category_url
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY p.is_featured DESC, p.created_at DESC";
    $result = $conn->query($sql);
}

// Set page variables
$page_title = $category_name;
$current_page = 'products';

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
            <li class="breadcrumb-item"><a href="category.php">Categories</a></li>
            <?php if ($category_url): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= e($category_name) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">All Products</li>
            <?php endif; ?>
        </ol>
    </nav>
</div>

<!-- Page Header -->
<div class="container mt-4 mb-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold mb-2"><?= e($category_name) ?></h1>
            <p class="text-muted mb-0">
                <?php if ($category_url): ?>
                    Showing all products in <?= e($category_name) ?>
                <?php else: ?>
                    Browse our complete collection of motorcycle parts, gear, and accessories
                <?php endif; ?>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="category.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="products.php" class="btn btn-sm <?= !$category_url ? 'btn-primary' : 'btn-outline-primary' ?>">
                    All Products
                </a>
                <?php
                // Get all categories for filter
                $filter_sql = "SELECT * FROM categories ORDER BY name ASC";
                $filter_result = $conn->query($filter_sql);
                while ($cat = $filter_result->fetch_assoc()):
                ?>
                    <a href="products.php?category=<?= e($cat['url']) ?>"
                       class="btn btn-sm <?= ($category_url == $cat['url']) ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <?= e($cat['name']) ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($product = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100 position-relative">
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
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <small class="text-muted"><?= e($product['category_name']) ?></small>
                            </div>
                            <h6 class="card-title">
                                <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark"><?= e($product['name']) ?></a>
                            </h6>
                            <p class="card-text text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= e($product['description']) ?>
                            </p>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <?php if (is_on_sale($product)): ?>
                                        <span class="text-muted text-decoration-line-through small"><?= format_price($product['price']) ?></span>
                                        <span class="text-success fw-bold d-block"><?= format_price($product['sale_price']) ?></span>
                                        <span class="badge bg-danger"><?= get_discount_percentage($product['price'], $product['sale_price']) ?>% OFF</span>
                                    <?php else: ?>
                                        <span class="text-success fw-bold"><?= format_price($product['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($product['stock'] > 0): ?>
                                    <button class="btn btn-primary btn-sm w-100" onclick="addToCart(<?= $product['id'] ?>)">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="bi bi-x-circle"></i> Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No products found in this category.
                    <br>
                    <a href="products.php" class="btn btn-primary mt-3">View All Products</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Count -->
    <?php if ($result->num_rows > 0): ?>
        <div class="row mt-4">
            <div class="col-12 text-center">
                <p class="text-muted">
                    Showing <strong><?= $result->num_rows ?></strong> <?= $result->num_rows == 1 ? 'product' : 'products' ?>
                    <?php if ($category_url): ?>
                        in <strong><?= e($category_name) ?></strong>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Close database connection
$conn->close();

// Include footer
include __DIR__ . '/../includes/footer.php';
?>
