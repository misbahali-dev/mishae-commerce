<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += (int)($item['quantity'] ?? 1);
    }
}

$wishlist_count = isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
$is_customer_logged_in = !empty($_SESSION['customer_id']);
$customer_first_name = '';
$customer_full_name = '';

if ($is_customer_logged_in) {
    if (!empty($_SESSION['customer_name'])) {
        $customer_full_name = $_SESSION['customer_name'];
    } elseif (isset($conn) && $conn) {
        $cid = (int)$_SESSION['customer_id'];
        $c_q = mysqli_query($conn, "SELECT cus_name, cus_email FROM customers WHERE cus_id = $cid LIMIT 1");
        if ($c_q && $c_r = mysqli_fetch_assoc($c_q)) {
            $customer_full_name = $c_r['cus_name'];
            $_SESSION['customer_name'] = $customer_full_name;
            if (empty($_SESSION['customer_email'])) {
                $_SESSION['customer_email'] = $c_r['cus_email'];
            }
        }
    }
    if (!empty($customer_full_name)) {
        $parts = explode(' ', trim($customer_full_name));
        $customer_first_name = htmlspecialchars($parts[0]);
    } else {
        $customer_first_name = 'Member';
    }

    $customer_img = $_SESSION['customer_img'] ?? '';
    if (empty($customer_img) && isset($conn) && $conn) {
        $cid = (int)$_SESSION['customer_id'];
        $ci_q = mysqli_query($conn, "SELECT cus_img FROM customers WHERE cus_id = $cid LIMIT 1");
        if ($ci_q && $ci_r = mysqli_fetch_assoc($ci_q)) {
            $customer_img = $ci_r['cus_img'] ?? '';
            $_SESSION['customer_img'] = $customer_img;
        }
    }
}

// Active Navigation Detection
$current_script = basename($_SERVER['PHP_SELF'] ?? '');
$get_cat = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$get_season = isset($_GET['season']) ? trim($_GET['season']) : '';

$nav_active_home = in_array($current_script, ['home.php', 'index.php']);
$nav_active_about = ($current_script === 'about.php');
$nav_active_ucat = ($current_script === 'ucat.php');
$nav_active_contact = ($current_script === 'contact.php');
$nav_active_women = ($current_script === 'products.php' && $get_cat === 15);
$nav_active_men = ($current_script === 'products.php' && $get_cat === 14);
$nav_active_kids = ($current_script === 'kids.php' || ($current_script === 'products.php' && ($get_cat === 16 || isset($_GET['gender']))));
$nav_active_all_fabrics = ($current_script === 'products.php' && $get_cat === 0 && empty($get_season) && !isset($_GET['gender']));
?>

<style>
/* Prevent any shifting of links or logo */
.mishae-nav-wrap {
    position: sticky;
    top: 0;
    background: #FAF8F5;
    border-bottom: 1px solid #EAE5DF;
    box-shadow: 0 4px 18px rgba(14, 14, 16, 0.06);
    z-index: 1030;
    transition: box-shadow 0.25s ease;
}

.mishae-navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1320px;
    margin: 0 auto;
    padding: 16px 24px;
    position: relative;
    flex-wrap: nowrap;
}

.mishae-nav-left {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    flex-shrink: 0;
}

/* Mobile Toggle: STRICTLY HIDDEN ON DESKTOP */
.mishae-mobile-toggle,
#mobileMenuToggle {
    display: none !important;
    visibility: hidden !important;
}

@media (min-width: 992px) {
    .mishae-mobile-toggle,
    #mobileMenuToggle {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
}

