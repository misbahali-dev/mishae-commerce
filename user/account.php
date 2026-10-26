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
                <div style="width: 75px; height: 75px; background: #0E0E10; color: #E8DDD0; font-size: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px; font-family: Georgia, serif;">
                    <?php echo strtoupper(substr($customer['cus_name'] ?? 'M', 0, 1)); ?>
                </div>
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

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
