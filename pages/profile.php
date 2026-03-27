<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>

    <!-- BOOTSTRAP -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <style>
      body {
        background-color: #f8f9fa;
      }
      .profile-card {
        max-width: 700px;
        margin: auto;
      }
      .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #0d6efd;
      }
    </style>
  </head>
  <body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">MyShop</a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navMenu"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="home.html">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="product.html">Products</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="checkout.html">Checkout</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="profile.html">Profile</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- PROFILE CONTENT -->
    <div class="container my-5">
      <div class="card profile-card shadow">
        <div class="card-body text-center">
          <img src="https://via.placeholder.com/120" class="profile-pic mb-3" />

          <h4 class="fw-bold">User Profile</h4>

          <form id="profileForm" class="mt-4">
            <div class="mb-3 text-start">
              <label class="form-label">Full Name</label>
              <input
                type="text"
                class="form-control"
                required
                value="Juan Dela Cruz"
              />
            </div>

            <div class="mb-3 text-start">
              <label class="form-label">Address</label>
              <input
                type="text"
                class="form-control"
                required
                value="Makati City, Philippines"
              />
            </div>

            <div class="mb-3 text-start">
              <label class="form-label">Phone Number</label>
              <input
                type="text"
                class="form-control"
                required
                value="09123456789"
              />
            </div>

            <button type="submit" class="btn btn-primary w-100 my-2">
              Save Changes
            </button>
          </form>

          <button
            class="btn btn-success w-100 mt-3"
            data-bs-toggle="modal"
            data-bs-target="#vendorModal"
          >
            Apply as Vendor
          </button>
        </div>
      </div>
    </div>

    <!-- VENDOR APPLICATION MODAL -->
    <div class="modal fade" id="vendorModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title fw-bold">Vendor Application</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <form id="vendorForm">
              <div class="mb-3">
                <label class="form-label">Business Name</label>
                <input type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Business Address</label>
                <input type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Business URL</label>
                <input type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Type of Products</label>
                <input type="text" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Upload Valid ID</label>
                <input type="file" class="form-control" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Upload Logo</label>
                <input type="file" class="form-control" required />
              </div>

              <!-- TERMS CHECKBOX -->
              <div class="form-check mb-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="termsCheck"
                  required
                />
                <label class="form-check-label" for="termsCheck">
                  I agree to the
                  <a href="#" class="text-primary">Terms & Conditions</a>.
                </label>
              </div>

              <button type="submit" class="btn btn-success w-100">
                Submit Application
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS -->
    <script>
      document
        .getElementById("profileForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          alert("Profile Updated Successfully!");
        });

      document
        .getElementById("vendorForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          alert("Vendor Application Submitted! Wait for admin approval.");
          var modal = bootstrap.Modal.getInstance(
            document.getElementById("vendorModal")
          );
          modal.hide();
        });

      document
        .getElementById("vendorForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();

          if (!document.getElementById("termsCheck").checked) {
            alert("You must agree to the Terms & Conditions.");
            return;
          }

          alert("Vendor Application Submitted! Wait for admin approval.");

          var modal = bootstrap.Modal.getInstance(
            document.getElementById("vendorModal")
          );
          modal.hide();
        });
    </script>
  </body>
</html>
