<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

$selected_cat = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
$selected_season = isset($_GET['season']) ? trim($_GET['season']) : '';
$selected_gender = isset($_GET['gender']) ? strtolower(trim($_GET['gender'])) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$sort = $_GET['sort'] ?? 'newest';

// Base Query
$where = ["category.cat_status = 1"];

if ($selected_cat > 0) {
    $where[] = "product.cat_id = $selected_cat";
}

if (!empty($selected_gender)) {
    if ($selected_gender === 'boys') {
        $where[] = "(product.pro_name LIKE '%boy%' OR product.pro_des LIKE '%boy%')";
    } elseif ($selected_gender === 'girls') {
        $where[] = "(product.pro_name LIKE '%girl%' OR product.pro_des LIKE '%girl%')";
    }
}

if (!empty($selected_season) && in_array($selected_season, ['Summer', 'Winter', 'All Seasons'])) {
    $season_clean = mysqli_real_escape_string($conn, $selected_season);
    if ($selected_season !== 'All Seasons') {
        $where[] = "(product.pro_season = '$season_clean' OR product.pro_season = 'All Seasons')";
    }
}

if (!empty($search)) {
    $where[] = "(product.pro_name LIKE '%$search%' OR product.pro_des LIKE '%$search%' OR category.cat_name LIKE '%$search%')";
}

$selected_cat_name = '';
if ($selected_cat > 0) {
    $c_q = mysqli_query($conn, "SELECT cat_name FROM category WHERE cat_id = $selected_cat LIMIT 1");
    if ($c_q && $c_row = mysqli_fetch_assoc($c_q)) {
        $selected_cat_name = $c_row['cat_name'];
    }
}

if (!empty($selected_gender)) {
    if ($selected_gender === 'boys') {
        $page_heading = "Boys’ Collection";
        $page_subheading = "Festive embroidered kurtas, shalwar suits, and classic eastern attire for young boys.";
    } elseif ($selected_gender === 'girls') {
        $page_heading = "Girls’ Collection";
        $page_subheading = "Delicate festive ensembles, silk frocks, and celebratory traditional wear for young girls.";
    }
} elseif (!empty($selected_cat_name)) {
    $page_heading = $selected_cat_name;
    $page_subheading = "Explore our latest " . strtolower($selected_cat_name) . " unstitched fabrics and curated designs.";
} elseif (!empty($selected_season)) {
    $page_heading = $selected_season . " Collection";
    $page_subheading = "Discover fabrics curated for the " . strtolower($selected_season) . " season.";
} elseif (!empty($search)) {
    $page_heading = "Search Results";
    $page_subheading = "Showing search results for \"" . $search . "\".";
} else {
    $page_heading = "All Collections";
    $page_subheading = "Explore luxury unstitched lawn, handcrafted silk, and festive ensembles.";
}

$where_clause = implode(' AND ', $where);

// Sorting
$order_by = "product.pro_id DESC";
if ($sort === 'price_asc') {
    $order_by = "product.pro_price ASC";
} elseif ($sort === 'price_desc') {
    $order_by = "product.pro_price DESC";
} elseif ($sort === 'name') {
    $order_by = "product.pro_name ASC";
}

$query = "SELECT product.*, category.cat_name 
          FROM product 
          INNER JOIN category ON product.cat_id = category.cat_id 
          WHERE $where_clause 
          ORDER BY $order_by";

$product_result = mysqli_query($conn, $query);

