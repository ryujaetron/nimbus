<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=profile');
    exit;
}

// Include database connection
require_once __DIR__ . '/../config/db.php';

// Include helper functions
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/vendor_check.php';

// Set page variables
$page_title = 'My Profile';
$current_page = 'profile';

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Get user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get vendor status
$vendor_status = get_vendor_status($conn, $user_id);
$vendor_profile = get_vendor_profile($conn, $user_id);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'update_profile') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($first_name) || empty($last_name)) {
            $error_msg = 'First name and last name are required.';
        } else {
            $update_sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $first_name, $last_name, $phone, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                $success_msg = 'Profile updated successfully!';
                // Refresh user data
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $error_msg = 'Failed to update profile.';
            }
        }
    }

    if ($_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_msg = 'All password fields are required.';
        } elseif (!password_verify($current_password, $user['password'])) {
            $error_msg = 'Current password is incorrect.';
        } elseif (strlen($new_password) < 6) {
            $error_msg = 'New password must be at least 6 characters.';
        } elseif ($new_password !== $confirm_password) {
            $error_msg = 'New passwords do not match.';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                $success_msg = 'Password changed successfully!';
            } else {
                $error_msg = 'Failed to change password.';
            }
        }
    }

    if ($_POST['action'] === 'apply_vendor') {
        $store_name = trim($_POST['store_name'] ?? '');
        $store_url = trim($_POST['store_url'] ?? '');
        $store_address = trim($_POST['store_address'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Check if already applied
        if ($vendor_status !== 'not_applied') {
            $error_msg = 'You have already submitted a vendor application.';
        } elseif (empty($store_name) || empty($store_url) || empty($store_address)) {
            $error_msg = 'Store name, URL, and address are required.';
        } else {
            // Generate URL-friendly slug
            $store_url = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $store_url));

            // Check if store URL is unique
            $check_sql = "SELECT id FROM vendor_profiles WHERE store_url = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $store_url);
            $check_stmt->execute();

            if ($check_stmt->get_result()->num_rows > 0) {
                $error_msg = 'Store URL already taken. Please choose another.';
            } else {
                // Handle file uploads
                $valid_id_image = null;
                $logo = null;
                $upload_dir = __DIR__ . '/../uploads/vendors/';

                // Create upload directory if not exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Upload Valid ID
                if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['valid_id']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

                    if (in_array($ext, $allowed)) {
                        $valid_id_image = 'id_' . $user_id . '_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['valid_id']['tmp_name'], $upload_dir . $valid_id_image);
                    }
                }

                // Upload Logo
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($ext, $allowed)) {
                        $logo = 'logo_' . $user_id . '_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo);
                    }
                }

                // Insert vendor profile
                $insert_sql = "INSERT INTO vendor_profiles (user_id, store_name, store_url, description, store_address, valid_id_image, logo, is_approved)
                               VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("issssss", $user_id, $store_name, $store_url, $description, $store_address, $valid_id_image, $logo);

                if ($insert_stmt->execute()) {
                    $success_msg = 'Vendor application submitted! Please wait for admin approval.';
                    $vendor_status = 'pending';
                    $vendor_profile = get_vendor_profile($conn, $user_id);
                } else {
                    $error_msg = 'Failed to submit application. Please try again.';
                }
            }
        }
    }
}

// Include header
include __DIR__ . '/../includes/header.php';

// Include navbar
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Page Header -->
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
        </ol>
    </nav>
</div>

