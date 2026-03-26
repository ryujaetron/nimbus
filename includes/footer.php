    <footer class="site-footer py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-shop"></i> CartHive</h5>
                    <p class="footer-text">Your trusted motorcycle parts and accessories store.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="footer-link">About Us</a></li>
                        <li><a href="contact.php" class="footer-link">Contact</a></li>
                        <li><a href="faq.php" class="footer-link">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="products.php?category=parts" class="footer-link">Motorcycle Parts</a></li>
                        <li><a href="products.php?category=gear" class="footer-link">Riding Gear</a></li>
                        <li><a href="products.php?category=accessories" class="footer-link">Accessories</a></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="text-center footer-text">
                <small>&copy; <?= date('Y') ?> CartHive. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <?php include __DIR__ . '/cart_sidebar.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart JS -->
    <script src="../assets/js/cart.js"></script>

    <!-- Wishlist JS -->
    <script src="../assets/js/wishlist.js"></script>

    <!-- Custom JS -->
    <?php if (isset($extra_js)): ?>
        <?= $extra_js ?>
    <?php endif; ?>
</body>
</html>
