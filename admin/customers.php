<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

$query = "SELECT customers.*, COUNT(orders.ord_id) AS total_orders, COALESCE(SUM(orders.ord_total), 0) AS total_spent 
          FROM customers 
          LEFT JOIN orders ON customers.cus_id = orders.cus_id 
          GROUP BY customers.cus_id 
          ORDER BY customers.cus_id DESC";
$res = mysqli_query($conn, $query);

$totalCustomers = mysqli_num_rows($res);

// Count active buyers
$activeBuyersRes = mysqli_query($conn, "SELECT COUNT(DISTINCT cus_id) FROM orders");
$totalBuyers = $activeBuyersRes ? mysqli_fetch_row($activeBuyersRes)[0] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Clients &amp; Customers</title>

    <link rel="stylesheet" href="./assets/css/layout.css">
    <link rel="stylesheet" href="./assets/css/customers.css">
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
                        <span>Customers</span>
                    </div>

                    <h1 class="page-title">Client Directory</h1>
                    <p class="page-description">Registered patrons, purchasing history, and communication channels.</p>
                </div>
            </div>

            <div class="customer-summary">

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p>Total Registered</p>
                        <h3><?php echo $totalCustomers; ?></h3>
                    </div>
                </div>

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div>
                        <p>Purchasing Clients</p>
                        <h3><?php echo $totalBuyers; ?></h3>
                    </div>
                </div>

                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-gem"></i>
                    </div>
                    <div>
                        <p>Patronage Status</p>
                        <h3 style="font-size: 18px; color: #8b6b30;">VIP Concierge</h3>
                    </div>
                </div>

            </div>

            <div class="product-card product-table-card customer-table-card">

                <div class="product-table-header">
                    <div>
                        <h2>All Clients</h2>
                        <p>Customer contact details, total orders placed, and lifetime spend.</p>
                    </div>

                    <div class="product-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="customerSearch" placeholder="Search by name, email or phone...">
                    </div>
                </div>

                <div class="product-table-wrapper">
                    <table class="premium-product-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Email Address</th>
                                <th>WhatsApp / Phone</th>
                                <th>Orders</th>
                                <th>Lifetime Value</th>
                                <th>Registered</th>
                            </tr>
                        </thead>

                        <tbody id="customerTableBody">
                        <?php
                        $i = 1;

                        while ($row = mysqli_fetch_assoc($res)) {
                            $name = $row['cus_name'];
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>

                                <td>
                                    <div class="customer-info">
                                        <div class="customer-avatar">
                                            <?php echo htmlspecialchars(strtoupper(substr($name, 0, 1))); ?>
                                        </div>

                                        <div class="customer-details">
                                            <strong><?php echo htmlspecialchars($name); ?></strong>
                                            <span>Client #<?php echo (int)$row['cus_id']; ?></span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($row['cus_email']); ?>" class="customer-email-link">
                                        <?php echo htmlspecialchars($row['cus_email']); ?>
                                    </a>
                                </td>

                                <td>
                                    <?php if (!empty($row['cus_phone'])): ?>
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $row['cus_phone']); ?>" target="_blank" class="text-decoration-none text-dark">
                                            <i class="fab fa-whatsapp text-success me-1"></i>
                                            <?php echo htmlspecialchars($row['cus_phone']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?php echo (int)$row['total_orders']; ?> Orders
                                    </span>
                                </td>

                                <td class="order-price">
                                    PKR <?php echo number_format((float)$row['total_spent']); ?>
                                </td>

                                <td>
                                    <small class="text-muted">
                                        <?php echo date('M d, Y', strtotime($row['cus_date'] ?? 'now')); ?>
                                    </small>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if ($totalCustomers == 0) { ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:40px; color: #888;">
                                    No registered clients yet.
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="product-table-footer">
                    <span>Total clients: <?php echo $totalCustomers; ?></span>
                    <span>MiSHAÉ Luxury Patron Database</span>
                </div>

            </div>

        </main>
    </div>

    <?php include './include/footer.php'; ?>

</div>

<script>
document.getElementById('customerSearch')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#customerTableBody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>