<!-- Profile Content -->
<div class="container my-4">
    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= e($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> <?= e($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card profile-card shadow-sm">
                <div class="card-body text-center">
                    <div class="profile-avatar mb-3">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h4 class="fw-bold mb-1"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                    <p class="text-muted mb-2"><?= e($user['email']) ?></p>

                    <!-- Role Badge -->
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge bg-danger mb-3">Admin</span>
                    <?php elseif ($user['role'] === 'vendor'): ?>
                        <span class="badge bg-success mb-3">Vendor</span>
                    <?php else: ?>
                        <span class="badge bg-secondary mb-3">Customer</span>
                    <?php endif; ?>

                    <!-- Vendor Status -->
                    <?php if ($vendor_status === 'pending'): ?>
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-clock"></i> Vendor application pending
                        </div>
                    <?php elseif ($vendor_status === 'approved'): ?>
                        <a href="../vendor/dashboard.php" class="btn btn-success w-100">
                            <i class="bi bi-shop"></i> Vendor Dashboard
                        </a>
                    <?php endif; ?>

                    <hr>

                    <div class="text-start">
                        <p class="mb-2">
                            <i class="bi bi-telephone text-muted me-2"></i>
                            <?= $user['phone'] ? e($user['phone']) : '<span class="text-muted">Not set</span>' ?>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-calendar text-muted me-2"></i>
                            Member since <?= date('M Y', strtotime($user['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-link-45deg"></i> Quick Links</h6>
                    <div class="d-grid gap-2">
                        <a href="order_history.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-clock-history"></i> Order History
                        </a>
                        <a href="wishlist.php" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-heart"></i> My Wishlist
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Forms -->
        <div class="col-lg-8">
            <!-- Edit Profile -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                       value="<?= e($user['first_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                       value="<?= e($user['last_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= e($user['email']) ?>" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control"
                                       value="<?= e($user['phone'] ?? '') ?>" placeholder="09123456789">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-check-lg"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="6">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning mt-3">
                            <i class="bi bi-key"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Vendor Section -->
            <?php if ($user['role'] !== 'admin'): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-shop"></i> Become a Vendor</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($vendor_status === 'approved'): ?>
                            <!-- Approved Vendor -->
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">You are an approved vendor!</h5>
                                <p class="text-muted">Store: <strong><?= e($vendor_profile['store_name']) ?></strong></p>
                                <a href="../vendor/dashboard.php" class="btn btn-success">
                                    <i class="bi bi-speedometer2"></i> Go to Vendor Dashboard
                                </a>
                            </div>

                        <?php elseif ($vendor_status === 'pending'): ?>
                            <!-- Pending Application -->
                            <div class="text-center py-3">
                                <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                                <h5 class="mt-2">Application Pending</h5>
                                <p class="text-muted">Your vendor application is under review. We'll notify you once it's approved.</p>

                                <div class="card bg-light mt-3">
                                    <div class="card-body text-start">
                                        <h6 class="fw-bold">Application Details:</h6>
                                        <p class="mb-1"><strong>Store Name:</strong> <?= e($vendor_profile['store_name']) ?></p>
                                        <p class="mb-1"><strong>Store URL:</strong> <?= e($vendor_profile['store_url']) ?></p>
                                        <p class="mb-0"><strong>Submitted:</strong> <?= date('M d, Y', strtotime($vendor_profile['created_at'])) ?></p>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- Apply as Vendor -->
                            <p class="text-muted mb-3">
                                Start selling your products on CartHive! Fill out the form below to apply as a vendor.
                            </p>

                            <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#vendorModal">
                                <i class="bi bi-shop-window"></i> Apply as Vendor
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Vendor Application Modal -->
<div class="modal fade" id="vendorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-shop"></i> Vendor Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="apply_vendor">

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Store Name <span class="text-danger">*</span></label>
                            <input type="text" name="store_name" class="form-control" required
                                   placeholder="Your Business Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Store URL <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">carthive.com/store/</span>
                                <input type="text" name="store_url" class="form-control" required
                                       placeholder="my-store" pattern="[a-zA-Z0-9\-]+">
                            </div>
                            <small class="text-muted">Only letters, numbers, and hyphens</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Store Address <span class="text-danger">*</span></label>
                            <input type="text" name="store_address" class="form-control" required
                                   placeholder="Complete business address">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Store Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Tell us about your products and business..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valid ID (for verification)</label>
                            <input type="file" name="valid_id" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-muted">JPG, PNG, or PDF (max 5MB)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Store Logo</label>
                            <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                            <small class="text-muted">JPG, PNG, GIF, or WebP</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="vendorTerms" required>
                                <label class="form-check-label" for="vendorTerms">
                                    I agree to the <a href="#" class="text-primary">Vendor Terms & Conditions</a>
                                    and <a href="#" class="text-primary">Marketplace Policy</a>.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send"></i> Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.profile-card {
    border: none;
    border-radius: 12px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #20c997 0%, #17a589 100%);
    border-radius: 50%;
}

.profile-avatar i {
    font-size: 60px;
    color: white;
}

.card {
    border-radius: 12px;
    border: none;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    background-color: var(--bs-light);
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

/* Dark mode adjustments */
[data-bs-theme="dark"] .card-header {
    background-color: #2d2d2d;
    border-bottom-color: #404040;
}

[data-bs-theme="dark"] .profile-card,
[data-bs-theme="dark"] .card {
    background-color: #1e1e1e;
    border-color: #333;
}

[data-bs-theme="dark"] .bg-light {
    background-color: #2d2d2d !important;
}

[data-bs-theme="dark"] .modal-content {
    background-color: #1e1e1e;
    border-color: #333;
}

[data-bs-theme="dark"] .modal-header {
    border-bottom-color: #333;
}

[data-bs-theme="dark"] .modal-footer {
    border-top-color: #333;
}

[data-bs-theme="dark"] .input-group-text {
    background-color: #3d3d3d;
    border-color: #505050;
    color: #e9ecef;
}
</style>

<?php
// Close database connection
$conn->close();

// Include footer
include __DIR__ . '/../includes/footer.php';
?>
