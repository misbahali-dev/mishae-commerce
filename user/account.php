<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$cus_id = (int)$_SESSION['customer_id'];

// Auto-ensure cus_img column exists
$chk_col = mysqli_query($conn, "SHOW COLUMNS FROM customers LIKE 'cus_img'");
if ($chk_col && mysqli_num_rows($chk_col) === 0) {
    mysqli_query($conn, "ALTER TABLE customers ADD COLUMN cus_img VARCHAR(255) NULL DEFAULT NULL");
}

// Handle Optional Profile Picture Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cus_img'])) {
    if (!empty($_FILES['cus_img']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cus_img']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed, true)) {
            $new_name = 'user_' . $cus_id . '_' . time() . '.' . $ext;
            $upload_path = __DIR__ . '/../admin/uploads/' . $new_name;
            if (move_uploaded_file($_FILES['cus_img']['tmp_name'], $upload_path)) {
                mysqli_query($conn, "UPDATE customers SET cus_img = '$new_name' WHERE cus_id = $cus_id");
                $_SESSION['customer_img'] = $new_name;
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Profile picture updated successfully.'
                ];
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'danger',
                    'text' => 'Failed to save uploaded picture. Please try again.'
                ];
            }
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'warning',
                'text' => 'Invalid image format. Allowed formats: JPG, PNG, WEBP.'
            ];
        }
    }
    header('Location: account.php');
    exit();
}

// Optional Remove Photo
if (isset($_GET['remove_photo'])) {
    mysqli_query($conn, "UPDATE customers SET cus_img = NULL WHERE cus_id = $cus_id");
    unset($_SESSION['customer_img']);
    $_SESSION['flash_message'] = [
        'type' => 'info',
        'text' => 'Profile picture removed.'
    ];
    header('Location: account.php');
    exit();
}

$cus_res = mysqli_query($conn, "SELECT * FROM customers WHERE cus_id = $cus_id LIMIT 1");
$customer = mysqli_fetch_assoc($cus_res);

