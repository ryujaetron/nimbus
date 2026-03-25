<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Categories - Nimbus</title>

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
    <!-- NAVBAR (same as home) -->
    <nav class="navbar navbar-expand-lg navbar-custom">
      <div class="container">
        <a class="navbar-brand fw-bold" href="home.html">
          <i class="bi bi-shop"></i> Nimbus
        </a>
        <button
          class="navbar-toggler"
          data-bs-toggle="collapse"
          data-bs-target="#navMenu"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="home.html">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="category.html">Categories</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="search.html">Search</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="product.html">Products</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- PAGE TITLE -->
    <section class="hero-section text-center text-white p-4">
      <h1 class="fw-bold">Browse by Category</h1>
      <p class="lead">Choose a motorcycle product type</p>
    </section>

    <!-- CATEGORIES GRID -->
    <div class="container my-5">
      <div class="row g-4">
        <!-- CATEGORY CARD -->
        <div class="col-md-3">
          <div class="card product-card text-center p-3">
            <i class="bi bi-helmet" style="font-size: 60px; color: #20c997"></i>
            <h5 class="mt-3 fw-bold">Riding Gear</h5>
            <a href="search.html" class="btn btn-primary btn-sm mt-2"
              >View Items</a
            >
          </div>
        </div>

        <div class="col-md-3">
          <div class="card product-card text-center p-3">
            <i class="bi bi-tools" style="font-size: 60px; color: #20c997"></i>
            <h5 class="mt-3 fw-bold">Motorcycle Parts</h5>
            <a href="search.html" class="btn btn-primary btn-sm mt-2"
              >View Items</a
            >
          </div>
        </div>

        <div class="col-md-3">
          <div class="card product-card text-center p-3">
            <i
              class="bi bi-lightbulb"
              style="font-size: 60px; color: #20c997"
            ></i>
            <h5 class="mt-3 fw-bold">Accessories</h5>
            <a href="search.html" class="btn btn-primary btn-sm mt-2"
              >View Items</a
            >
          </div>
        </div>

        <div class="col-md-3">
          <div class="card product-card text-center p-3">
            <i
              class="bi bi-wrench-adjustable-circle"
              style="font-size: 60px; color: #20c997"
            ></i>
            <h5 class="mt-3 fw-bold">Maintenance</h5>
            <a href="search.html" class="btn btn-primary btn-sm mt-2"
              >View Items</a
            >
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-3">
      <small>&copy; 2024 Nimbus. All rights reserved.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
