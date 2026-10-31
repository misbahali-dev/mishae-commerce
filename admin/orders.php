<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = (int)($_POST['ord_id'] ?? 0);
    $new_status = mysqli_real_escape_string($conn, trim($_POST['ord_status'] ?? 'Pending'));
    $pay_status = mysqli_real_escape_string($conn, trim($_POST['pay_status'] ?? 'Pending'));

    if ($order_id > 0) {
        $update_query = "UPDATE orders SET ord_status = '$new_status', pay_status = '$pay_status' WHERE ord_id = $order_id";
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['admin_msg'] = "Order #ORD-$order_id status successfully updated to $new_status.";
        }
    }
    header('Location: orders.php');
    exit();
}

$query = "SELECT orders.*, customers.cus_name, customers.cus_email, customers.cus_phone 
          FROM orders 
          LEFT JOIN customers ON orders.cus_id = customers.cus_id 
          ORDER BY orders.ord_id DESC";
$res = mysqli_query($conn, $query);

$totalOrders = mysqli_num_rows($res);

$pendingRes = mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE ord_status = 'Pending'");
$totalPending = $pendingRes ? mysqli_fetch_row($pendingRes)[0] : 0;

$shippedRes = mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE ord_status = 'Shipped'");
$totalShipped = $shippedRes ? mysqli_fetch_row($shippedRes)[0] : 0;

