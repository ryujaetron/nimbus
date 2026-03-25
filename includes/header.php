<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - CartHive' : 'CartHive' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom CSS -->
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
        .navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
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

    <?php if (isset($extra_css)): ?>
        <?= $extra_css ?>
    <?php endif; ?>
</head>
<body>