// Fetch categories for filter tabs
$cat_result = mysqli_query($conn, "SELECT * FROM category WHERE cat_status = 1 ORDER BY cat_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Fabric Collections | MiSHAÉ</title>

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
    <link rel="stylesheet" href="assets/css/upro.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body>

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<!-- Single Clean Page Header -->
<section class="page-intro-header">
    <div class="container text-center">
        <h1 class="page-intro-title"><?php echo htmlspecialchars($page_heading); ?></h1>
        <p class="page-intro-desc"><?php echo htmlspecialchars($page_subheading); ?></p>
    </div>
</section>

<!-- Main Catalog Section -->
<section class="products-section">
    <!-- Flash Notifications -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mishae-alert mishae-alert-<?php echo $_SESSION['flash_message']['type']; ?>">
            <span><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['flash_message']['text']); ?></span>
            <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close Alert">&times;</button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- Category Filters & Sorting Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <!-- Category & Seasonal Filter Tabs -->
        <div class="products-filter mb-2 mb-md-0">
            <a href="products.php" class="<?php echo ($selected_cat === 0 && empty($search) && empty($selected_season) && empty($selected_gender)) ? 'active' : ''; ?>">
                All Collections
            </a>
            <a href="products.php?season=Summer" class="<?php echo ($selected_season === 'Summer') ? 'active' : ''; ?>">
                Summer Edit
            </a>
            <a href="products.php?season=Winter" class="<?php echo ($selected_season === 'Winter') ? 'active' : ''; ?>">
                Winter Edit
            </a>
            <?php 
            mysqli_data_seek($cat_result, 0);
            while ($cat = mysqli_fetch_assoc($cat_result)): 
                $is_c_active = ($selected_cat === (int)$cat['cat_id'] && empty($selected_season));
                // If it's Kids category, link directly or handle
                $cat_link = ((int)$cat['cat_id'] === 16) ? 'kids.php' : ('products.php?cat_id=' . (int)$cat['cat_id']);
            ?>
                <a href="<?php echo $cat_link; ?>" 
                   class="<?php echo ($is_c_active && empty($selected_gender)) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Sorting Dropdown -->
        <form method="GET" action="products.php" class="d-flex align-items-center" style="gap: 8px;">
            <?php if (!empty($selected_season)): ?><input type="hidden" name="season" value="<?php echo htmlspecialchars($selected_season); ?>"><?php endif; ?>
            <?php if ($selected_cat > 0): ?>
                <input type="hidden" name="cat_id" value="<?php echo $selected_cat; ?>">
            <?php endif; ?>
            <?php if (!empty($selected_gender)): ?>
                <input type="hidden" name="gender" value="<?php echo htmlspecialchars($selected_gender); ?>">
            <?php endif; ?>
            <?php if (!empty($search)): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <?php endif; ?>

            <label class="small text-muted mb-0 font-weight-bold text-uppercase d-none d-sm-inline" style="font-size: 10px; letter-spacing: 1px;">Sort By:</label>
            <select name="sort" class="form-control form-control-sm mishae-sort-select" style="min-width: 160px; height: 36px; padding: 6px 12px; font-size: 12px; line-height: 1.4; color: #292526; background-color: #FAF8F5; border: 1px solid #EAE5DF; border-radius: 4px; font-weight: 500; cursor: pointer;" onchange="this.form.submit()">
                <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?> style="color: #292526; background-color: #FFFFFF;">Newest Arrivals</option>
                <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?> style="color: #292526; background-color: #FFFFFF;">Price: Low to High</option>
                <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?> style="color: #292526; background-color: #FFFFFF;">Price: High to Low</option>
                <option value="name" <?php echo ($sort === 'name') ? 'selected' : ''; ?> style="color: #292526; background-color: #FFFFFF;">Alphabetical</option>
            </select>
        </form>
    </div>

    <!-- Active Search Filter Tag -->
    <?php if (!empty($search)): ?>
        <div class="mb-4">
            <span class="badge badge-light p-2 border font-weight-normal" style="font-size: 13px;">
                Showing results for: <strong>"<?php echo htmlspecialchars($search); ?>"</strong>
                <a href="products.php" class="text-danger ml-2 text-decoration-none">&times; Clear Search</a>
            </span>
        </div>
    <?php endif; ?>

    <!-- Kids Collection Sub-Filter Tabs -->
    <?php if ($selected_cat === 16 || !empty($selected_gender)): ?>
        <div class="mb-4 d-flex align-items-center flex-wrap" style="gap: 8px;">
            <span class="text-muted small font-weight-bold text-uppercase mr-2" style="letter-spacing: 1.2px; font-size: 11px;">Kids Collection:</span>
            <a href="kids.php" class="btn btn-sm btn-outline-dark" style="font-size: 12px; border-radius: 20px; padding: 4px 14px;">
                <i class="fas fa-th-large mr-1"></i> Overview
            </a>
            <a href="products.php?cat_id=16" class="btn btn-sm <?php echo (empty($selected_gender)) ? 'btn-dark text-white' : 'btn-outline-dark'; ?>" style="font-size: 12px; border-radius: 20px; padding: 4px 14px;">
                All Kids
            </a>
            <a href="products.php?cat_id=16&gender=boys" class="btn btn-sm <?php echo ($selected_gender === 'boys') ? 'btn-dark text-white' : 'btn-outline-dark'; ?>" style="font-size: 12px; border-radius: 20px; padding: 4px 14px;">
                Boys’ Collection
            </a>
            <a href="products.php?cat_id=16&gender=girls" class="btn btn-sm <?php echo ($selected_gender === 'girls') ? 'btn-dark text-white' : 'btn-outline-dark'; ?>" style="font-size: 12px; border-radius: 20px; padding: 4px 14px;">
                Girls’ Collection
            </a>
        </div>
    <?php endif; ?>

    <!-- Products Grid -->
    <div class="products-grid">
        <?php if ($product_result && mysqli_num_rows($product_result) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($product_result)): 
                $p_id = (int)$product['pro_id'];
                $is_fav = isset($_SESSION['wishlist'][$p_id]);
            ?>
                <div class="product-card">
                    <div style="position: relative;">
                        <!-- Product Image Link -->
                        <a href="product-detail.php?id=<?php echo $p_id; ?>" class="product-image">
                            <img src="../admin/uploads/<?php echo htmlspecialchars($product['pro_img']); ?>" 
                                alt="<?php echo htmlspecialchars($product['pro_name']); ?>">
                            <span class="product-category"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                        </a>

                        <!-- Floating Wishlist Button -->
                        <a href="cart-action.php?action=wishlist_toggle&pro_id=<?php echo $p_id; ?>" 
                           style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: <?php echo $is_fav ? '#C62828' : '#222'; ?>; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.15);"
                           title="<?php echo $is_fav ? 'In Wishlist' : 'Add to Wishlist'; ?>">
                            <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart" style="font-size: 14px;"></i>
                        </a>
                    </div>

                    <!-- Information -->
                    <div class="product-info">
                        <h3>
                            <a href="product-detail.php?id=<?php echo $p_id; ?>">
                                <?php echo htmlspecialchars($product['pro_name']); ?>
                            </a>
                        </h3>

                        <p class="product-price">
                            PKR <?php echo number_format((float)$product['pro_price']); ?>
                        </p>

                        <?php if ((int)$product['pro_quan'] <= 0): ?>
                            <span class="stock-label">Out of Stock</span>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top mishae-product-card-footer">
                                <a href="product-detail.php?id=<?php echo $p_id; ?>" class="product-btn mishae-view-product">
                                    Buy Now <span>&rarr;</span>
                                </a>
                                <form action="cart-action.php" method="POST" class="m-0">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="pro_id" value="<?php echo $p_id; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-dark btn-sm text-uppercase mishae-quick-add-btn">
                                        <i class="fas fa-shopping-bag mr-1"></i> Quick Add
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="products-empty">
                <i class="fas fa-search text-muted mb-3" style="font-size: 32px;"></i>
                <h3>No Luxury Fabrics Found</h3>
                <p>We couldn't find any designs matching your specific selection.</p>
                <a href="products.php" class="btn btn-outline-dark text-uppercase mt-2" style="font-size: 11px; letter-spacing: 1px;">
                    View All Collections
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
