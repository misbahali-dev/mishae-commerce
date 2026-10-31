<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'text' => 'Please log in to your account or register to view your shopping bag.'
    ];
    $_SESSION['redirect_after_login'] = 'cart.php';
    header('Location: login.php');
    exit();
}

// Visiting the shopping cart resets any transient Buy Now selection
unset($_SESSION['is_buy_now'], $_SESSION['buy_now_item']);

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;

// Validate prices with DB to prevent stale session prices
foreach ($cart as $key => $item) {
    $pid = (int)($item['pro_id'] ?? 0);
    if ($pid > 0) {
        $p_check = mysqli_query($conn, "SELECT pro_price, pro_quan FROM product WHERE pro_id = $pid LIMIT 1");
        if ($p_check && $p_row = mysqli_fetch_assoc($p_check)) {
            $cart[$key]['pro_price'] = (float)$p_row['pro_price'];
            $max_s = (int)$p_row['pro_quan'];
            $cart[$key]['quantity'] = min((int)$item['quantity'], $max_s);
        }
    }
    $subtotal += ($cart[$key]['pro_price'] * $cart[$key]['quantity']);
}
$_SESSION['cart'] = $cart;

// Flat Delivery Charge: PKR 250 per order
$standard_shipping = 250;
$shipping_fee = empty($cart) ? 0 : $standard_shipping;

// Coupon Calculation
$discount = 0;
$coupon = $_SESSION['coupon'] ?? null;
if ($coupon && !empty($cart)) {
    if ($coupon['type'] === 'percent') {
        $discount = ($subtotal * $coupon['discount_percent']) / 100;
    } elseif ($coupon['type'] === 'shipping') {
        $shipping_fee = 0;
    }
}

