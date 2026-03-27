<?php
/**
 * Vendor Check Helper
 * Provides functions to check vendor status and permissions
 */

// Check if user is an approved vendor
function is_approved_vendor($conn, $user_id) {
    if (!$user_id) return false;

    $sql = "SELECT u.role, vp.is_approved
            FROM users u
            LEFT JOIN vendor_profiles vp ON u.id = vp.user_id
            WHERE u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return ($row['role'] === 'vendor' && $row['is_approved'] == 1);
    }

    return false;
}

// Check if user has a pending vendor application
function has_pending_vendor_application($conn, $user_id) {
    if (!$user_id) return false;

    $sql = "SELECT vp.id, vp.is_approved
            FROM vendor_profiles vp
            WHERE vp.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return ($row['is_approved'] == 0);
    }

    return false;
}

// Check if user is a vendor (approved or pending)
function is_vendor($conn, $user_id) {
    if (!$user_id) return false;

    $sql = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return ($row['role'] === 'vendor');
    }

    return false;
}

// Get vendor profile
function get_vendor_profile($conn, $user_id) {
    if (!$user_id) return null;

    $sql = "SELECT * FROM vendor_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

// Get vendor status: 'approved', 'pending', 'rejected', 'not_applied'
function get_vendor_status($conn, $user_id) {
    if (!$user_id) return 'not_applied';

    $profile = get_vendor_profile($conn, $user_id);

    if (!$profile) {
        return 'not_applied';
    }

    if ($profile['is_approved'] == 1) {
        return 'approved';
    } elseif ($profile['is_approved'] == 0) {
        return 'pending';
    } else {
        return 'rejected';
    }
}

// Require approved vendor (redirect if not)
function require_approved_vendor($conn, $user_id) {
    if (!is_approved_vendor($conn, $user_id)) {
        header('Location: ../pages/profile.php?error=vendor_required');
        exit;
    }
}

// Get vendor stats
function get_vendor_stats($conn, $user_id) {
    if (!$user_id) return null;

    $stats = [
        'total_products' => 0,
        'total_orders' => 0,
        'total_revenue' => 0
    ];

    // Count products
    $sql = "SELECT COUNT(*) as count FROM products WHERE vendor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stats['total_products'] = $row['count'];
    }

    // Count orders and revenue
    $sql = "SELECT COUNT(DISTINCT o.id) as order_count, SUM(oi.subtotal) as revenue
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE p.vendor_id = ? AND o.status != 'cancelled'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stats['total_orders'] = $row['order_count'] ?? 0;
        $stats['total_revenue'] = $row['revenue'] ?? 0;
    }

    return $stats;
}
?>
