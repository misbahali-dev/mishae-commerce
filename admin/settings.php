<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['ad_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['ad_email'] ?? ''));
    $password = trim($_POST['ad_password'] ?? '');

    $admin_id = $_SESSION['admin_id'] ?? 1;

    $query = "UPDATE ad_login SET ad_name = '$name', ad_email = '$email'";
    if (!empty($password)) {
        $query .= ", ad_password = '$password'";
    }
    $query .= " WHERE id = $admin_id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['admin_name'] = $name;
        $_SESSION['admin_email'] = $email;
        $msg = "Administrator credentials updated successfully.";
    } else {
        $msg = "Failed to update profile settings.";
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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

                        <form action="settings.php" method="POST">
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

                            <button type="submit" name="update_admin" class="btn btn-dark text-uppercase" style="font-size: 11px; letter-spacing: 1.5px; padding: 10px 20px;">
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

        </main>
    </div>

    <?php include './include/footer.php'; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
