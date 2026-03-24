<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body class="custom-bg">
    <div
      class="container d-flex justify-content-center align-items-center"
      style="min-height: 100vh"
    >
      <div class="card p-4 shadow" style="width: 350px">
        <h2 class="text-center mb-4">CartHive</h2>

        <form id="login-form" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" required />
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              id="password"
              class="form-control"
              required
            />
            <div class="invalid-feedback">Please enter your password.</div>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>

          <div class="text-center mt-3">
            <a href="register.html" style="color: #00000">Register Here</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Bootstrap JS + Validation -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Bootstrap 5 validation
      (function () {
        "use strict";
        const forms = document.querySelectorAll(".needs-validation");
        Array.from(forms).forEach(function (form) {
          form.addEventListener(
            "submit",
            function (event) {
              if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add("was-validated");
            },
            false
          );
        });
      })();
    </script>
  </body>
</html>
