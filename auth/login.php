<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../pages/home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Nimbus</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body class="custom-bg">
    <div
      class="container d-flex justify-content-center align-items-center"
      style="min-height: 100vh"
    >
      <div class="card p-4 shadow" style="width: 350px">
        <h2 class="text-center mb-4">Nimbus</h2>

        <div id="alert-message" class="alert d-none" role="alert"></div>

        <form id="login-form" class="needs-validation" novalidate>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required />
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              required
            />
            <div class="invalid-feedback">Please enter your password.</div>
          </div>

          <button type="submit" class="btn btn-primary w-100" id="login-btn">Login</button>

          <div class="text-center mt-3">
            <a href="register.php" style="color: #000000">Register Here</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Bootstrap JS + Validation -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      const form = document.getElementById('login-form');
      const alertMsg = document.getElementById('alert-message');
      const loginBtn = document.getElementById('login-btn');

      form.addEventListener('submit', async function(event) {
        event.preventDefault();
        event.stopPropagation();

        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          return;
        }

        // Disable button and show loading
        loginBtn.disabled = true;
        loginBtn.textContent = 'Logging in...';

        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('email', document.getElementById('email').value);
        formData.append('password', document.getElementById('password').value);

        try {
          const response = await fetch('../handlers/auth_handler.php', {
            method: 'POST',
            body: formData
          });

          const data = await response.json();

          if (data.success) {
            alertMsg.className = 'alert alert-success';
            alertMsg.textContent = data.message;
            alertMsg.classList.remove('d-none');

            // Redirect after short delay
            setTimeout(() => {
              window.location.href = data.redirect;
            }, 1000);
          } else {
            alertMsg.className = 'alert alert-danger';
            alertMsg.textContent = data.message;
            alertMsg.classList.remove('d-none');

            loginBtn.disabled = false;
            loginBtn.textContent = 'Login';
          }
        } catch (error) {
          alertMsg.className = 'alert alert-danger';
          alertMsg.textContent = 'An error occurred. Please try again.';
          alertMsg.classList.remove('d-none');

          loginBtn.disabled = false;
          loginBtn.textContent = 'Login';
        }
      });
    </script>
  </body>
</html>