@media (max-width: 991.98px) {
    .mishae-navbar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 12px 18px !important;
        flex-wrap: nowrap !important;
        gap: 10px !important;
    }

    .mishae-nav-left {
        display: inline-flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
        min-width: 0 !important;
    }

    .mishae-mobile-toggle,
    #mobileMenuToggle {
        display: inline-flex !important;
        visibility: visible !important;
        align-items: center !important;
        justify-content: center !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        color: #292526 !important;
        font-size: 19px !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
    }

    .mishae-logo {
        font-size: 21px !important;
        letter-spacing: 1.8px !important;
        line-height: 1 !important;
        flex-shrink: 0 !important;
    }

    .mishae-logo span {
        font-size: 8px !important;
        letter-spacing: 2px !important;
        margin-top: 2px !important;
    }

    .mishae-nav-actions {
        display: inline-flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-shrink: 0 !important;
    }

    .mishae-account-desktop {
        display: none !important;
    }

    .nav-search-toggle {
        font-size: 15px !important;
        padding: 6px !important;
    }

    .nav-badge-wrapper i {
        font-size: 16px !important;
    }
}

.mishae-nav-links {
    display: flex;
    align-items: center;
    gap: 24px;
    flex: 1 1 auto;
    justify-content: center;
    margin: 0 20px;
}

.mishae-nav-links a {
    position: relative;
    display: inline-flex;
    align-items: center;
    padding: 8px 0 !important;
    color: #292526 !important;
    font-family: 'Poppins', Arial, sans-serif !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    letter-spacing: 0.3px !important;
    text-transform: none !important;
    text-decoration: none !important;
    transition: color 0.25s ease;
    white-space: nowrap;
}

.mishae-nav-links a:hover,
.mishae-nav-links a.active {
    color: #8B7968 !important;
}

.mishae-nav-links a::after {
    content: '' !important;
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    width: 0 !important;
    height: 2px !important;
    background: #C5A880 !important;
    transition: width 0.25s ease !important;
}

.mishae-nav-links a:hover::after,
.mishae-nav-links a.active::after {
    width: 100% !important;
}

.mishae-nav-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-shrink: 0;
}

/* Stable Search Icon Button (Never resizes navbar) */
.nav-search-toggle {
    background: transparent;
    border: none;
    color: #292526;
    font-size: 16px;
    cursor: pointer;
    padding: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: color 0.2s ease, background 0.2s ease;
}

.nav-search-toggle:hover {
    color: #8B7968;
    background: rgba(197, 168, 128, 0.15);
}

/* Search Dropdown (Slides down below navbar, never shifting links) */
.nav-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #FAF8F5;
    border-top: 1px solid #EAE5DF;
    border-bottom: 1px solid #EAE5DF;
    box-shadow: 0 12px 30px rgba(14, 14, 16, 0.08);
    padding: 16px 24px;
    z-index: 1000;
    display: none;
    animation: navSearchSlide 0.25s ease forwards;
}

@keyframes navSearchSlide {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

.nav-search-inner {
    max-width: 760px;
    margin: 0 auto;
    position: relative;
    display: flex;
    align-items: center;
    background: #FFFFFF;
    border: 1px solid #EAE5DF;
    border-radius: 30px;
    padding: 4px 6px 4px 18px;
    transition: border-color 0.25s, box-shadow 0.25s;
}

.nav-search-inner:focus-within {
    border-color: #C5A880;
    box-shadow: 0 0 0 3px rgba(197, 168, 128, 0.15);
}

.nav-search-inner input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 8px 6px;
    font-size: 13.5px;
    color: #292526;
    outline: none;
    font-family: inherit;
}

.nav-search-submit {
    background: #29211f;
    color: #FFFFFF;
    border: none;
    border-radius: 20px;
    padding: 8px 22px;
    font-size: 11.5px;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.25s, transform 0.2s;
}

.nav-search-submit:hover {
    background: #a78543;
    color: #FFFFFF;
}

.nav-search-close {
    background: none;
    border: none;
    color: #888;
    font-size: 24px;
    line-height: 1;
    padding: 0 10px;
    cursor: pointer;
    transition: color 0.2s;
}

.nav-search-close:hover {
    color: #292526;
}

/* User Account Button & Dropdown */
.mishae-account-dropdown-wrap {
    position: relative;
    display: inline-block;
}

