<?php
// Include authentication check
require_once __DIR__ . '/../includes/auth_check.php';

// Include database connection
require_once __DIR__ . '/../config/db.php';

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';

// Get search parameters
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort_by = isset($_GET['sort']) ? trim($_GET['sort']) : 'relevance';

// Set page variables
$page_title = 'Search Results';
$current_page = 'search';

// Perform search if query is not empty
$results = [];
$result_count = 0;

if (!empty($search_query)) {
    // Prepare search term for SQL LIKE query
    $search_term = '%' . $search_query . '%';

    // Build base query
    $sql = "SELECT p.*, c.name as category_name, c.url as category_url
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            AND (p.name LIKE ? OR p.description LIKE ?)";

    // Add category filter if specified
    if (!empty($filter_category)) {
        $sql .= " AND c.url = ?";
    }

    // Add sorting
    switch ($sort_by) {
        case 'price_low':
            $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
            break;
        case 'price_high':
            $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
            break;
        case 'newest':
            $sql .= " ORDER BY p.created_at DESC";
            break;
        case 'name':
            $sql .= " ORDER BY p.name ASC";
            break;
        default: // relevance
            $sql .= " ORDER BY
                CASE
                    WHEN p.name LIKE ? THEN 1
                    WHEN p.description LIKE ? THEN 2
                    ELSE 3
                END,
                p.is_featured DESC,
                p.created_at DESC";
    }

    // Prepare and execute statement
    $stmt = $conn->prepare($sql);

    if (!empty($filter_category)) {
        if ($sort_by === 'relevance') {
            $stmt->bind_param("sssss", $search_term, $search_term, $filter_category, $search_term, $search_term);
        } else {
            $stmt->bind_param("sss", $search_term, $search_term, $filter_category);
        }
    } else {
        if ($sort_by === 'relevance') {
            $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
        } else {
            $stmt->bind_param("ss", $search_term, $search_term);
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $result_count = $result->num_rows;

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();
}

// Get all categories for filter
$categories_sql = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);

// Include header
include __DIR__ . '/../includes/header.php';

// Include navbar
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Search Header -->
<div class="container mt-4 mb-3">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Search Results</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Search Box Section -->
<div class="container mb-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-3">
                <?php if (!empty($search_query)): ?>
                    Search Results for "<?= e($search_query) ?>"
                <?php else: ?>
                    Search Products
                <?php endif; ?>
            </h1>

            <!-- Search Form -->
            <form action="search.php" method="GET" class="mb-3">
                <div class="input-group input-group-lg">
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Search for products..."
                           value="<?= e($search_query) ?>"
                           required>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>

            <?php if (!empty($search_query)): ?>
                <!-- Filter Bar -->
                <div class="d-flex flex-wrap gap-3 align-items-center mb-3 p-3 bg-light rounded">
                    <!-- Category Filter -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Category:</label>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="applyFilter(this.value, 'category')">
                            <option value="">All Categories</option>
                            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?= e($cat['url']) ?>" <?= $filter_category === $cat['url'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small mb-0">Sort by:</label>
                        <select class="form-select form-select-sm" style="width: auto;" onchange="applyFilter(this.value, 'sort')">
                            <option value="relevance" <?= $sort_by === 'relevance' ? 'selected' : '' ?>>Relevance</option>
                            <option value="price_low" <?= $sort_by === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sort_by === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="newest" <?= $sort_by === 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="name" <?= $sort_by === 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <?php if (!empty($filter_category) || $sort_by !== 'relevance'): ?>
                        <a href="search.php?q=<?= urlencode($search_query) ?>" class="btn btn-sm btn-outline-secondary ms-auto">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    <?php endif; ?>
                </div>

                <p class="text-muted">
                    Found <strong><?= $result_count ?></strong> <?= $result_count == 1 ? 'product' : 'products' ?>
                    <?php if (!empty($filter_category)): ?>
                        <?php
                        // Get category name
                        $categories_result->data_seek(0);
                        while ($cat = $categories_result->fetch_assoc()) {
                            if ($cat['url'] === $filter_category) {
                                echo 'in <strong>' . e($cat['name']) . '</strong>';
                                break;
                            }
                        }
                        ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Search Results -->
<div class="container mb-5">
    <?php if (empty($search_query)): ?>
        <!-- No search query yet -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-search" style="font-size: 80px; color: #dee2e6;"></i>
                    <h3 class="mt-3 text-muted">Start searching for products</h3>
                    <p class="text-muted">Enter keywords above to find motorcycle parts, gear, and accessories</p>

                    <div class="mt-4">
                        <h5 class="mb-3">Popular searches:</h5>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="search.php?q=helmet" class="btn btn-outline-primary btn-sm">Helmet</a>
                            <a href="search.php?q=gloves" class="btn btn-outline-primary btn-sm">Gloves</a>
                            <a href="search.php?q=parts" class="btn btn-outline-primary btn-sm">Parts</a>
                            <a href="search.php?q=LED" class="btn btn-outline-primary btn-sm">LED</a>
                            <a href="search.php?q=accessories" class="btn btn-outline-primary btn-sm">Accessories</a>
                        </div>
                    </div>

                    <div class="mt-5">
                        <a href="home.php" class="btn btn-primary">Go to Homepage</a>
                        <a href="category.php" class="btn btn-outline-primary">Browse Categories</a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($result_count > 0): ?>
        <!-- Display search results -->
        <div class="row g-4">
            <?php foreach ($results as $product): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100">
                        <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                            <div class="product-img">
                                <?php if ($product['image']): ?>
                                    <img src="../uploads/products/<?= e($product['image']) ?>" class="card-img-top" alt="<?= e($product['name']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-image" style="font-size: 80px; color: #dee2e6;"></i>
                                <?php endif; ?>
                                <?php if ($product['is_featured']): ?>
                                    <span class="badge badge-featured position-absolute top-0 end-0 m-2">Featured</span>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <small class="text-muted"><?= e($product['category_name']) ?></small>
                            </div>
                            <h6 class="card-title">
                                <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                                    <?= e($product['name']) ?>
                                </a>
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
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- No results found -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown" style="font-size: 80px; color: #dee2e6;"></i>
                    <h3 class="mt-3">No products found</h3>
                    <p class="text-muted">We couldn't find any products matching "<strong><?= e($search_query) ?></strong>"
                        <?php if (!empty($filter_category)): ?>
                            <?php
                            $categories_result->data_seek(0);
                            while ($cat = $categories_result->fetch_assoc()) {
                                if ($cat['url'] === $filter_category) {
                                    echo ' in <strong>' . e($cat['name']) . '</strong>';
                                    break;
                                }
                            }
                            ?>
                        <?php endif; ?>
                    </p>

                    <div class="mt-4">
                        <h5 class="mb-3">Suggestions:</h5>
                        <ul class="list-unstyled text-muted">
                            <li>Check your spelling</li>
                            <li>Try different or more general keywords</li>
                            <li>Remove filters to see more results</li>
                            <li>Browse by <a href="category.php">categories</a></li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <?php if (!empty($filter_category) || $sort_by !== 'relevance'): ?>
                            <a href="search.php?q=<?= urlencode($search_query) ?>" class="btn btn-primary">
                                <i class="bi bi-x-circle"></i> Clear Filters &amp; Try Again
                            </a>
                        <?php endif; ?>
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="bi bi-grid"></i> View All Products
                        </a>
                        <a href="category.php" class="btn btn-outline-secondary">
                            <i class="bi bi-grid-3x3-gap"></i> Browse Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Filter navigation helper
function applyFilter(value, type) {
    const urlParams = new URLSearchParams(window.location.search);

    if (type === 'category') {
        if (value) {
            urlParams.set('category', value);
        } else {
            urlParams.delete('category');
        }
    } else if (type === 'sort') {
        if (value && value !== 'relevance') {
            urlParams.set('sort', value);
        } else {
            urlParams.delete('sort');
        }
    }

    window.location.href = 'search.php?' + urlParams.toString();
}
</script>

<?php
// Close database connection
$conn->close();

// Include footer
include __DIR__ . '/../includes/footer.php';
?>
