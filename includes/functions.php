<?php
/**
 * Helper Functions for CartHive
 */

/**
 * Sanitize output for HTML display
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price in Philippine Peso
 */
function format_price($price) {
    return '₱' . number_format($price, 2);
}

/**
 * Get the current price (sale price if available, otherwise regular price)
 */
function get_current_price($product) {
    return $product['sale_price'] ? $product['sale_price'] : $product['price'];
}

/**
 * Check if product is on sale
 */
function is_on_sale($product) {
    return !empty($product['sale_price']) && $product['sale_price'] > 0;
}

/**
 * Calculate discount percentage
 */
function get_discount_percentage($regular_price, $sale_price) {
    if ($sale_price >= $regular_price) return 0;
    return round((($regular_price - $sale_price) / $regular_price) * 100);
}

/**
 * Format date for display
 */
function format_date($date) {
    return date('F j, Y', strtotime($date));
}

/**
 * Format datetime for display
 */
function format_datetime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

/**
 * Get user initials for avatar
 */
function get_user_initials($name) {
    $parts = explode(' ', $name);
    $initials = '';
    foreach ($parts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    return substr($initials, 0, 2);
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 */
function get_flash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get product image URL
 */
function get_product_image($image) {
    if (empty($image)) {
        return '../assets/images/placeholder.png';
    }
    return '../uploads/products/' . $image;
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}
?>
