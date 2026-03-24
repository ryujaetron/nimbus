(function () {
  "use strict";

  const form = document.getElementById("registration-form");
  const password = document.getElementById("password");

  // Live password feedback
  password.addEventListener("input", function () {
    const value = password.value;
    const lengthRule = value.length >= 8;
    const upperRule = /[A-Z]/.test(value);
    const lowerRule = /[a-z]/.test(value);
    const numberRule = /[0-9]/.test(value);
    const specialRule = /[\W_]/.test(value);

    document.getElementById("pw-length").style.color = lengthRule
      ? "#20c997"
      : "#000";
    document.getElementById("pw-uppercase").style.color = upperRule
      ? "#20c997"
      : "#000";
    document.getElementById("pw-lowercase").style.color = lowerRule
      ? "#20c997"
      : "#000";
    document.getElementById("pw-number").style.color = numberRule
      ? "#20c997"
      : "#000";
    document.getElementById("pw-special").style.color = specialRule
      ? "#20c997"
      : "#000";
  });

  // Form submission validation
  form.addEventListener("submit", function (event) {
    let isValid = true;

    const nameRegex = /^[A-Za-z]+$/;
    const firstName = document.getElementById("firstName");
    const lastName = document.getElementById("lastName");
    const email = document.getElementById("email");

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
      event.preventDefault();
      event.stopPropagation();
      return false;
    }

    form.classList.add("was-validated");
  });
})();
