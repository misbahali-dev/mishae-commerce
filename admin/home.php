<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// Real Database Metrics
$cat_count = 0;
$c_res = mysqli_query($conn, "SELECT COUNT(*) FROM category WHERE cat_status = 1");
if ($c_res) $cat_count = mysqli_fetch_row($c_res)[0];

$pro_count = 0;
$p_res = mysqli_query($conn, "SELECT COUNT(*) FROM product");
if ($p_res) $pro_count = mysqli_fetch_row($p_res)[0];

$ord_count = 0;
$o_res = mysqli_query($conn, "SELECT COUNT(*) FROM orders");
if ($o_res) $ord_count = mysqli_fetch_row($o_res)[0];

$cus_count = 0;
$cu_res = mysqli_query($conn, "SELECT COUNT(*) FROM customers");
if ($cu_res) $cus_count = mysqli_fetch_row($cu_res)[0];

$revenue = 0;
$r_res = mysqli_query($conn, "SELECT COALESCE(SUM(ord_total), 0) FROM orders");
if ($r_res) $revenue = mysqli_fetch_row($r_res)[0];

// Fetch Recent Orders
$recent_orders = mysqli_query($conn, "SELECT orders.*, customers.cus_name 
                                      FROM orders 
                                      LEFT JOIN customers ON orders.cus_id = customers.cus_id 
                                      ORDER BY orders.ord_id DESC LIMIT 5");

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Admin Dashboard</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS Files -->
    <link rel="stylesheet" href="/commerce/admin/assets/css/layout.css">
    <link rel="stylesheet" href="/commerce/admin/assets/css/home.css">
</head>

<body>

    <?php include './include/sidebar.php'; ?>

    <!-- Main page -->
    <main class="main-content">
        <?php include './include/header.php'; ?>

        <section class="dashboard-content">

            <!-- Welcome section -->
            <div class="welcome-section">
                <div class="welcome-text">
                    <p class="welcome-small">ATELIER EXECUTIVE OVERVIEW</p>
                    <h1 id="greeting">Welcome Back, <?php echo htmlspecialchars($admin_name); ?></h1>
                    <p>
                        Here is the live performance overview of your MiSHAÉ luxury fabric store today.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="/commerce/admin/product/addpro.php" class="primary-btn">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Product</span>
                    </a>
                </div>
            </div>

            <!-- Statistics cards -->
            <section class="stats-grid" aria-label="Store statistics">

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <span class="stat-change" style="color: #2E7D32;">
                            <i class="fa-solid fa-circle-check"></i> Active
                        </span>
                    </div>
                    <h2><?php echo number_format($cat_count); ?></h2>
                    <p>Active Collections</p>
                    <span class="stat-note">Women, Men &amp; Kids</span>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="fa-solid fa-shirt"></i>
                        </div>
                        <span class="stat-change" style="color: #2E7D32;">
                            <i class="fa-solid fa-boxes-stacked"></i> In Store
                        </span>
                    </div>
                    <h2><?php echo number_format($pro_count); ?></h2>
                    <p>Total Products</p>
                    <span class="stat-note">Luxury fabrics &amp; ensembles</span>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <span class="stat-change" style="color: #2E7D32;">
                            <i class="fa-solid fa-receipt"></i> Lifetime
                        </span>
                    </div>
                    <h2><?php echo number_format($ord_count); ?></h2>
                    <p>Total Orders</p>
                    <span class="stat-note">Client purchases</span>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-icon">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <span class="stat-change" style="color: #9D7E53;">
                            PKR
                        </span>
                    </div>
                    <h2>PKR <?php echo number_format($revenue); ?></h2>
                    <p>Store Revenue</p>
                    <span class="stat-note">From completed orders</span>
                </article>

            </section>

            <!-- Dashboard lower section -->
            <section class="dashboard-grid">

                <!-- Store Highlights Card -->
                <article class="dashboard-card">
                    <div class="card-header">
                        <div>
                            <h2>Store Health &amp; Activity</h2>
                            <p class="card-subtitle">Platform Status &amp; Live Metrics</p>
                        </div>
                        <span class="chart-label" style="background: #E8F5E9; color: #2E7D32;">System Online</span>
                    </div>

                    <div style="padding: 20px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span><i class="fa-solid fa-users text-muted mr-2" style="width: 20px;"></i> Registered Clients</span>
                            <strong><?php echo number_format($cus_count); ?> Clients</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span><i class="fa-solid fa-store text-muted mr-2" style="width: 20px;"></i> Storefront Status</span>
                            <a href="/commerce/user/home.php" target="_blank" style="color: #9D7E53; text-decoration: none; font-weight: 500;">Visit Public Storefront &rarr;</a>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                            <span><i class="fa-solid fa-shield-halved text-muted mr-2" style="width: 20px;"></i> Database Charset</span>
                            <span class="badge" style="background: #FAF8F5; color: #222; border: 1px solid #ddd; padding: 4px 8px; font-size: 11px;">UTF-8 Unicode</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                            <span><i class="fa-solid fa-truck text-muted mr-2" style="width: 20px;"></i> Shipping Coverage</span>
                            <span>Nationwide Pakistan (COD)</span>
                        </div>
                    </div>
                </article>

                <!-- Recent orders -->
                <article class="dashboard-card">
                    <div class="card-header">
                        <div>
                            <h2>Recent Orders</h2>
                            <p class="card-subtitle">Latest store transactions</p>
                        </div>
                        <a href="/commerce/admin/orders.php" class="text-link">View all &rarr;</a>
                    </div>

                    <div class="orders-list">
                        <?php if ($recent_orders && mysqli_num_rows($recent_orders) > 0): ?>
                            <?php while ($ord = mysqli_fetch_assoc($recent_orders)): 
                                $status = $ord['ord_status'] ?? 'Pending';
                                $status_class = strtolower($status);
                            ?>
                                <div class="order">
                                    <div class="order-left">
                                        <div class="order-icon">
                                            <i class="fa-solid fa-bag-shopping"></i>
                                        </div>
                                        <div class="order-info">
                                            <strong>#ORD-<?php echo $ord['ord_id']; ?></strong>
                                            <small><?php echo htmlspecialchars($ord['cus_name'] ?? 'Client'); ?> &bull; PKR <?php echo number_format($ord['ord_total']); ?></small>
                                        </div>
                                    </div>
                                    <span class="status <?php echo htmlspecialchars($status_class); ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px 10px; color: #888;">
                                <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
                                <p style="font-size: 13px; margin: 0;">No client orders received yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

            </section>

        </section>
    </main>

    <?php include './include/footer.php'; ?>

</body>
</html>