.mishae-account-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    border: 1px solid #C5A880;
    border-radius: 20px;
    font-size: 11.5px;
    letter-spacing: 0.3px;
    color: #292526 !important;
    text-decoration: none !important;
    background: #FAF8F5;
    transition: all 0.25s ease;
    white-space: nowrap;
    cursor: pointer;
    font-family: inherit;
    line-height: 1.2;
}

.mishae-account-btn.mishae-account-guest {
    border-color: #29211f;
    background: #FFFFFF;
    color: #29211f !important;
}

.mishae-account-btn.mishae-account-register {
    border-color: #29211f;
    background: #29211f;
    color: #FFFFFF !important;
}

.mishae-account-btn:hover {
    border-color: #a78543;
    background: #a78543;
    color: #FFFFFF !important;
}

.mishae-account-btn:hover i {
    color: #FFFFFF !important;
}

.mishae-account-btn .nav-chevron {
    font-size: 9px;
    opacity: 0.6;
    transition: transform 0.2s ease;
}

.mishae-account-dropdown-wrap:hover .nav-chevron,
.mishae-account-dropdown-wrap.open .nav-chevron {
    transform: rotate(180deg);
}

.mishae-account-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 220px;
    background: #FFFFFF;
    border: 1px solid #EAE5DF;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    padding: 8px 0;
    z-index: 1050;
    display: none;
    animation: mishaeFadeDown 0.2s ease forwards;
}

.mishae-account-dropdown-wrap:hover .mishae-account-menu,
.mishae-account-dropdown-wrap:focus-within .mishae-account-menu {
    display: block;
}

