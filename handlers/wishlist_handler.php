<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

$is_guest = !isset($_SESSION['user_id']);
$user_id = $is_guest ? null : $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// Initialize guest wishlist if doesn't exist
if ($is_guest && !isset($_SESSION['guest_wishlist'])) {
    $_SESSION['guest_wishlist'] = [];
}

// ADD TO WISHLIST
if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product';
        echo json_encode($response);
        exit;
    }

    // Check if product exists
    $product_sql = "SELECT id FROM products WHERE id = ? AND is_active = 1";
    $product_stmt = $conn->prepare($product_sql);
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();

    if ($product_result->num_rows === 0) {
        $response['message'] = 'Product not found';
        echo json_encode($response);
        exit;
    }

    if ($is_guest) {
        // Guest wishlist - store in session
        if (in_array($product_id, $_SESSION['guest_wishlist'])) {
            $response['message'] = 'Product already in wishlist';
            echo json_encode($response);
            exit;
        }

        $_SESSION['guest_wishlist'][] = $product_id;
        $_SESSION['wishlist_count'] = count($_SESSION['guest_wishlist']);
    } else {
        // Logged-in user - store in database
        $check_sql = "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $response['message'] = 'Product already in wishlist';
            echo json_encode($response);
            exit;
        }

        $insert_sql = "INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $product_id);

        if (!$insert_stmt->execute()) {
            $response['message'] = 'Failed to add to wishlist';
            echo json_encode($response);
            exit;
        }

        $count_sql = "SELECT COUNT(*) as total FROM wishlists WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $_SESSION['wishlist_count'] = $count_data['total'] ?? 0;
    }

    $response['success'] = true;
    $response['message'] = 'Added to wishlist';
}

// REMOVE FROM WISHLIST
elseif ($action === 'remove') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product';
        echo json_encode($response);
        exit;
    }

    if ($is_guest) {
        // Guest wishlist remove
        $key = array_search($product_id, $_SESSION['guest_wishlist']);
        if ($key !== false) {
            unset($_SESSION['guest_wishlist'][$key]);
            $_SESSION['guest_wishlist'] = array_values($_SESSION['guest_wishlist']); // Re-index
        }
        $_SESSION['wishlist_count'] = count($_SESSION['guest_wishlist']);
    } else {
        // Logged-in user remove
        $delete_sql = "DELETE FROM wishlists WHERE user_id = ? AND product_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $user_id, $product_id);
        $delete_stmt->execute();

        $count_sql = "SELECT COUNT(*) as total FROM wishlists WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $_SESSION['wishlist_count'] = $count_data['total'] ?? 0;
    }

    $response['success'] = true;
    $response['message'] = 'Removed from wishlist';
}

// CHECK IF IN WISHLIST
elseif ($action === 'check') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product';
        echo json_encode($response);
        exit;
    }

    if ($is_guest) {
        // Guest wishlist check
        $in_wishlist = in_array($product_id, $_SESSION['guest_wishlist']);
    } else {
        // Logged-in user check
        $check_sql = "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $in_wishlist = $check_result->num_rows > 0;
    }

    $response['success'] = true;
    $response['in_wishlist'] = $in_wishlist;
}

else {
    $response['message'] = 'Invalid action';
}

$conn->close();
echo json_encode($response);
?>