$grand_total = max(0, $subtotal - $discount + $shipping_fee);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Bag | MiSHAÉ Luxury Fabrics</title>

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
<body>

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<main class="cart-page-wrapper">
    <!-- Flash Notifications -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mishae-alert mishae-alert-<?php echo $_SESSION['flash_message']['type']; ?>" id="cartAlert">
            <span><i class="fas fa-info-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['flash_message']['text']); ?></span>
            <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close Alert">&times;</button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="page-intro-header mb-4">
        <div class="container text-center">
            <h1 class="page-intro-title">Shopping Bag</h1>
            <p class="page-intro-desc">Review your selected pieces before proceeding to checkout.</p>
        </div>
    </div>

    <?php if (!empty($cart)): ?>
        <div class="cart-layout">
            <!-- Items List -->
            <div class="cart-items-card">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Item Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $key => $item): 
                            $line_total = $item['pro_price'] * $item['quantity'];
                            $p_id = (int)($item['pro_id'] ?? $key);
                            $cart_key = $item['cart_key'] ?? $key;
                            $option = $item['option'] ?? 'Unstitched (Standard 3-Piece)';
                        ?>
                            <tr>
                                <td>
                                    <div class="cart-product-cell">
                                        <img src="../admin/uploads/<?php echo htmlspecialchars($item['pro_img']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['pro_name']); ?>" 
                                             class="cart-thumb">
                                        <div>
                                            <span class="cart-item-cat"><?php echo htmlspecialchars($item['cat_name'] ?? 'Luxury Edition'); ?></span>
                                            <a href="product-detail.php?id=<?php echo $p_id; ?>" class="cart-item-title">
                                                <?php echo htmlspecialchars($item['pro_name']); ?>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="cart-item-price">PKR <?php echo number_format($item['pro_price']); ?></span>
                                    <small class="text-muted d-block">per dress</small>
                                </td>
                                <td>
                                    <form action="cart-action.php" method="POST" class="d-inline-flex align-items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($cart_key); ?>">
                                        <div class="qty-control" style="transform: scale(0.9); transform-origin: left;">
                                            <button type="submit" name="quantity" value="<?php echo max(0, $item['quantity'] - 1); ?>" class="qty-btn" title="Decrease Quantity">&minus;</button>
                                            <span class="qty-input d-flex align-items-center justify-content-center"><?php echo $item['quantity']; ?></span>
                                            <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn" title="Increase Quantity">&plus;</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <span class="cart-item-price font-weight-bold">PKR <?php echo number_format($line_total); ?></span>
                                    <small class="text-muted d-block"><?php echo $item['quantity']; ?> × PKR <?php echo number_format($item['pro_price']); ?></small>
                                </td>
                                <td class="text-right">
                                    <a href="javascript:void(0);" 
                                       class="cart-remove-btn" 
                                       title="Remove piece" 
                                       onclick="confirmCartRemove('cart-action.php?action=remove&key=<?php echo urlencode($cart_key); ?>', '<?php echo htmlspecialchars($item['pro_name'], ENT_QUOTES); ?>');">
                                        <i class="far fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="products.php" class="btn btn-outline-dark btn-sm text-uppercase" style="letter-spacing: 1px; font-size: 11px;">
                        &larr; Continue Shopping
                    </a>
                    <a href="javascript:void(0);" 
                       class="text-danger small" 
                       onclick="confirmClearBag('cart-action.php?action=clear');">
                        <i class="far fa-trash-alt mr-1"></i> Clear Shopping Bag
                    </a>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="cart-summary-card">
                <h3>Order Summary</h3>

                <div class="summary-row">
                    <span>Products Subtotal</span>
                    <span>PKR <?php echo number_format($subtotal); ?></span>
                </div>

                <div class="summary-row">
                    <span>Standard Delivery Fee</span>
                    <span>PKR <?php echo number_format($shipping_fee); ?></span>
                </div>

                <?php if ($discount > 0): ?>
                    <div class="summary-row text-success">
                        <span>Privilege Discount (<?php echo htmlspecialchars($coupon['code']); ?>)</span>
                        <span>- PKR <?php echo number_format($discount); ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-row total-row">
                    <span>Payable Total</span>
                    <span>PKR <?php echo number_format($grand_total); ?></span>
                </div>
                <small class="text-muted d-block mt-1">Delivery fee is PKR 250 flat per order, calculated once regardless of item count.</small>

                <!-- Coupon Form -->
                <form action="cart-action.php" method="POST" class="coupon-form mt-3">
                    <input type="hidden" name="action" value="apply_coupon">
                    <input type="text" name="coupon_code" placeholder="Privilege Code (e.g. MISHAE15)" class="coupon-input" required>
                    <button type="submit" class="coupon-btn">APPLY</button>
                </form>

                <?php if ($coupon): ?>
                    <div class="p-2 bg-light border rounded small d-flex justify-content-between align-items-center mb-3">
                        <span class="text-success"><i class="fas fa-tag mr-1"></i> <strong><?php echo htmlspecialchars($coupon['code']); ?></strong> applied</span>
                        <a href="cart-action.php?action=remove_coupon" class="text-danger ml-2">&times; Remove</a>
                    </div>
                <?php endif; ?>

                <a href="checkout.php" class="btn-checkout">
                    PROCEED TO CHECKOUT &rarr;
                </a>

                <div class="mt-4 pt-3 border-top text-center" style="font-size: 11px; color: #888;">
                    <i class="fas fa-shield-alt mr-1 text-dark"></i> Guaranteed Authentic Fabric &bull; Safe Checkout
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-5 bg-white border rounded shadow-sm my-4" style="max-width: 600px; margin: 0 auto;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #FAF8F5; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i class="fas fa-shopping-bag" style="font-size: 36px; color: #C5A880;"></i>
            </div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 26px;">Your Shopping Bag is Empty</h3>
            <p class="text-muted px-4" style="font-size: 14px;">
                Looks like you haven't added any luxury pieces yet. Explore our unstitched lawn, festive chiffon, and raw silk collections.
            </p>
            <a href="products.php" class="btn btn-dark text-uppercase px-4 py-2 mt-3" style="font-size: 11px; letter-spacing: 2px;">
                Explore Collections &rarr;
            </a>
        </div>
    <?php endif; ?>
</main>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const cartAlert = document.getElementById('cartAlert');
if (cartAlert) {
    setTimeout(function() {
        cartAlert.style.transition = 'opacity 0.5s ease';
        cartAlert.style.opacity = '0';
        setTimeout(() => { cartAlert.remove(); }, 500);
    }, 3500);
}
</script>

</body>
</html>
