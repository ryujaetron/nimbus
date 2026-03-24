<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? '';

// REGISTRATION
if ($action === 'register') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $response['message'] = 'All fields are required';
        echo json_encode($response);
        exit;
    }

    // Validate names (letters only)
    if (!preg_match('/^[A-Za-z]+$/', $firstName) || !preg_match('/^[A-Za-z]+$/', $lastName)) {
        $response['message'] = 'Names should contain letters only';
        echo json_encode($response);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit;
    }

    // Validate password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $response['message'] = 'Password does not meet requirements';
        echo json_encode($response);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response['message'] = 'Email already registered';
        echo json_encode($response);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, 'customer')");
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Registration successful! Redirecting to login...';
    } else {
        $response['message'] = 'Registration failed. Please try again.';
    }

    $stmt->close();
}

// LOGIN
elseif ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email) || empty($password)) {
        $response['message'] = 'All fields are required';
        echo json_encode($response);
        exit;
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Invalid email or password';
        echo json_encode($response);
        exit;
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        $response['success'] = true;
        $response['message'] = 'Login successful!';
        $response['redirect'] = ($user['role'] === 'admin') ? '../admin/dashboard.php' : '../pages/home.php';
    } else {
        $response['message'] = 'Invalid email or password';
    }

    $stmt->close();
}

else {
    $response['message'] = 'Invalid action';
}

$conn->close();
echo json_encode($response);
?>
