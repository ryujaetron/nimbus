<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product - Nimbus</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>

  <body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom">
      <div class="container">
        <a class="navbar-brand fw-bold" href="home.html">
          <i class="bi bi-shop"></i> Nimbus
        </a>
        <button
          class="navbar-toggler"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="home.html">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="category.html">Categories</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="search.html">Search</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="product.html">Product</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- HERO SECTION -->
    <div class="hero-section">
      <div class="container text-center">
        <h1 class="fw-bold">Product Details</h1>
        <p class="mb-0">View complete product information</p>
      </div>
    </div>

    <!-- PRODUCT DETAILS -->
    <div class="container my-5">
      <div class="row g-4">
        <!-- LEFT IMAGE -->
        <div class="col-md-6">
          <div class="card p-3">
            <img
              src="https://via.placeholder.com/500x400?text=Product+Image"
              class="img-fluid rounded"
            />
          </div>
        </div>

        <!-- RIGHT INFO -->
        <div class="col-md-6">
          <h2 class="fw-bold">Premium Riding Helmet</h2>

          <p class="text-muted mb-1">Category: Riding Gear</p>

          <div class="mt-3 mb-3">
            <span class="fs-4 fw-bold text-success">₱2,499.00</span>
            <span class="price-old ms-2">₱3,199.00</span>
          </div>

          <p class="text-muted">
            This premium motorcycle helmet ensures maximum protection, comfort,
            and durability. Designed for riders who value safety and
            performance.
          </p>

          <ul>
            <li>Impact-resistant shell</li>
            <li>Breathable inner padding</li>
            <li>Anti-scratch visor</li>
            <li>Lightweight design</li>
          </ul>

          <div class="d-flex mt-4 gap-2">
            <button class="btn btn-primary">
              <i class="bi bi-cart-plus"></i> Add to Cart
            </button>
            <button class="btn btn-outline-danger">
              <i class="bi bi-heart"></i> Add to Wishlist
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white py-3 text-center">
      <small>&copy; 2024 Nimbus. All rights reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
