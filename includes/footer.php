    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-shop"></i> CartHive</h5>
                    <p class="text-muted">Your trusted motorcycle parts and accessories store.</p>
                </div>
                <div class="col-md-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-muted text-decoration-none">Contact</a></li>
                        <li><a href="faq.php" class="text-muted text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="products.php?category=parts" class="text-muted text-decoration-none">Motorcycle Parts</a></li>
                        <li><a href="products.php?category=gear" class="text-muted text-decoration-none">Riding Gear</a></li>
                        <li><a href="products.php?category=accessories" class="text-muted text-decoration-none">Accessories</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center text-muted">
                <small>&copy; <?= date('Y') ?> CartHive. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart JS -->
    <script src="../assets/js/cart.js"></script>

    <!-- Custom JS -->
    <?php if (isset($extra_js)): ?>
        <?= $extra_js ?>
    <?php endif; ?>
</body>
</html>