@keyframes mishaeFadeDown {
    from {
        opacity: 0;
        transform: translateY(-6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.mishae-account-menu-header {
    padding: 8px 16px 10px 16px;
}

.mishae-account-menu-header strong {
    display: block;
    font-size: 13px;
    color: #292526;
    font-weight: 600;
    line-height: 1.3;
}

.mishae-account-menu-header small {
    display: block;
    font-size: 11px;
    color: #8B7968;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mishae-account-menu-divider {
    height: 1px;
    background: #EAE5DF;
    margin: 4px 0;
}

.mishae-account-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    font-size: 12px;
    color: #292526 !important;
    text-decoration: none !important;
    transition: background 0.15s ease, color 0.15s ease;
}

.mishae-account-menu-item i {
    width: 14px;
    text-align: center;
    font-size: 12px;
    color: #8B7968;
}

.mishae-account-menu-item:hover {
    background: #FAF8F5;
    color: #8B7968 !important;
}

.mishae-account-menu-item:hover i {
    color: #8B7968 !important;
}

.mishae-account-menu-item.text-danger {
    color: #b71c1c !important;
}

.mishae-account-menu-item.text-danger i {
    color: #b71c1c !important;
}

.mishae-account-menu-item.text-danger:hover {
    background: #FDF2F2;
    color: #b71c1c !important;
}

/* Simple and clean ampersand style */
.simple-and {
    font-family: 'DM Sans', Arial, sans-serif !important;
    font-style: normal !important;
    font-weight: 400 !important;
}
</style>

<div class="mishae-nav-wrap">
    <nav class="mishae-navbar">
        <div class="mishae-nav-left">
            <!-- Mobile Menu Toggle Button (Strictly hidden on desktop) -->
            <button class="mishae-mobile-toggle" id="mobileMenuToggle" type="button" aria-label="Toggle Navigation">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand Logo (Frozen Stable Position) -->
            <a href="home.php" class="mishae-logo" style="display: inline-block !important; font-family: Georgia, serif !important; font-size: 27px !important; font-weight: normal !important; letter-spacing: 2px !important; color: #0E0E10 !important; text-decoration: none !important; line-height: 1 !important; flex-shrink: 0 !important;">
                MiSHAÉ
                <span style="display: block !important; font-size: 9px !important; letter-spacing: 3px !important; font-family: sans-serif !important; text-transform: uppercase !important; color: #8B7968 !important; margin-top: 3px !important; font-weight: normal !important; line-height: 1 !important;">Luxury Fabrics</span>
            </a>
        </div>

        <!-- Desktop Navigation Links (Frozen Stable Position) -->
        <div class="mishae-nav-links d-none d-lg-flex">
            <a href="home.php" class="<?php echo $nav_active_home ? 'active' : ''; ?>">Home</a>
            <a href="ucat.php" class="<?php echo $nav_active_ucat ? 'active' : ''; ?>">Collections</a>
            <a href="products.php" class="<?php echo $nav_active_all_fabrics ? 'active' : ''; ?>">All Fabrics</a>
            <a href="products.php?cat_id=15" class="<?php echo $nav_active_women ? 'active' : ''; ?>">Women</a>
            <a href="products.php?cat_id=14" class="<?php echo $nav_active_men ? 'active' : ''; ?>">Men</a>
            <a href="kids.php" class="<?php echo $nav_active_kids ? 'active' : ''; ?>">Kids</a>
            <a href="about.php" class="<?php echo $nav_active_about ? 'active' : ''; ?>">Our Story</a>
            <a href="contact.php" class="<?php echo $nav_active_contact ? 'active' : ''; ?>">Contact</a>
        </div>

        <!-- Navigation Actions (Search Toggle, Wishlist, Cart, Account) -->
        <div class="mishae-nav-actions">
            <!-- Search Toggle Button (Does not shift nav links) -->
            <button type="button" class="nav-search-toggle" id="navSearchToggle" aria-label="Search" title="Search catalog">
                <i class="fas fa-search"></i>
            </button>

            <!-- Wishlist Link -->
            <a href="wishlist.php" class="nav-badge-wrapper" id="navWishlistBtn" aria-label="Wishlist" title="Your Wishlist">
                <i class="far fa-heart" style="font-size: 18px;"></i>
                <span class="nav-badge" id="navWishlistBadge" style="<?php echo ($wishlist_count > 0) ? '' : 'display: none;'; ?>"><?php echo $wishlist_count; ?></span>
            </a>

            <!-- Cart Link -->
            <a href="cart.php" class="nav-badge-wrapper" aria-label="Shopping Bag" title="Shopping Bag">
                <i class="fas fa-shopping-bag" style="font-size: 18px;"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="nav-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <!-- Account / Personalized Welcome Greeting (Desktop only) -->
            <?php if ($is_customer_logged_in): ?>
                <div class="mishae-account-dropdown-wrap mishae-account-desktop">
                    <a href="account.php" class="mishae-account-btn" title="My Account" id="accountMenuBtn">
                        <?php if (!empty($customer_img) && file_exists(__DIR__ . '/../../admin/uploads/' . $customer_img)): ?>
                            <img src="../admin/uploads/<?php echo htmlspecialchars($customer_img); ?>" alt="Avatar" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover; border: 1px solid #C5A880; margin-right: 2px;">
                        <?php else: ?>
                            <i class="far fa-user-circle"></i>
                        <?php endif; ?>
                        <span>Welcome, <?php echo $customer_first_name; ?></span>
                        <i class="fas fa-chevron-down nav-chevron"></i>
                    </a>
                    <div class="mishae-account-menu" id="accountDropdownMenu">
                        <div class="mishae-account-menu-header" style="display: flex; align-items: center; gap: 10px;">
                            <?php if (!empty($customer_img) && file_exists(__DIR__ . '/../../admin/uploads/' . $customer_img)): ?>
                                <img src="../admin/uploads/<?php echo htmlspecialchars($customer_img); ?>" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #C5A880;">
                            <?php endif; ?>
                            <div>
                                <strong><?php echo htmlspecialchars($customer_full_name); ?></strong>
                                <?php if (!empty($_SESSION['customer_email'])): ?>
                                    <small><?php echo htmlspecialchars($_SESSION['customer_email']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mishae-account-menu-divider"></div>
                        <a href="account.php" class="mishae-account-menu-item">
                            <i class="far fa-user"></i> My Profile &amp; Orders
                        </a>
                        <a href="wishlist.php" class="mishae-account-menu-item">
                            <i class="far fa-heart"></i> My Wishlist
                            <?php if ($wishlist_count > 0): ?>
                                <span class="badge badge-pill badge-secondary ml-auto" style="font-size: 10px;"><?php echo $wishlist_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="mishae-account-menu-divider"></div>
                        <a href="logout.php" class="mishae-account-menu-item text-danger">
                            <i class="fas fa-sign-out-alt"></i> Sign Out
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="mishae-auth-buttons mishae-account-desktop" style="display: flex; align-items: center; gap: 8px;">
                    <a href="login.php" class="mishae-account-btn mishae-account-guest" title="Log In to Your Account">
                        <i class="far fa-user"></i>
                        <span>Log In</span>
                    </a>
                    <a href="register.php" class="mishae-account-btn mishae-account-register" title="Create a New Account">
                        <i class="fas fa-user-plus"></i>
                        <span>Sign Up</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Slide-Down Search Overlay Bar (Zero Shifting of Navbar Links) -->
    <div id="navSearchDropdown" class="nav-search-dropdown">
        <div class="nav-search-inner">
            <i class="fas fa-search text-muted mr-2" style="font-size: 14px;"></i>
            <form action="products.php" method="GET" style="display: flex; flex: 1; align-items: center;" id="navSearchForm">
                <input type="text" name="search" id="navSearchInput" 
                       placeholder="Search luxury fabrics, unstitched lawn, festive silk..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                       autocomplete="off">
                <button type="submit" class="nav-search-submit">Search</button>
            </form>
            <button type="button" class="nav-search-close" id="navSearchClose" aria-label="Close Search">&times;</button>
        </div>
    </div>
</div>

<!-- Mobile Drawer Navigation -->
<div id="mishaeMobileDrawer" style="display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(0,0,0,0.5);">
    <div style="background: #fff; width: 280px; height: 100%; padding: 25px 20px; display: flex; flex-direction: column; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
            <div>
                <span style="font-family: Georgia, serif; font-size: 20px; font-weight: bold; display: block;">MiSHAÉ</span>
                <?php if ($is_customer_logged_in): ?>
                    <small style="color: #8B7968; font-size: 11px;">Welcome, <?php echo $customer_first_name; ?></small>
                <?php endif; ?>
            </div>
            <button id="closeMobileDrawer" style="background:none; border:none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>

        <form action="products.php" method="GET" style="margin-bottom: 20px;">
            <div style="position: relative;">
                <input type="text" name="search" placeholder="Search fabrics..." style="width: 100%; padding: 8px 32px 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                <button type="submit" style="position: absolute; right: 10px; top: 8px; background: none; border: none; color: #666;"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <nav style="display: flex; flex-direction: column; gap: 15px; font-size: 14px;">
            <a href="home.php" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_home ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Home</a>
            <a href="ucat.php" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_ucat ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Collections</a>
            <a href="products.php" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_all_fabrics ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">All Fabrics</a>
            <a href="products.php?cat_id=15" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_women ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Women</a>
            <a href="products.php?cat_id=14" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_men ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Men</a>
            <a href="kids.php" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_kids ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Kids</a>
            <a href="about.php" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_about ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Our Story</a>
            <a href="contact.php" style="color: #222; text-decoration: none; padding: 5px 0; <?php echo $nav_active_contact ? 'color: #8B7765 !important; font-weight: bold; border-left: 3px solid #C5A880; padding-left: 8px;' : ''; ?>">Contact Us</a>
            <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
            <a href="wishlist.php" style="color: #222; text-decoration: none; padding: 5px 0;"><i class="far fa-heart me-2"></i> Wishlist (<?php echo $wishlist_count; ?>)</a>
            <a href="cart.php" style="color: #222; text-decoration: none; padding: 5px 0;"><i class="fas fa-shopping-bag me-2"></i> Shopping Bag (<?php echo $cart_count; ?>)</a>
            <?php if ($is_customer_logged_in): ?>
                <a href="account.php" style="color: #222; text-decoration: none; padding: 5px 0;"><i class="far fa-user me-2"></i> My Account (<?php echo $customer_first_name; ?>)</a>
                <a href="logout.php" style="color: #c62828; text-decoration: none; padding: 5px 0;"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" style="color: #222; text-decoration: none; padding: 6px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="far fa-user" style="width: 16px; color: #a78543;"></i>
                    <span>Log In</span>
                </a>
                <a href="register.php" style="color: #222; text-decoration: none; padding: 6px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-plus" style="width: 16px; color: #a78543;"></i>
                    <span>Sign Up / Create Account</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Dropdown Toggle (Zero layout shift on navbar links)
    var searchToggle = document.getElementById('navSearchToggle');
    var searchDropdown = document.getElementById('navSearchDropdown');
    var searchClose = document.getElementById('navSearchClose');
    var searchInput = document.getElementById('navSearchInput');

    if (searchToggle && searchDropdown) {
        searchToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOpen = searchDropdown.style.display === 'block';
            searchDropdown.style.display = isOpen ? 'none' : 'block';
            if (!isOpen && searchInput) {
                setTimeout(function() { searchInput.focus(); }, 50);
            }
        });

        if (searchClose) {
            searchClose.addEventListener('click', function(e) {
                e.stopPropagation();
                searchDropdown.style.display = 'none';
            });
        }

        document.addEventListener('click', function(e) {
            if (searchDropdown.style.display === 'block' && !searchDropdown.contains(e.target) && e.target !== searchToggle && !searchToggle.contains(e.target)) {
                searchDropdown.style.display = 'none';
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && searchDropdown.style.display === 'block') {
                searchDropdown.style.display = 'none';
            }
        });
    }

    // Mobile Drawer Toggle
    var toggleBtn = document.getElementById('mobileMenuToggle');
    var drawer = document.getElementById('mishaeMobileDrawer');
    var closeBtn = document.getElementById('closeMobileDrawer');

    if (toggleBtn && drawer && closeBtn) {
        toggleBtn.addEventListener('click', function() {
            drawer.style.display = 'block';
        });
        closeBtn.addEventListener('click', function() {
            drawer.style.display = 'none';
        });
        drawer.addEventListener('click', function(e) {
            if (e.target === drawer) {
                drawer.style.display = 'none';
            }
        });
    }

    // Account Dropdown Toggle
    var accountWrap = document.querySelector('.mishae-account-dropdown-wrap');
    var accountBtn = document.getElementById('accountMenuBtn');
    if (accountWrap && accountBtn) {
        accountBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            accountWrap.classList.toggle('open');
        });
        document.addEventListener('click', function(e) {
            if (!accountWrap.contains(e.target)) {
                accountWrap.classList.remove('open');
            }
        });
    }

    // Universal Smooth Alert Dismissal (Zero Jerk, Silky Ease)
    window.dismissMishaeAlert = function(el) {
        var alert = el.closest ? el.closest('.mishae-alert') : el;
        if (!alert || alert.classList.contains('alert-collapsing')) return;
        alert.classList.add('alert-collapsing');
        setTimeout(function() {
            if (alert.parentNode) alert.parentNode.removeChild(alert);
        }, 520);
    };

    var alerts = document.querySelectorAll('.mishae-alert');
    alerts.forEach(function(alert) {
        var isError = alert.classList.contains('mishae-alert-danger');
        var delay = isError ? 5000 : 3500;
        setTimeout(function() {
            window.dismissMishaeAlert(alert);
        }, delay);
    });
});
</script>
