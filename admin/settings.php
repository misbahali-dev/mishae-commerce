<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

$msg = '';
$admin_id = $_SESSION['admin_id'] ?? 1;

// Auto-ensure ad_img column exists in ad_login
$chk_ad = mysqli_query($conn, "SHOW COLUMNS FROM ad_login LIKE 'ad_img'");
if ($chk_ad && mysqli_num_rows($chk_ad) === 0) {
    mysqli_query($conn, "ALTER TABLE ad_login ADD COLUMN ad_img VARCHAR(255) NULL DEFAULT NULL");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['ad_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['ad_email'] ?? ''));
    $password = trim($_POST['ad_password'] ?? '');

    $img_sql = "";
    if (!empty($_FILES['ad_img']['name'])) {
        $ext = strtolower(pathinfo($_FILES['ad_img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $new_name = 'admin_' . $admin_id . '_' . time() . '.' . $ext;
            $upload_path = __DIR__ . '/uploads/' . $new_name;
            if (move_uploaded_file($_FILES['ad_img']['tmp_name'], $upload_path)) {
                $img_sql = ", ad_img = '$new_name'";
                $_SESSION['admin_img'] = $new_name;
            }
        }
    }

    $query = "UPDATE ad_login SET ad_name = '$name', ad_email = '$email'$img_sql";
    if (!empty($password)) {
        $query .= ", ad_password = '$password'";
    }
    $query .= " WHERE id = $admin_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_email'] = $email; 
        $msg = "Administrator profile updated successfully.";
    } else {
        $msg = "Failed to update profile settings.";
    }
}

// Optional Remove Admin Photo
if (isset($_GET['remove_admin_photo'])) {
    mysqli_query($conn, "UPDATE ad_login SET ad_img = NULL WHERE id = $admin_id");
    unset($_SESSION['admin_img']);
    header('Location: settings.php');
    exit();
}

$admin_id = $_SESSION['admin_id'] ?? 1;
$admin_res = mysqli_query($conn, "SELECT * FROM ad_login WHERE id = $admin_id LIMIT 1");
$admin = mysqli_fetch_assoc($admin_res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Store Settings</title>

    <link rel="stylesheet" href="./assets/css/layout.css">
    <link rel="stylesheet" href="./assets/css/settings.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
 
<div class="admin-wrapper">

    <?php include './include/sidebar.php'; ?>

    <div class="main-content">

        <?php include './include/header.php'; ?>

        <main class="page-content">

            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <a href="./home.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Settings</span>
                    </div>

                    <h1 class="page-title">Store &amp; Account Settings</h1>
                    <p class="page-description">Configure your administrative credentials and brand parameters.</p>
                </div>
            </div>

            <?php if (!empty($msg)): 
                $is_set_error = preg_match('/(not|failed|could not|error)/i', $msg);
            ?>
                <div class="alert <?= $is_set_error ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert" data-type="<?= $is_set_error ? 'error' : 'success' ?>">
                    <i class="fa-solid <?= $is_set_error ? 'fa-circle-exclamation' : 'fa-circle-check' ?> me-2"></i> <?php echo htmlspecialchars($msg); ?>
                    <button type="button" class="alert-close-btn" data-bs-dismiss="alert" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="product-card p-4">
                        <h4 class="mb-3" style="font-size: 18px; font-weight: 600;">Administrator Profile</h4>
                        <p class="text-muted small mb-4">Update login credentials for the MiSHAÉ administrative panel.</p>

                        <form action="settings.php" method="POST" enctype="multipart/form-data">
                            <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded">
                                <?php if (!empty($admin['ad_img']) && file_exists(__DIR__ . '/uploads/' . $admin['ad_img'])): ?>
                                    <img src="./uploads/<?php echo htmlspecialchars($admin['ad_img']); ?>" alt="Admin Profile" style="width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 2px solid #C5A880; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                <?php else: ?>
                                    <div style="width: 65px; height: 65px; border-radius: 50%; background: #0E0E10; color: #E8DDD0; display: flex; align-items: center; justify-content: center; font-size: 24px; font-family: Georgia, serif; border: 2px solid #C5A880; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                                        <?php echo strtoupper(substr($admin['ad_name'] ?? 'A', 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-grow-1">
                                    <label class="form-label mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Profile Picture (Optional)</label>
                                    <input type="file" name="ad_img" class="form-control form-control-sm" accept="image/*">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted" style="font-size: 11px;">Select JPG, PNG or WEBP</small>
                                        <?php if (!empty($admin['ad_img'])): ?>
                                            <button type="button" class="btn btn-link text-danger p-0 border-0 small" data-bs-toggle="modal" data-bs-target="#deleteAdminPhotoModal" style="font-size: 11px; text-decoration: none;">
                                                <i class="fa-solid fa-trash-can me-1"></i> Remove Photo
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Admin Display Name</label>
                                <input type="text" name="ad_name" class="form-control" value="<?php echo htmlspecialchars($admin['ad_name'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">Login Email Address</label>
                                <input type="email" name="ad_email" class="form-control" value="<?php echo htmlspecialchars($admin['ad_email'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" style="font-size: 12px; font-weight: 600; text-transform: uppercase;">New Password (leave blank to keep current)</label>
                                <input type="password" name="ad_password" class="form-control" placeholder="Enter new password">
                            </div>

                            <button type="submit" name="update_admin" class="btn-save-settings">
                                Save Settings
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="product-card p-4">
                        <h4 class="mb-3" style="font-size: 18px; font-weight: 600;">Fabric Brand Configuration</h4>
                        <div class="p-3 bg-light rounded mb-3" style="font-size: 13px;">
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <strong>Brand Name:</strong>
                                <span>MiSHAÉ Luxury Fabrics</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <strong>Default Currency:</strong>
                                <span>PKR (Pakistani Rupee)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <strong>Free Delivery Threshold:</strong>
                                <span>Orders above PKR 10,000</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <strong>Active Privilege Coupons:</strong>
                                <span><code>MISHAE15</code> (15% Off), <code>FREESHIP</code></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>WhatsApp Concierge:</strong>
                                <span>+92 300 1234567</span>
                            </div>
                        </div>

                        <a href="/commerce/user/home.php" target="_blank" class="btn btn-outline-dark btn-sm text-uppercase" style="font-size: 11px; letter-spacing: 1px;">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Storefront
                        </a>
                    </div>
                </div>
            </div>

            <!-- Separate Modal to Delete Admin Profile Photo -->
            <div class="modal fade" id="deleteAdminPhotoModal" tabindex="-1" aria-labelledby="deleteAdminPhotoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                    <div class="modal-content" style="border-radius: 12px; border: 1px solid #EAE5DF;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title" id="deleteAdminPhotoModalLabel" style="font-family: 'Playfair Display', serif; font-size: 20px;">Remove Profile Photo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div style="width: 55px; height: 55px; background: #FFF5F5; color: #DC3545; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 15px;">
                                <i class="fa-solid fa-trash-can"></i>
                            </div>
                            <p class="mb-1" style="font-size: 14px; color: #29211f; font-weight: 500;">Are you sure you want to remove your admin profile picture?</p>
                            <small class="text-muted">Your profile will display the default monogram initial instead.</small>
                        </div>
                        <div class="modal-footer border-0 pt-0 justify-content-center" style="gap: 10px;">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; padding: 8px 18px;">Cancel</button>
                            <a href="settings.php?remove_admin_photo=1" class="btn btn-dark" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; padding: 8px 18px; background: #29211f; border-color: #29211f;">Remove Photo</a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <?php include './include/footer.php'; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
