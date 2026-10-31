<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

$pro_id = (int)($_GET['id'] ?? $_GET['pro_id'] ?? 0);
if ($pro_id <= 0) {
    header('Location: products.php');
    exit();
}

$query = "SELECT product.*, category.cat_name, category.cat_id 
          FROM product 
          LEFT JOIN category ON product.cat_id = category.cat_id 
          WHERE product.pro_id = $pro_id LIMIT 1";

$res = mysqli_query($conn, $query);
if (!$res || mysqli_num_rows($res) === 0) {
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($res);
$cat_id = (int)$product['cat_id'];
$unit_price = (float)$product['pro_price'];
$stock = (int)$product['pro_quan'];

// Fetch Related Products from same category
$related_res = mysqli_query($conn, "SELECT product.*, category.cat_name 
                                    FROM product 
                                    LEFT JOIN category ON product.cat_id = category.cat_id 
                                    WHERE product.cat_id = $cat_id AND product.pro_id != $pro_id 
                                    ORDER BY product.pro_id DESC LIMIT 4");

$in_wishlist = isset($_SESSION['wishlist'][$pro_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['pro_name']); ?> | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/upro.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body>

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<main class="pdetail-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="pdetail-breadcrumb">
        <a href="home.php">Home</a>
        <span>/</span>
        <a href="products.php">All Fabrics</a>
        <span>/</span>
        <a href="products.php?cat_id=<?php echo $cat_id; ?>"><?php echo htmlspecialchars($product['cat_name']); ?></a>
        <span>/</span>
        <strong class="text-dark"><?php echo htmlspecialchars($product['pro_name']); ?></strong>
    </div>

    <!-- Flash Notifications -->
    <div id="pdetailAlertContainer">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mishae-alert mishae-alert-<?php echo $_SESSION['flash_message']['type']; ?>" id="sessionAlert">
                <span><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['flash_message']['text']); ?></span>
                <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close Alert">&times;</button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
    </div>

    <!-- Clean, Focused 2-Column Product Layout -->
    <div class="pdetail-grid">
        <!-- Left Column: Product Image -->
        <div class="pdetail-image-column">
            <div class="pdetail-gallery">
                <img src="../admin/uploads/<?php echo htmlspecialchars($product['pro_img']); ?>" 
                     alt="<?php echo htmlspecialchars($product['pro_name']); ?>" 
                     class="pdetail-main-img" 
                     id="pdetailMainImage">
            </div>
        </div>

        <!-- Right Column: Product Information & Purchase Actions -->
        <div class="pdetail-info-column">
            <div class="pdetail-category">
                <?php echo htmlspecialchars($product['cat_name'] ?? 'Luxury Edition'); ?>
            </div>

            <h1 class="pdetail-title">
                <?php echo htmlspecialchars($product['pro_name']); ?>
            </h1>

            <!-- Stock Availability Badge -->
            <?php if ($stock >= 5): ?>
                <div class="pdetail-stock-badge pdetail-in-stock">
                    <i class="fas fa-check-circle mr-1"></i> In Stock
                </div>
            <?php elseif ($stock > 0): ?>
                <div class="pdetail-stock-badge pdetail-low-stock">
                    <i class="fas fa-fire mr-1"></i> Only <?php echo $stock; ?> Left in Stock
                </div>
            <?php else: ?>
                <div class="pdetail-stock-badge pdetail-out-stock">
                    <i class="fas fa-times-circle mr-1"></i> Currently Sold Out
                </div>
            <?php endif; ?>

            <!-- Clear Unit Price -->
            <div class="pdetail-price-box">
                <span class="pdetail-price">PKR <?php echo number_format($unit_price); ?></span>
            </div>

            <!-- Product Description -->
            <div class="pdetail-description">
                <p><?php echo nl2br(htmlspecialchars($product['pro_des'])); ?></p>
            </div>

            <?php if ($stock > 0): ?>
                <!-- Purchase Form (No Sizes) -->
                <form action="cart-action.php" method="POST" id="productPurchaseForm">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="pro_id" value="<?php echo $pro_id; ?>">
                    <input type="hidden" name="action_type" id="actionTypeInput" value="add_cart">

                    <!-- Compact Quantity & Live Subtotal Bar -->
                    <div class="pdetail-order-bar">
                        <div class="pdetail-qty-wrap">
                            <label>Quantity:</label>
                            <div class="qty-control">
                                <button type="button" class="qty-btn" id="btnQtyMinus" title="Decrease">&minus;</button>
                                <input type="text" name="quantity" id="qtyInput" value="1" readonly class="qty-input">
                                <button type="button" class="qty-btn" id="btnQtyPlus" title="Increase">&plus;</button>
                            </div>
                        </div>

                        <div class="pdetail-subtotal-wrap">
                            <span class="subtotal-label">Subtotal:</span>
                            <span class="subtotal-amount" id="liveSubtotal">PKR <?php echo number_format($unit_price); ?></span>
                        </div>
                    </div>

                    <!-- Purchase Action Buttons -->
                    <div class="pdetail-action-buttons">
                        <!-- Add to Cart -->
                        <button type="submit" id="btnAddToCart" class="btn-add-to-cart">
                            <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                        </button>

                        <!-- Buy Now (Direct to Checkout) -->
                        <button type="submit" id="btnBuyNow" class="btn-buy-now">
                            Buy Now &rarr;
                        </button>

                        <!-- Wishlist Toggle -->
                        <a href="cart-action.php?action=wishlist_toggle&pro_id=<?php echo $pro_id; ?>" 
                           class="btn-wishlist-toggle" 
                           title="<?php echo $in_wishlist ? 'Saved in Wishlist' : 'Save to Wishlist'; ?>"
                           aria-label="Save to Wishlist">
                            <i class="<?php echo $in_wishlist ? 'fas text-danger' : 'far'; ?> fa-heart"></i>
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <div class="p-3 bg-light rounded text-center text-muted mb-3 border">
                    <p class="mb-0 small">This design is currently sold out. Contact concierge for custom orders.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Related Luxury Products Section -->
    <?php if ($related_res && mysqli_num_rows($related_res) > 0): ?>
        <section class="mt-5 pt-3">
            <div class="products-heading text-center mb-4">
                <p class="section-subtitle">CURATED HARMONY</p>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 24px;">You May Also Admire</h2>
                <p class="text-muted small">Complementary pieces from our latest couture atelier.</p>
            </div>

            <div class="products-grid">
                <?php while ($rel = mysqli_fetch_assoc($related_res)): ?>
                    <div class="product-card">
                        <a href="product-detail.php?id=<?php echo (int)$rel['pro_id']; ?>" class="product-image">
                            <img src="../admin/uploads/<?php echo htmlspecialchars($rel['pro_img']); ?>" 
                                 alt="<?php echo htmlspecialchars($rel['pro_name']); ?>">
                            <span class="product-category"><?php echo htmlspecialchars($rel['cat_name']); ?></span>
                        </a>
                        <div class="product-info">
                            <h3>
                                <a href="product-detail.php?id=<?php echo (int)$rel['pro_id']; ?>">
                                    <?php echo htmlspecialchars($rel['pro_name']); ?>
                                </a>
                            </h3>
                            <p class="product-price">PKR <?php echo number_format($rel['pro_price']); ?></p>
                            <a href="product-detail.php?id=<?php echo (int)$rel['pro_id']; ?>" class="product-btn">
                                Buy Now <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function() {
    const unitPrice = <?php echo (float)$unit_price; ?>;
    const maxStock = <?php echo (int)$stock; ?>;

    const qtyInput = document.getElementById('qtyInput');
    const liveSubtotal = document.getElementById('liveSubtotal');
    const actionTypeInput = document.getElementById('actionTypeInput');

    function formatNumber(num) {
        return num.toLocaleString('en-PK');
    }

    function recalculate() {
        if (!qtyInput) return;
        let qty = parseInt(qtyInput.value) || 1;
        if (qty < 1) qty = 1;
        if (qty > maxStock) qty = maxStock;
        qtyInput.value = qty;

        const subtotal = unitPrice * qty;
        if (liveSubtotal) {
            liveSubtotal.textContent = 'PKR ' + formatNumber(subtotal);
        }
    }

    // Quantity buttons
    const btnMinus = document.getElementById('btnQtyMinus');
    const btnPlus = document.getElementById('btnQtyPlus');

    if (btnMinus) {
        btnMinus.addEventListener('click', function() {
            let current = parseInt(qtyInput.value) || 1;
            if (current > 1) {
                qtyInput.value = current - 1;
                recalculate();
            }
        });
    }

    if (btnPlus) {
        btnPlus.addEventListener('click', function() {
            let current = parseInt(qtyInput.value) || 1;
            if (current < maxStock) {
                qtyInput.value = current + 1;
                recalculate();
            }
        });
    }

    // Button actions: Add to Cart vs Buy Now
    const btnAddToCart = document.getElementById('btnAddToCart');
    const btnBuyNow = document.getElementById('btnBuyNow');

    if (btnAddToCart) {
        btnAddToCart.addEventListener('click', function(e) {
            if (actionTypeInput) actionTypeInput.value = 'add_cart';
        });
    }

    if (btnBuyNow) {
        btnBuyNow.addEventListener('click', function(e) {
            if (actionTypeInput) actionTypeInput.value = 'buy_now';
        });
    }

    // Auto dismiss session alerts smoothly
    const sessionAlert = document.getElementById('sessionAlert');
    if (sessionAlert) {
        setTimeout(function() {
            sessionAlert.style.transition = 'opacity 0.5s ease';
            sessionAlert.style.opacity = '0';
            setTimeout(() => { sessionAlert.remove(); }, 500);
        }, 3500);
    }
})();
</script>

</body>
</html>