// Fetch Customer Orders
$orders_res = mysqli_query($conn, "SELECT * FROM orders WHERE cus_id = $cus_id ORDER BY ord_id DESC");
$order_count = mysqli_num_rows($orders_res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body style="background: #FAF8F5;">

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<main class="container py-5" style="min-height: 75vh;">
    <!-- Flash Notifications -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mishae-alert mishae-alert-<?php echo $_SESSION['flash_message']['type']; ?>">
            <span><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['flash_message']['text']); ?></span>
            <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close Alert">&times;</button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="bg-white p-4 rounded border shadow-sm text-center">
                <div class="position-relative d-inline-block mb-3">
                    <?php if (!empty($customer['cus_img']) && file_exists(__DIR__ . '/../admin/uploads/' . $customer['cus_img'])): ?>
                        <img src="../admin/uploads/<?php echo htmlspecialchars($customer['cus_img']); ?>" alt="<?php echo htmlspecialchars($customer['cus_name']); ?>" style="width: 85px; height: 85px; border-radius: 50%; object-fit: cover; border: 3px solid #C5A880; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div style="width: 85px; height: 85px; background: #0E0E10; color: #E8DDD0; font-size: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-family: Georgia, serif; border: 3px solid #C5A880; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
                            <?php echo strtoupper(substr($customer['cus_name'] ?? 'M', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Optional Profile Photo Selection Form -->
                <form action="account.php" method="POST" enctype="multipart/form-data" class="mb-3">
                    <label class="btn btn-outline-dark btn-sm text-uppercase mb-1" style="font-size: 10px; letter-spacing: 1px; cursor: pointer; padding: 5px 14px; border-radius: 20px;">
                        <i class="fas fa-camera mr-1"></i> <?php echo !empty($customer['cus_img']) ? 'Change Photo' : 'Select Photo (Optional)'; ?>
                        <input type="file" name="cus_img" accept="image/*" style="display: none;" onchange="this.form.submit();">
                    </label>
                    <small class="d-block text-muted" style="font-size: 11px;">Optional JPG, PNG or WEBP</small>
                    <?php if (!empty($customer['cus_img'])): ?>
                        <div>
                            <button type="button" class="btn btn-link text-danger p-0 border-0 small mt-1" data-toggle="modal" data-target="#deleteUserPhotoModal" style="font-size: 11px; text-decoration: none;">
                                <i class="fas fa-trash-alt mr-1"></i> Remove Photo
                            </button>
                        </div>
                    <?php endif; ?>
                </form>

                <h4 style="font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 5px;">
                    <?php echo htmlspecialchars($customer['cus_name']); ?>
                </h4>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($customer['cus_email']); ?></p>

                <div class="text-left pt-3 border-top small">
                    <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($customer['cus_phone'] ?? 'Not specified'); ?></p>
                    <p class="mb-2"><strong>Client Since:</strong> <?php echo date('M Y', strtotime($customer['cus_date'] ?? 'now')); ?></p>
                    <p class="mb-0"><strong>Total Orders:</strong> <?php echo $order_count; ?></p>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <a href="logout.php" class="btn btn-outline-danger btn-sm btn-block text-uppercase" style="font-size: 11px; letter-spacing: 1px;">
                        <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Orders List -->
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded border shadow-sm">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 20px;">
                    Your Order History
                </h3>

                <?php if ($order_count > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="thead-light">
                                <tr style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($orders_res)): ?>
                                    <tr style="font-size: 13px;">
                                        <td><strong>#ORD-<?php echo $order['ord_id']; ?></strong></td>
                                        <td><?php echo date('M d, Y', strtotime($order['ord_date'])); ?></td>
                                        <td class="font-weight-bold">PKR <?php echo number_format($order['ord_total']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo ($order['ord_status'] === 'Delivered') ? 'success' : (($order['ord_status'] === 'Cancelled') ? 'danger' : 'warning'); ?> px-2 py-1 text-uppercase" style="font-size: 10px;">
                                                <?php echo htmlspecialchars($order['ord_status']); ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($order['pay_method']); ?></small></td>
                                        <td>
                                            <a href="order-confirmation.php?ord_id=<?php echo $order['ord_id']; ?>" class="btn btn-sm btn-outline-dark" style="font-size: 11px;">
                                                Receipt
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-box-open text-muted mb-3" style="font-size: 38px;"></i>
                        <h5>No Previous Orders</h5>
                        <p class="text-muted small">You haven't placed any fabric orders with MiSHAÉ yet.</p>
                        <a href="products.php" class="btn btn-dark text-uppercase btn-sm px-4 py-2 mt-2" style="font-size: 11px; letter-spacing: 1px;">
                            Start Shopping &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Separate Modal to Delete User Profile Photo -->
<div class="modal fade" id="deleteUserPhotoModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserPhotoModalLabel" aria-hidden="true" style="z-index: 10600 !important;">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 400px; z-index: 10601 !important;">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid #EAE5DF; box-shadow: 0 20px 50px rgba(14, 14, 16, 0.25);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="deleteUserPhotoModalLabel" style="font-family: 'Playfair Display', serif; font-size: 20px;">Remove Profile Photo</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="if(window.jQuery){ jQuery('#deleteUserPhotoModal').modal('hide'); }" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <div style="width: 55px; height: 55px; background: #FFF5F5; color: #DC3545; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 15px;">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <p class="mb-1" style="font-size: 14px; color: #29211f; font-weight: 500;">Are you sure you want to remove your profile picture?</p>
                <small class="text-muted">Your account will display your initial monogram instead.</small>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center" style="gap: 10px;">
                <button type="button" class="btn btn-light" data-dismiss="modal" onclick="if(window.jQuery){ jQuery('#deleteUserPhotoModal').modal('hide'); }" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; padding: 8px 18px;">Cancel</button>
                <a href="account.php?remove_photo=1" class="btn btn-dark" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase; padding: 8px 18px; background: #29211f; border-color: #29211f;">Remove Photo</a>
            </div>
        </div>
    </div>
</div>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
