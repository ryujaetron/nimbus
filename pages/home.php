<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Get featured products
$sql = "SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_active = 1
        ORDER BY p.is_featured DESC, p.created_at DESC
        LIMIT 8";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Nimbus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .navbar-custom {
            background-color: #20c997;
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #fff !important;
        }
        .navbar-custom .nav-link:hover {
            color: #e0e0e0 !important;
        }
        .hero-section {
            background: linear-gradient(135deg, #20c997 0%, #17a589 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .product-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .product-img {
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .badge-featured {
            background-color: #ffc107;
            color: #000;
        }
        .price-old {
            text-decoration: line-through;
            color: #6c757d;
        }
        .price-sale {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home.php">
                <i class="bi bi-shop"></i> Nimbus
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="wishlist.php">
                            <i class="bi bi-heart"></i> Wishlist
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="order_history.php">
                            <i class="bi bi-clock-history"></i> Orders
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="../admin/dashboard.php"><i class="bi bi-speedometer2"></i> Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Welcome to Nimbus!</h1>
                    <p class="lead mb-4">Your one-stop shop for motorcycle parts, riding gear, and accessories.</p>
                    <p class="mb-0">Hello, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>! Start shopping for the best motorcycle gear.</p>
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
                        <div class="card product-card">
                            <div class="product-img">
                                <?php if ($product['image']): ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <i class="bi bi-image" style="font-size: 80px; color: #dee2e6;"></i>
                                <?php endif; ?>
                                <?php if ($product['is_featured']): ?>
                                    <span class="badge badge-featured position-absolute top-0 end-0 m-2">Featured</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted"><?= htmlspecialchars($product['category_name']) ?></small>
                                </div>
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-truncate" style="max-height: 48px;">
                                    <?= htmlspecialchars($product['description']) ?>
                                </p>
                                <div class="mb-3">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="price-old">₱<?= number_format($product['price'], 2) ?></span>
                                        <span class="price-sale fs-5">₱<?= number_format($product['sale_price'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="fs-5 fw-bold text-success">₱<?= number_format($product['price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-sm">
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

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-shop"></i> Nimbus</h5>
                    <p class="text-muted">Your trusted motorcycle parts and accessories store.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted text-decoration-none">Motorcycle Parts</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Riding Gear</a></li>
                        <li><a href="#" class="text-muted text-decoration-none">Accessories</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center text-muted">
                <small>&copy; <?= date('Y') ?> Nimbus. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

