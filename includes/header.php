<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
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

    <!-- Dark Mode Detection (runs early to prevent flash) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <style>
        /* CSS Variables for theming */
        :root {
            --navbar-bg: #20c997;
            --hero-gradient-start: #20c997;
            --hero-gradient-end: #17a589;
            --card-bg: #ffffff;
            --product-img-bg: #f8f9fa;
        }

        [data-bs-theme="dark"] {
            --navbar-bg: #1a8a6e;
            --hero-gradient-start: #1a8a6e;
            --hero-gradient-end: #126b55;
            --card-bg: #2d2d2d;
            --product-img-bg: #3d3d3d;
        }

        .navbar-custom {
            background-color: var(--navbar-bg);
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
            background: linear-gradient(135deg, var(--hero-gradient-start) 0%, var(--hero-gradient-end) 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            background-color: var(--card-bg);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .product-img {
            height: 200px;
            width: 100%;
            background: var(--product-img-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img img {
            transform: scale(1.05);
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

        /* Wishlist Heart Button Styles */
        .wishlist-btn {
            transition: all 0.2s ease;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wishlist-btn:hover {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 1) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .wishlist-btn i {
            font-size: 16px;
            transition: all 0.2s ease;
        }
        .wishlist-btn.in-wishlist i {
            color: #dc3545 !important;
        }
        .wishlist-btn:hover i {
            transform: scale(1.1);
        }

        /* Dark Mode Toggle Button */
        .theme-toggle {
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .theme-toggle i {
            font-size: 1.1rem;
        }

        /* Dark mode specific adjustments */
        [data-bs-theme="dark"] .card {
            border-color: #404040;
        }
        [data-bs-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #e9ecef !important;
        }
        [data-bs-theme="dark"] .card-title a {
            color: #e9ecef !important;
        }
        [data-bs-theme="dark"] .card-title a:hover {
            color: #20c997 !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #2d2d2d !important;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #3d3d3d;
            border-color: #505050;
            color: #e9ecef;
        }
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #2d2d2d;
            border-color: #404040;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #e9ecef;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #3d3d3d;
        }

        /* Navbar search box dark mode */
        [data-bs-theme="dark"] #navbarSearchInput {
            background-color: #2d2d2d !important;
            border-color: #505050 !important;
            color: #e9ecef !important;
        }
        [data-bs-theme="dark"] #navbarSearchInput::placeholder {
            color: #adb5bd;
        }

        /* Footer Styles */
        .site-footer {
            background-color: #f8f9fa;
            color: #212529;
            border-top: 1px solid #dee2e6;
        }
        .site-footer h5,
        .site-footer h6 {
            color: #212529;
        }
        .footer-text {
            color: #6c757d;
        }
        .footer-link {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-link:hover {
            color: #20c997;
        }
        .footer-divider {
            border-color: #dee2e6;
            opacity: 0.5;
        }

        /* Footer Dark Mode */
        [data-bs-theme="dark"] .site-footer {
            background-color: #1a1a1a;
            color: #e9ecef;
            border-top-color: #333;
        }
        [data-bs-theme="dark"] .site-footer h5,
        [data-bs-theme="dark"] .site-footer h6 {
            color: #e9ecef;
        }
        [data-bs-theme="dark"] .footer-text {
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .footer-link {
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .footer-link:hover {
            color: #20c997;
        }
        [data-bs-theme="dark"] .footer-divider {
            border-color: #444;
        }
    </style>

    <?php if (isset($extra_css)): ?>
        <?= $extra_css ?>
    <?php endif; ?>
</head>
<body>
