<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Page</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body class="custom-bg">
    <div
      class="container d-flex justify-content-center align-items-center min-vh-100"
    >
      <div class="card p-4 shadow" style="width: 450px">
        <h2 class="text-center mb-4">Register</h2>

        <form id="registration-form" class="needs-validation" novalidate>
          <!-- First & Last Name -->
          <div class="row mb-3">
            <div class="col">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" id="firstName" class="form-control" required />
              <div class="invalid-feedback">
                Please enter a valid first name (letters only).
              </div>
            </div>
            <div class="col">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" id="lastName" class="form-control" required />
              <div class="invalid-feedback">
                Please enter a valid last name (letters only).
              </div>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control" required />
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              id="password"
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
          <button type="submit" class="btn btn-primary w-100">Register</button>

          <div class="text-center mt-3">
            <a href="index.html" class="register-link"
              >Already have an account? Login</a
            >
          </div>
        </form>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
  </body>
</html>
