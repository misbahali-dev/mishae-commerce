<?php
$current_page = basename($_SERVER['PHP_SELF'] ?? '');
$is_home = ($current_page === 'home.php' || $current_page === 'index.php');
$is_cat_add = ($current_page === 'addcat.php');
$is_cat_view = ($current_page === 'viewcat.php');
$is_cat = ($is_cat_add || $is_cat_view);
$is_pro_add = ($current_page === 'addpro.php');
$is_pro_view = ($current_page === 'viewpro.php');
$is_pro = ($is_pro_add || $is_pro_view);
$is_orders = ($current_page === 'orders.php');
$is_customers = ($current_page === 'customers.php');
$is_messages = ($current_page === 'messages.php');
$is_settings = ($current_page === 'settings.php');
?>
<aside class="sidebar" id="sidebar">

    <div class="brand">
        <span>MiSHAÉ</span>
        <small>ADMIN PANEL</small>
    </div>

    <nav class="sidebar-menu" aria-label="Admin navigation">

        <div class="menu-title">OVERVIEW</div>

        <a href="/commerce/admin/home.php"
            class="menu-item <?= $is_home ? 'active' : '' ?>"
            aria-current="<?= $is_home ? 'page' : 'false' ?>">

            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>

        </a>

        <div class="menu-title">STORE MANAGEMENT</div>

        <!-- Categories dropdown -->
        <button type="button"
            class="menu-item dropdown <?= $is_cat ? 'open' : '' ?>"
            aria-expanded="<?= $is_cat ? 'true' : 'false' ?>">

            <i class="fa-solid fa-layer-group"></i>
            <span>Categories</span>
            <i class="fa-solid fa-chevron-down arrow"></i>

        </button>

        <div class="submenu <?= $is_cat ? 'open' : '' ?>">
            <a href="/commerce/admin/category/addcat.php" class="<?= $is_cat_add ? 'active' : '' ?>">
                Add Category
            </a>

            <a href="/commerce/admin/category/viewcat.php" class="<?= $is_cat_view ? 'active' : '' ?>">
                All Categories
            </a>
        </div>

        <!-- Products dropdown -->
        <button type="button"
            class="menu-item dropdown <?= $is_pro ? 'open' : '' ?>"
            aria-expanded="<?= $is_pro ? 'true' : 'false' ?>">

            <i class="fa-solid fa-shirt"></i>
            <span>Products</span>
            <i class="fa-solid fa-chevron-down arrow"></i>

        </button>

        <div class="submenu <?= $is_pro ? 'open' : '' ?>">
            <a href="/commerce/admin/product/addpro.php" class="<?= $is_pro_add ? 'active' : '' ?>">
                Add Product
            </a>

            <a href="/commerce/admin/product/viewpro.php" class="<?= $is_pro_view ? 'active' : '' ?>">
                All Products
            </a>
        </div>

        <!-- Orders (Direct Single-Click Link) -->
        <a href="/commerce/admin/orders.php"
            class="menu-item <?= $is_orders ? 'active' : '' ?>"
            aria-current="<?= $is_orders ? 'page' : 'false' ?>">
            <i class="fa-solid fa-bag-shopping"></i>
            <span>Orders</span>
        </a>

        <div class="menu-title">CUSTOMER MANAGEMENT</div>

        <a href="/commerce/admin/customers.php" class="menu-item <?= $is_customers ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            <span>Customers</span>
        </a>

        <a href="/commerce/admin/messages.php" class="menu-item <?= $is_messages ? 'active' : '' ?>">
            <i class="fa-regular fa-envelope"></i>
            <span>Messages</span>
        </a>

        <div class="menu-title">STORE SETTINGS</div>

        <a href="/commerce/admin/settings.php" class="menu-item <?= $is_settings ? 'active' : '' ?>">
            <i class="fa-solid fa-gear"></i>
            <span>Settings</span>
        </a>

    </nav>

    <div class="sidebar-bottom">

        <a href="/commerce/admin/logout.php"
            class="menu-item logout">

            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>

        </a>

    </div>

</aside>

<!-- Mobile overlay: keep this outside the aside -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
