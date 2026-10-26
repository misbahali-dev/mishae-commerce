<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure database connection
if (!isset($conn) || !$conn) {
    $config_paths = [
        __DIR__ . '/../config/config.php',
        __DIR__ . '/../../config/config.php',
        dirname(__DIR__) . '/config/config.php'
    ];
    foreach ($config_paths as $p) {
        if (file_exists($p)) {
            require_once $p;
            break;
        }
    }
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? 'admin@mishae.com';
$admin_initial = strtoupper(substr($admin_name, 0, 1) ?: 'A');

// Dynamic Notifications Fetch
$notif_items = [];
$total_alerts = 0;

if (isset($conn) && $conn) {
    // 1. Pending Orders
    $ord_q = mysqli_query($conn, "SELECT ord_id, ord_date, ord_total, ord_status FROM orders WHERE ord_status = 'Pending' ORDER BY ord_id DESC LIMIT 3");
    if ($ord_q) {
        while ($o = mysqli_fetch_assoc($ord_q)) {
            $total_alerts++;
            $notif_items[] = [
                'type' => 'order',
                'icon' => 'fa-solid fa-bag-shopping',
                'color' => '#C5A880',
                'bg' => 'rgba(197, 168, 128, 0.15)',
                'title' => 'New Order #ORD-' . (int)$o['ord_id'],
                'desc' => 'PKR ' . number_format((float)$o['ord_total']) . ' • Pending Dispatch',
                'time' => date('M d, H:i', strtotime($o['ord_date'])),
                'link' => '/commerce/admin/orders.php'
            ];
        }
    }

    // 2. Low/Out of Stock Alerts
    $stock_q = mysqli_query($conn, "SELECT pro_id, pro_name, pro_quan FROM product WHERE pro_quan < 3 ORDER BY pro_quan ASC LIMIT 3");
    if ($stock_q) {
        while ($s = mysqli_fetch_assoc($stock_q)) {
            $total_alerts++;
            $is_out = ((int)$s['pro_quan'] === 0);
            $notif_items[] = [
                'type' => 'stock',
                'icon' => 'fa-solid fa-triangle-exclamation',
                'color' => $is_out ? '#dc3545' : '#e67e22',
                'bg' => $is_out ? 'rgba(220, 53, 69, 0.12)' : 'rgba(230, 126, 34, 0.12)',
                'title' => $is_out ? 'Out of Stock Alert' : 'Low Stock Alert',
                'desc' => htmlspecialchars($s['pro_name']) . ' (' . (int)$s['pro_quan'] . ' remaining)',
                'time' => 'Inventory',
                'link' => '/commerce/admin/product/viewpro.php'
            ];
        }
    }

    // 3. Client Inquiries
    $msg_q = mysqli_query($conn, "SELECT id, name, subject, created_at FROM contact_messages ORDER BY id DESC LIMIT 2");
    if ($msg_q) {
        while ($m = mysqli_fetch_assoc($msg_q)) {
            $total_alerts++;
            $notif_items[] = [
                'type' => 'message',
                'icon' => 'fa-solid fa-envelope',
                'color' => '#3498db',
                'bg' => 'rgba(52, 152, 219, 0.12)',
                'title' => 'Message from ' . htmlspecialchars($m['name']),
                'desc' => htmlspecialchars($m['subject']),
                'time' => date('M d', strtotime($m['created_at'])),
                'link' => '/commerce/admin/messages.php'
            ];
        }
    }
}
?>
<header class="topbar">

    <div class="topbar-left">

        <button
            type="button"
            class="menu-toggle"
            id="menuToggle"
            aria-label="Open sidebar"
            aria-controls="sidebar"
            aria-expanded="false">

            <i class="fa-solid fa-bars"></i>

        </button>

        <h3>Dashboard</h3>

    </div>

    <div class="topbar-right">

        <!-- Notification Dropdown Container -->
        <div class="topbar-dropdown-container">
            <button
                type="button"
                class="notification"
                id="notificationBtn"
                aria-label="Notifications"
                aria-haspopup="true"
                aria-expanded="false"
                title="Notifications">

                <i class="fa-regular fa-bell"></i>
                <?php if ($total_alerts > 0): ?>
                    <span><?= $total_alerts ?></span>
                <?php endif; ?>

            </button>

            <!-- Notification Dropdown Menu -->
            <div class="topbar-dropdown notification-dropdown" id="notificationDropdown" role="menu">
                <div class="topbar-dropdown-header">
                    <div>
                        <h4 class="topbar-dropdown-title">Notifications</h4>
                        <small class="text-muted"><?= $total_alerts ?> pending alert<?= $total_alerts === 1 ? '' : 's' ?></small>
                    </div>
                    <?php if ($total_alerts > 0): ?>
                        <a href="/commerce/admin/orders.php" class="topbar-header-action">View Orders</a>
                    <?php endif; ?>
                </div>

                <div class="topbar-dropdown-body">
                    <?php if (empty($notif_items)): ?>
                        <div class="topbar-empty-state">
                            <i class="fa-regular fa-circle-check"></i>
                            <p>All caught up!</p>
                            <small>No pending orders or low inventory alerts.</small>
                        </div>
                    <?php else: ?>
                        <div class="notification-list">
                            <?php foreach ($notif_items as $item): ?>
                                <a href="<?= htmlspecialchars($item['link']) ?>" class="notification-item">
                                    <div class="notification-icon" style="color: <?= $item['color'] ?>; background: <?= $item['bg'] ?>;">
                                        <i class="<?= $item['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-details">
                                        <div class="notification-title"><?= htmlspecialchars($item['title']) ?></div>
                                        <div class="notification-desc"><?= $item['desc'] ?></div>
                                        <div class="notification-time"><?= $item['time'] ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="topbar-dropdown-footer">
                    <a href="/commerce/admin/orders.php">Manage Orders</a>
                    <span>•</span>
                    <a href="/commerce/admin/messages.php">Messages</a>
                    <span>•</span>
                    <a href="/commerce/admin/product/viewpro.php">Inventory</a>
                </div>
            </div>
        </div>

        <!-- Admin Profile Dropdown Container -->
        <div class="topbar-dropdown-container">
            <div class="admin-profile" id="adminProfileBtn" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false" title="Account Menu">

                <div class="admin-avatar"><?= $admin_initial ?></div>

                <div class="admin-info">
                    <strong><?= htmlspecialchars($admin_name) ?></strong>
                    <small>Store Administrator</small>
                </div>

                <i class="fa-solid fa-chevron-down admin-profile-arrow"></i>

            </div>

            <!-- Profile Dropdown Menu -->
            <div class="topbar-dropdown profile-dropdown" id="profileDropdown" role="menu">
                <div class="profile-dropdown-header">
                    <div class="profile-avatar-large"><?= $admin_initial ?></div>
                    <div class="profile-header-info">
                        <strong><?= htmlspecialchars($admin_name) ?></strong>
                        <small><?= htmlspecialchars($admin_email) ?></small>
                        <span class="profile-role-pill">Store Administrator</span>
                    </div>
                </div>

                <div class="profile-dropdown-menu">
                    <a href="/commerce/admin/settings.php" class="profile-menu-item">
                        <i class="fa-solid fa-gear"></i>
                        <span>Store Settings</span>
                    </a>
                    <a href="/commerce/admin/orders.php" class="profile-menu-item">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span>Client Orders</span>
                    </a>
                    <a href="/commerce/admin/messages.php" class="profile-menu-item">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Client Messages</span>
                    </a>
                    <a href="/commerce/admin/customers.php" class="profile-menu-item">
                        <i class="fa-solid fa-users"></i>
                        <span>Manage Customers</span>
                    </a>
                    <a href="/commerce/user/home.php" target="_blank" class="profile-menu-item">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>View Live Store</span>
                    </a>
                </div>

                <div class="profile-dropdown-footer">
                    <a href="/commerce/admin/logout.php" class="profile-logout-link">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Sign Out</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</header>
