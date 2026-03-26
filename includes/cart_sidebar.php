<!-- Cart Sidebar -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h5 class="mb-0">
            <i class="bi bi-cart3"></i> Shopping Cart
        </h5>
        <button type="button" class="btn-close" onclick="closeCart()"></button>
    </div>

    <div class="cart-body" id="cartItems">
        <!-- Cart items will be loaded here -->
    </div>

    <div class="text-center py-5 d-none" id="emptyCartMessage">
        <i class="bi bi-cart-x" style="font-size: 60px; color: #dee2e6;"></i>
        <p class="text-muted mt-3">Your cart is empty</p>
        <button class="btn btn-primary" onclick="closeCart()">Continue Shopping</button>
    </div>

    <div class="cart-footer" id="cartFooter">
        <div class="d-flex justify-content-between mb-3">
            <strong>Total:</strong>
            <strong class="text-success" id="cartTotal">₱0.00</strong>
        </div>
        <div class="d-grid gap-2" id="checkoutButtons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged-in user - proceed to checkout -->
                <a href="checkout.php" class="btn btn-primary">
                    <i class="bi bi-credit-card"></i> Proceed to Checkout
                </a>
            <?php else: ?>
                <!-- Guest user - login required -->
                <a href="../auth/login.php?redirect=checkout" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Login to Checkout
                </a>
                <p class="text-muted small text-center mb-0">
                    <i class="bi bi-info-circle"></i> Login required to complete purchase
                </p>
            <?php endif; ?>
            <button class="btn btn-outline-secondary" onclick="closeCart()">Continue Shopping</button>
        </div>
    </div>
</div>

<style>
.cart-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
}

.cart-overlay.active {
    opacity: 1;
    visibility: visible;
}

.cart-sidebar {
    position: fixed;
    top: 0;
    right: -400px;
    width: 400px;
    height: 100%;
    background: white;
    z-index: 1050;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    transition: right 0.3s;
    display: flex;
    flex-direction: column;
}

.cart-sidebar.active {
    right: 0;
}

.cart-header {
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
}

.cart-footer {
    padding: 1.5rem;
    border-top: 1px solid #dee2e6;
    background: #f8f9fa;
}

.cart-item-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .cart-sidebar {
        width: 100%;
        right: -100%;
    }
}
</style>
