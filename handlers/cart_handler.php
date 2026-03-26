<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

$is_guest = !isset($_SESSION['user_id']);
$user_id = $is_guest ? null : $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// Initialize guest cart if doesn't exist
if ($is_guest && !isset($_SESSION['guest_cart'])) {
    $_SESSION['guest_cart'] = [];
}

// ADD TO CART
if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($product_id <= 0 || $quantity <= 0) {
        $response['message'] = 'Invalid product or quantity';
        echo json_encode($response);
        exit;
    }

    // Check if product exists and has stock
    $product_sql = "SELECT id, name, stock, price, sale_price FROM products WHERE id = ? AND is_active = 1";
    $product_stmt = $conn->prepare($product_sql);
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();

    if ($product_result->num_rows === 0) {
        $response['message'] = 'Product not found';
        echo json_encode($response);
        exit;
    }

    $product = $product_result->fetch_assoc();

    if ($product['stock'] < $quantity) {
        $response['message'] = 'Insufficient stock available';
        echo json_encode($response);
        exit;
    }

    if ($is_guest) {
        // Guest cart - store in session
        $found = false;
        foreach ($_SESSION['guest_cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $new_quantity = $item['quantity'] + $quantity;
                if ($new_quantity > $product['stock']) {
                    $response['message'] = 'Cannot add more than available stock';
                    echo json_encode($response);
                    exit;
                }
                $item['quantity'] = $new_quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['guest_cart'][] = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];
        }

        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['guest_cart'], 'quantity'));
    } else {
        // Logged-in user - store in database
        $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $cart_item = $check_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;

            if ($new_quantity > $product['stock']) {
                $response['message'] = 'Cannot add more than available stock';
                echo json_encode($response);
                exit;
            }

            $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_quantity, $cart_item['id']);
            $update_stmt->execute();
        } else {
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $insert_stmt->execute();
        }

        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $_SESSION['cart_count'] = $count_data['total'] ?? 0;
    }

    $response['success'] = true;
    $response['message'] = 'Product added to cart';
    $response['cart_count'] = $_SESSION['cart_count'];
}

// UPDATE CART QUANTITY
elseif ($action === 'update') {
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($quantity < 0) {
        $response['message'] = 'Invalid quantity';
        echo json_encode($response);
        exit;
    }

    if ($is_guest) {
        // Guest cart update
        if ($quantity === 0) {
            unset($_SESSION['guest_cart'][$cart_id]);
            $_SESSION['guest_cart'] = array_values($_SESSION['guest_cart']); // Re-index array
            $response['message'] = 'Item removed from cart';
        } else {
            if (isset($_SESSION['guest_cart'][$cart_id])) {
                $_SESSION['guest_cart'][$cart_id]['quantity'] = $quantity;
                $response['message'] = 'Cart updated';
            }
        }
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['guest_cart'], 'quantity'));
    } else {
        // Logged-in user cart update
        if ($quantity === 0) {
            $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $cart_id, $user_id);
            $delete_stmt->execute();
            $response['message'] = 'Item removed from cart';
        } else {
            $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $update_stmt->execute();
            $response['message'] = 'Cart updated';
        }

        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $_SESSION['cart_count'] = $count_data['total'] ?? 0;
    }

    $response['success'] = true;
    $response['cart_count'] = $_SESSION['cart_count'];
}

// REMOVE FROM CART
elseif ($action === 'remove') {
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;

    if ($is_guest) {
        // Guest cart remove
        if (isset($_SESSION['guest_cart'][$cart_id])) {
            unset($_SESSION['guest_cart'][$cart_id]);
            $_SESSION['guest_cart'] = array_values($_SESSION['guest_cart']); // Re-index
        }
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['guest_cart'], 'quantity'));
    } else {
        // Logged-in user remove
        $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $cart_id, $user_id);
        $delete_stmt->execute();

        $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        $_SESSION['cart_count'] = $count_data['total'] ?? 0;
    }

    $response['success'] = true;
    $response['message'] = 'Item removed from cart';
    $response['cart_count'] = $_SESSION['cart_count'];
}

// GET CART ITEMS
elseif ($action === 'get') {
    $items = [];
    $total = 0;

    if ($is_guest) {
        // Guest cart - get from session
        if (!empty($_SESSION['guest_cart'])) {
            $product_ids = array_column($_SESSION['guest_cart'], 'product_id');
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

            $cart_sql = "SELECT p.id, p.name, p.price, p.sale_price, p.image, p.stock
                         FROM products p
                         WHERE p.id IN ($placeholders) AND p.is_active = 1";
            $cart_stmt = $conn->prepare($cart_sql);
            $cart_stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();

            $products = [];
            while ($row = $cart_result->fetch_assoc()) {
                $products[$row['id']] = $row;
            }

            foreach ($_SESSION['guest_cart'] as $index => $cart_item) {
                if (isset($products[$cart_item['product_id']])) {
                    $product = $products[$cart_item['product_id']];
                    $price = $product['sale_price'] ? $product['sale_price'] : $product['price'];
                    $subtotal = $price * $cart_item['quantity'];
                    $total += $subtotal;

                    $items[] = [
                        'cart_id' => $index, // Use array index as cart_id for guest
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $price,
                        'original_price' => $product['price'],
                        'sale_price' => $product['sale_price'],
                        'image' => $product['image'],
                        'quantity' => $cart_item['quantity'],
                        'stock' => $product['stock'],
                        'subtotal' => $subtotal
                    ];
                }
            }
        }
    } else {
        // Logged-in user - get from database
        $cart_sql = "SELECT c.id as cart_id, c.quantity,
                            p.id, p.name, p.price, p.sale_price, p.image, p.stock
                     FROM cart c
                     JOIN products p ON c.product_id = p.id
                     WHERE c.user_id = ? AND p.is_active = 1";
        $cart_stmt = $conn->prepare($cart_sql);
        $cart_stmt->bind_param("i", $user_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();

        while ($item = $cart_result->fetch_assoc()) {
            $price = $item['sale_price'] ? $item['sale_price'] : $item['price'];
            $subtotal = $price * $item['quantity'];
            $total += $subtotal;

            $items[] = [
                'cart_id' => $item['cart_id'],
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $price,
                'original_price' => $item['price'],
                'sale_price' => $item['sale_price'],
                'image' => $item['image'],
                'quantity' => $item['quantity'],
                'stock' => $item['stock'],
                'subtotal' => $subtotal
            ];
        }
    }

    $response['success'] = true;
    $response['items'] = $items;
    $response['total'] = $total;
    $response['cart_count'] = count($items);
    $response['is_guest'] = $is_guest;
}

else {
    $response['message'] = 'Invalid action';
}

$conn->close();
echo json_encode($response);
?>
