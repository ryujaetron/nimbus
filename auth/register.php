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
    <title>Register - Nimbus</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/style.css" />
  </head>
  <body class="custom-bg">
    <div
      class="container d-flex justify-content-center align-items-center min-vh-100"
    >
      <div class="card p-4 shadow" style="width: 450px">
        <h2 class="text-center mb-4">Register - Nimbus</h2>

        <div id="alert-message" class="alert d-none" role="alert"></div>

        <form id="registration-form" class="needs-validation" novalidate>
          <!-- First & Last Name -->
          <div class="row mb-3">
            <div class="col">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" id="firstName" name="firstName" class="form-control" required />
              <div class="invalid-feedback">
                Please enter a valid first name (letters only).
              </div>
            </div>
            <div class="col">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" id="lastName" name="lastName" class="form-control" required />
              <div class="invalid-feedback">
                Please enter a valid last name (letters only).
              </div>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required />
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              required
            />
            <div id="password-help" class="form-text">
              Your password must meet the following requirements:
              <ul>
                <li id="pw-length">Minimum 8 characters</li>
                <li id="pw-uppercase">At least one uppercase letter</li>
                <li id="pw-lowercase">At least one lowercase letter</li>
                <li id="pw-number">At least one number</li>
                <li id="pw-special">
                  At least one special character (e.g., !@#$%)
                </li>
              </ul>
            </div>
            <div class="invalid-feedback" id="password-feedback">
              Password does not meet all requirements.
            </div>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary w-100" id="register-btn">Register</button>

          <div class="text-center mt-3">
            <a href="login.php" class="register-link"
              >Already have an account? Login</a
            >
          </div>
        </form>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
      const alertMsg = document.getElementById('alert-message');
      const registerBtn = document.getElementById('register-btn');
      const form = document.getElementById('registration-form');

      // Override form submit to send to backend
      form.addEventListener("submit", async function (event) {
        event.preventDefault();
        event.stopPropagation();

        // Get all form validation checks from main.js
        let isValid = true;

        const nameRegex = /^[A-Za-z]+$/;
        const firstName = document.getElementById("firstName");
        const lastName = document.getElementById("lastName");
        const email = document.getElementById("email");
        const password = document.getElementById("password");

        // First Name
        if (!nameRegex.test(firstName.value)) {
          firstName.classList.add("is-invalid");
          isValid = false;
        } else {
          firstName.classList.remove("is-invalid");
          firstName.classList.add("is-valid");
        }

        // Last Name
        if (!nameRegex.test(lastName.value)) {
          lastName.classList.add("is-invalid");
          isValid = false;
        } else {
          lastName.classList.remove("is-invalid");
          lastName.classList.add("is-valid");
        }

        // Email
        if (!email.checkValidity()) {
          email.classList.add("is-invalid");
          isValid = false;
        } else {
          email.classList.remove("is-invalid");
          email.classList.add("is-valid");
        }

        // Password
        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!passRegex.test(password.value)) {
          password.classList.add("is-invalid");
          isValid = false;
        } else {
          password.classList.remove("is-invalid");
          password.classList.add("is-valid");
        }

        if (!isValid) {
          form.classList.add("was-validated");
          return false;
        }

        // Disable button and show loading
        registerBtn.disabled = true;
        registerBtn.textContent = 'Registering...';

        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'register');
        formData.append('firstName', firstName.value);
        formData.append('lastName', lastName.value);
        formData.append('email', email.value);
        formData.append('password', password.value);

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

            // Redirect to login after short delay
            setTimeout(() => {
              window.location.href = 'login.php';
            }, 1500);
          } else {
            alertMsg.className = 'alert alert-danger';
            alertMsg.textContent = data.message;
            alertMsg.classList.remove('d-none');

            registerBtn.disabled = false;
            registerBtn.textContent = 'Register';
          }
        } catch (error) {
          alertMsg.className = 'alert alert-danger';
          alertMsg.textContent = 'An error occurred. Please try again.';
          alertMsg.classList.remove('d-none');

          registerBtn.disabled = false;
          registerBtn.textContent = 'Register';
        }
      });
    </script>
  </body>
</html>
