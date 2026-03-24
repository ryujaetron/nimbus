<?php
session_start();

// If user is logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: pages/home.php');
    }
    exit;
}

// Otherwise redirect to login page
header('Location: auth/login.php');
exit;
?>