$deliveredRes = mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE ord_status = 'Delivered'");
$totalDelivered = $deliveredRes ? mysqli_fetch_row($deliveredRes)[0] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Manage Orders</title>

    <link rel="stylesheet" href="./assets/css/layout.css">
    <link rel="stylesheet" href="./assets/css/orders.css">
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
                        <span>Orders</span>
                    </div>

                    <h1 class="page-title">Client Orders</h1>
                    <p class="page-description">Manage and dispatch client fabric orders for MiSHAÉ.</p>
                </div>
            </div>

            <?php if (isset($_SESSION['admin_msg'])): 
                $ord_msg = (string)$_SESSION['admin_msg'];
                $is_ord_error = preg_match('/(not|failed|could not|error)/i', $ord_msg);
            ?>
                <div class="alert <?= $is_ord_error ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show" role="alert" data-type="<?= $is_ord_error ? 'error' : 'success' ?>">
                    <i class="fa-solid <?= $is_ord_error ? 'fa-circle-exclamation' : 'fa-circle-check' ?> me-2"></i> <?php echo htmlspecialchars($ord_msg); ?>
                    <button type="button" class="alert-close-btn" data-bs-dismiss="alert" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['admin_msg']); ?>
            <?php endif; ?>

            <div class="product-summary order-summary">

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <p>Total Orders</p>
                        <h3><?php echo $totalOrders; ?></h3>
                    </div>
                </div>

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p>Pending Orders</p>
                        <h3><?php echo $totalPending; ?></h3>
                    </div>
                </div>

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div>
                        <p>Shipped Orders</p>
                        <h3><?php echo $totalShipped; ?></h3>
                    </div>
                </div>

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <p>Delivered Orders</p>
                        <h3><?php echo $totalDelivered; ?></h3>
                    </div>
                </div>

            </div>

            <div class="product-card product-table-card order-table-card">

                <div class="product-table-header">
                    <div>
                        <h2>All Orders</h2>
                        <p>View customer purchases, change status, and inspect items.</p>
                    </div>

                    <div class="product-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="orderSearch" placeholder="Search orders by ID or customer...">
                    </div>
                </div>

                <div class="product-table-wrapper">
                    <table class="premium-product-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Ref</th>
                                <th>Client Details</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="orderTableBody">
                        <?php
                        $i = 1;

                        while ($row = mysqli_fetch_assoc($res)) {
                            $order_id = (int)$row['ord_id'];
                            $status = $row['ord_status'] ?? 'Pending';
                            $statusClass = strtolower($status);
                            $payment = $row['pay_status'] ?? 'Pending';
                            $paymentClass = strtolower($payment);

                            // Fetch items for this order
                            $items_q = mysqli_query($conn, "SELECT order_items.*, product.pro_name 
                                                            FROM order_items 
                                                            LEFT JOIN product ON order_items.pro_id = product.pro_id 
                                                            WHERE order_items.ord_id = $order_id");
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>

                                <td>
                                    <div class="order-details">
                                        <strong>#ORD-<?php echo $order_id; ?></strong>
                                    </div>
                                </td>

                                <td>
                                    <strong><?php echo htmlspecialchars($row['cus_name'] ?? 'Guest Client'); ?></strong>
                                    <div style="font-size: 11px; color: #777;">
                                        <?php echo htmlspecialchars($row['cus_email'] ?? ''); ?>
                                    </div>
                                </td>

                                <td>
                                    <?php echo date('M d, Y', strtotime($row['ord_date'])); ?>
                                </td>

                                <td class="order-price">
                                    PKR <?php echo number_format((float)$row['ord_total']); ?>
                                </td>

                                <td>
                                    <span class="order-status <?php echo htmlspecialchars($statusClass); ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="payment-status <?php echo htmlspecialchars($paymentClass); ?>">
                                        <?php echo htmlspecialchars($payment); ?>
                                    </span>
                                    <div style="font-size: 10px; color: #888;"><?php echo htmlspecialchars($row['pay_method'] ?? 'COD'); ?></div>
                                </td>

                                <td>
                                    <!-- Status Update Modal Trigger -->
                                    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editOrderModal<?php echo $order_id; ?>" style="font-size: 11px;">
                                        Manage
                                    </button>

                                    <!-- Edit Order Modal -->
                                    <div class="modal fade" id="editOrderModal<?php echo $order_id; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="orders.php" method="POST">
                                                    <input type="hidden" name="ord_id" value="<?php echo $order_id; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Manage Order #ORD-<?php echo $order_id; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold" style="font-size: 12px;">Order Status</label>
                                                            <select name="ord_status" class="form-select form-select-sm">
                                                                <option value="Pending" <?php echo ($status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="Processing" <?php echo ($status === 'Processing') ? 'selected' : ''; ?>>Processing in Atelier</option>
                                                                <option value="Shipped" <?php echo ($status === 'Shipped') ? 'selected' : ''; ?>>Shipped / Out for Delivery</option>
                                                                <option value="Delivered" <?php echo ($status === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                                                <option value="Cancelled" <?php echo ($status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold" style="font-size: 12px;">Payment Status</label>
                                                            <select name="pay_status" class="form-select form-select-sm">
                                                                <option value="Pending" <?php echo ($payment === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="Paid" <?php echo ($payment === 'Paid') ? 'selected' : ''; ?>>Paid / Collected</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3 p-2 bg-light rounded" style="font-size: 12px;">
                                                            <strong>Shipping Destination:</strong>
                                                            <pre class="m-0" style="font-size: 11px; white-space: pre-wrap;"><?php echo htmlspecialchars($row['ship_address']); ?></pre>
                                                        </div>

                                                        <div class="mb-2" style="font-size: 12px;">
                                                            <strong>Ordered Items:</strong>
                                                            <ul class="list-unstyled mt-1">
                                                                <?php while ($it = mysqli_fetch_assoc($items_q)): ?>
                                                                    <li class="border-bottom py-1 d-flex justify-content-between">
                                                                        <span><?php echo htmlspecialchars($it['pro_name'] ?? 'Product Removed / Archived'); ?> &times; <?php echo (int)$it['item_quan']; ?></span>
                                                                        <span>PKR <?php echo number_format($it['item_price'] * $it['item_quan']); ?></span>
                                                                    </li>
                                                                <?php endwhile; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_order_status" class="btn btn-dark btn-sm">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if ($totalOrders == 0) { ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding:30px;">
                                    No orders placed yet.
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="product-table-footer">
                    <span>Total recorded orders: <?php echo $totalOrders; ?></span>
                    <span>MiSHAÉ Luxury Administration</span>
                </div>

            </div>

        </main>
    </div>

    <?php include './include/footer.php'; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('orderSearch')?.addEventListener('keyup', function(){
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#orderTableBody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>
