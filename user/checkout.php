<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'text' => 'Please log in to your account or register to complete your order.'
    ];
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit();
}

$is_buy_now = !empty($_SESSION['is_buy_now']) && !empty($_SESSION['buy_now_item']);

if ($is_buy_now) {
    $cart = ['buy_now' => $_SESSION['buy_now_item']];
} else {
    $cart = $_SESSION['cart'] ?? [];
}

if (empty($cart)) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Your shopping bag is empty. Please select products before proceeding to checkout.'
    ];
    header('Location: products.php');
    exit();
}

$subtotal = 0;
// Recalculate and validate prices and stock against database to prevent stale values or tampering
foreach ($cart as $key => $item) {
    $pid = (int)($item['pro_id'] ?? 0);
    $qty = max(1, (int)($item['quantity'] ?? 1));
    if ($pid > 0) {
        $p_check = mysqli_query($conn, "SELECT pro_price, pro_quan, pro_name, pro_img FROM product WHERE pro_id = $pid LIMIT 1");
        if ($p_check && $p_row = mysqli_fetch_assoc($p_check)) {
            $cart[$key]['pro_price'] = (float)$p_row['pro_price'];
            $cart[$key]['pro_name'] = $p_row['pro_name'];
            $cart[$key]['pro_img'] = $p_row['pro_img'];
            $max_s = (int)$p_row['pro_quan'];
            $qty = min($qty, $max_s);
            $cart[$key]['quantity'] = $qty;
        }
    }
    $line_total = $cart[$key]['pro_price'] * $qty;
    $subtotal += $line_total;
}

if ($is_buy_now) {
    $_SESSION['buy_now_item'] = reset($cart);
} else {
    $_SESSION['cart'] = $cart;
}

// Standard Delivery Fee: Flat PKR 250 per order
$shipping_fee = 250;

$discount = 0;
$coupon = $_SESSION['coupon'] ?? null;
if ($coupon) {
    if ($coupon['type'] === 'percent') {
        $discount = ($subtotal * $coupon['discount_percent']) / 100;
    } elseif ($coupon['type'] === 'shipping') {
        $shipping_fee = 0;
    }
}

$grand_total = max(0, $subtotal - $discount + $shipping_fee);

// Prefill Customer Delivery Information
$prefill_name = $_SESSION['customer_name'] ?? '';
$prefill_email = $_SESSION['customer_email'] ?? '';
$prefill_phone = $_SESSION['customer_phone'] ?? '';
$prefill_address = '';
$prefill_city = '';
$prefill_postal = '';

if (isset($_SESSION['customer_id'])) {
    $cid = (int)$_SESSION['customer_id'];
    $cq = mysqli_query($conn, "SELECT * FROM customers WHERE cus_id = $cid LIMIT 1");
    if ($cq && $c_row = mysqli_fetch_assoc($cq)) {
        if (!empty($c_row['cus_name'])) $prefill_name = $c_row['cus_name'];
        if (!empty($c_row['cus_email'])) $prefill_email = $c_row['cus_email'];
        if (!empty($c_row['cus_phone'])) $prefill_phone = $c_row['cus_phone'];
        $prefill_address = $c_row['cus_address'] ?? '';
        $prefill_city = $c_row['cus_city'] ?? '';
        $prefill_postal = $c_row['cus_postal'] ?? '';
    }
} elseif (isset($_SESSION['guest_checkout'])) {
    $prefill_name = $_SESSION['guest_checkout']['name'] ?? '';
    $prefill_email = $_SESSION['guest_checkout']['email'] ?? '';
    $prefill_phone = $_SESSION['guest_checkout']['phone'] ?? '';
    $prefill_address = $_SESSION['guest_checkout']['address'] ?? '';
    $prefill_city = $_SESSION['guest_checkout']['city'] ?? '';
    $prefill_postal = $_SESSION['guest_checkout']['postal'] ?? '';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $city = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
    $postal_code = mysqli_real_escape_string($conn, trim($_POST['postal_code'] ?? ''));
    $notes = mysqli_real_escape_string($conn, trim($_POST['order_notes'] ?? ''));
    $payment_method = mysqli_real_escape_string($conn, trim($_POST['payment_method'] ?? 'Cash on Delivery'));
    $save_as_default = isset($_POST['save_as_default']);

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($city)) {
        $error = 'Please fill in all required shipping and contact details.';
    } else {
        // Save guest details in session for convenience
        $_SESSION['guest_checkout'] = [
            'name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'postal' => $postal_code
        ];

        // 1. Resolve or Create Customer
        $cus_id = 0;
        $check_cus = mysqli_query($conn, "SELECT cus_id FROM customers WHERE cus_email = '$email' LIMIT 1");
        if ($check_cus && mysqli_num_rows($check_cus) > 0) {
            $cus_row = mysqli_fetch_assoc($check_cus);
            $cus_id = (int)$cus_row['cus_id'];
            if ($save_as_default || isset($_SESSION['customer_id'])) {
                mysqli_query($conn, "UPDATE customers SET cus_name = '$full_name', cus_phone = '$phone', cus_address = '$address', cus_city = '$city', cus_postal = '$postal_code' WHERE cus_id = $cus_id");
            }
        } else {
            // Create guest record
            $insert_cus = mysqli_query($conn, "INSERT INTO customers (cus_name, cus_email, cus_phone, cus_address, cus_city, cus_postal) 
                                                VALUES ('$full_name', '$email', '$phone', '$address', '$city', '$postal_code')");
            if ($insert_cus) {
                $cus_id = mysqli_insert_id($conn);
            }
        }

        // 2. Prepare Shipping Address blob
        $full_shipping = "Name: $full_name\nPhone: $phone\nAddress: $address\nCity: $city\nPostal Code: $postal_code";
        if (!empty($notes)) {
            $full_shipping .= "\nNotes: $notes";
        }

        // 3. Insert Order
        $ord_query = "INSERT INTO orders (cus_id, ord_date, ord_total, ord_status, pay_method, pay_status, ship_address) 
                      VALUES ($cus_id, NOW(), $grand_total, 'Pending', '$payment_method', 'Pending', '$full_shipping')";

        if (mysqli_query($conn, $ord_query)) {
            $ord_id = mysqli_insert_id($conn);

            // 4. Insert Order Items & Deduct Product Quantity
            foreach ($cart as $key => $item) {
                $p_id = (int)($item['pro_id'] ?? $key);
                $p_price = (float)$item['pro_price'];
                $p_qty = (int)$item['quantity'];
                $p_option = mysqli_real_escape_string($conn, $item['option'] ?? 'Standard Piece');

                mysqli_query($conn, "INSERT INTO order_items (ord_id, pro_id, item_price, item_quan, item_option) 
                                     VALUES ($ord_id, $p_id, $p_price, $p_qty, '$p_option')");

                mysqli_query($conn, "UPDATE product SET pro_quan = GREATEST(0, pro_quan - $p_qty) WHERE pro_id = $p_id");

                // If this piece was also in regular cart, remove it
                if ($is_buy_now && isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $c_k => $c_item) {
                        if ((int)($c_item['pro_id'] ?? 0) === $p_id) {
                            unset($_SESSION['cart'][$c_k]);
                        }
                    }
                }
            }

            // 5. Clear Order State
            if ($is_buy_now) {
                unset($_SESSION['buy_now_item'], $_SESSION['is_buy_now'], $_SESSION['coupon']);
            } else {
                $_SESSION['cart'] = [];
                unset($_SESSION['coupon']);
            }
            $_SESSION['last_order'] = [
                'ord_id' => $ord_id,
                'name' => $full_name,
                'email' => $email,
                'total' => $grand_total
            ];

            header("Location: order-confirmation.php?ord_id=$ord_id");
            exit();
        } else {
            $error = 'Failed to record your order due to a database issue. Please try again or contact concierge.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details &amp; Checkout | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
    <link rel="stylesheet" href="assets/css/checkout.css?v=3.7">
</head>
<body>

<?php include __DIR__ . '/include/topbar.php'; ?>
<?php include __DIR__ . '/include/navbar.php'; ?>

<main class="mishae-checkout-wrapper">
    <div class="mishae-checkout-container">
        <!-- Breadcrumb Navigation -->
        <div class="checkout-breadcrumb">
            <a href="home.php">Home</a>
            <span>/</span>
            <a href="cart.php">Shopping Bag</a>
            <span>/</span>
            <strong>Order Details &amp; Checkout</strong>
        </div>

        <!-- Section Header -->
        <div class="checkout-intro-header">
            <span class="checkout-intro-subtitle">Direct Atelier Dispatch &bull; Express Courier</span>
            <h1 class="checkout-intro-title">Complete Your Order Details</h1>
            <p class="checkout-intro-desc">
                Review your selected unstitched pieces, enter your delivery address, and confirm your order for priority dispatch.
            </p>
        </div>

        <!-- Validation Alert -->
        <?php if (!empty($error)): ?>
            <div class="mishae-alert mishae-alert-danger mb-4" role="alert">
                <span><i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?></span>
                <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Checkout Form -->
        <form action="checkout.php" method="POST" id="checkoutForm" novalidate>
            <div class="checkout-main-grid">
                <!-- Left Column: Customer & Delivery Details -->
                <div class="checkout-form-column">
                    <!-- Section 1: Customer Information -->
                    <div class="checkout-card">
                        <div class="checkout-card-head">
                            <div class="checkout-card-head-title">
                                <span class="checkout-step-badge">1</span>
                                <h2>Customer Information</h2>
                            </div>
                            <span class="checkout-card-head-extra">Contact Coordinates</span>
                        </div>

                        <div class="checkout-field-row">
                            <div class="checkout-form-group">
                                <label for="fieldFullName">Full Name <span class="req">*</span></label>
                                <input type="text" 
                                       id="fieldFullName"
                                       name="full_name" 
                                       required 
                                       value="<?php echo htmlspecialchars($prefill_name); ?>" 
                                       placeholder="e.g. Ayesha Khan" 
                                       autocomplete="name">
                            </div>

                            <div class="checkout-form-group">
                                <label for="fieldEmail">Email Address <span class="req">*</span></label>
                                <input type="email" 
                                       id="fieldEmail"
                                       name="email" 
                                       required 
                                       value="<?php echo htmlspecialchars($prefill_email); ?>" 
                                       placeholder="e.g. ayesha@example.com" 
                                       autocomplete="email">
                            </div>
                        </div>

                        <div class="checkout-form-group mb-0">
                            <label for="fieldPhone">WhatsApp / Mobile Phone <span class="req">*</span></label>
                            <input type="tel" 
                                   id="fieldPhone"
                                   name="phone" 
                                   required 
                                   value="<?php echo htmlspecialchars($prefill_phone); ?>" 
                                   placeholder="e.g. 0300 1234567" 
                                   autocomplete="tel">
                            <small>Our courier partner coordinates delivery via WhatsApp/SMS to this number.</small>
                        </div>
                    </div>

                    <!-- Section 2: Delivery Destination -->
                    <div class="checkout-card">
                        <div class="checkout-card-head">
                            <div class="checkout-card-head-title">
                                <span class="checkout-step-badge">2</span>
                                <h2>Delivery Address</h2>
                            </div>
                            <span class="checkout-card-head-extra">Pakistan Courier</span>
                        </div>

                        <div class="checkout-form-group">
                            <label for="fieldAddress">Street Address / House / Flat # <span class="req">*</span></label>
                            <textarea id="fieldAddress"
                                      name="address" 
                                      rows="2" 
                                      required 
                                      placeholder="House/Apartment #, Street name, Sector, Block, or Phase" 
                                      autocomplete="street-address"><?php echo htmlspecialchars($prefill_address); ?></textarea>
                        </div>

                        <div class="checkout-field-row">
                            <div class="checkout-form-group">
                                <label for="fieldCity">City <span class="req">*</span></label>
                                <select id="fieldCity" name="city" required>
                                    <option value="">Select Delivery City</option>
                                    <?php
                                    $cities = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala', 'Hyderabad', 'Bahawalpur', 'Sargodha', 'Sukkur', 'Larkana', 'Other'];
                                    foreach ($cities as $c) {
                                        $sel = (strcasecmp($prefill_city, $c) === 0) ? 'selected' : '';
                                        echo "<option value=\"$c\" $sel>$c</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="checkout-form-group">
                                <label for="fieldPostal">Postal / Zip Code</label>
                                <input type="text" 
                                       id="fieldPostal"
                                       name="postal_code" 
                                       value="<?php echo htmlspecialchars($prefill_postal); ?>" 
                                       placeholder="e.g. 75500" 
                                       autocomplete="postal-code">
                            </div>
                        </div>

                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <label class="checkout-checkbox-wrap">
                                <input type="checkbox" name="save_as_default" value="1" checked>
                                <span>Save this address as my primary delivery address</span>
                            </label>
                        <?php endif; ?>

                        <div class="checkout-form-group mb-0">
                            <label for="fieldNotes">Dispatch Instructions / Notes</label>
                            <input type="text" 
                                   id="fieldNotes"
                                   name="order_notes" 
                                   placeholder="e.g. Ring the bell, deliver after 2:00 PM, near landmark">
                        </div>
                    </div>

                    <!-- Section 3: Payment Method -->
                    <div class="checkout-card mb-0">
                        <div class="checkout-card-head">
                            <div class="checkout-card-head-title">
                                <span class="checkout-step-badge">3</span>
                                <h2>Payment Method</h2>
                            </div>
                            <span class="checkout-card-head-extra">Safe &amp; Verified</span>
                        </div>

                        <div class="checkout-payment-methods">
                            <!-- Option 1: COD -->
                            <label class="payment-method-option selected" id="methodCodOption">
                                <div class="payment-method-left">
                                    <input type="radio" name="payment_method" value="Cash on Delivery" checked>
                                    <div class="payment-method-info">
                                        <strong>Cash on Delivery (COD)</strong>
                                        <span>Pay with cash upon parcel handover at your doorstep</span>
                                    </div>
                                </div>
                                <i class="fas fa-money-bill-wave payment-method-icon" style="color: #2E7D32;"></i>
                            </label>

                            <!-- Option 2: Bank Transfer -->
                            <label class="payment-method-option" id="methodBankOption">
                                <div class="payment-method-left">
                                    <input type="radio" name="payment_method" value="Direct Bank Transfer">
                                    <div class="payment-method-info">
                                        <strong>Direct Bank Transfer</strong>
                                        <span>Meezan Bank &bull; MiSHAÉ Luxury Fabrics Atelier</span>
                                    </div>
                                </div>
                                <i class="fas fa-university payment-method-icon"></i>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary Sidebar -->
                <div class="checkout-sidebar-column">
                    <div class="checkout-sidebar-card">
                        <div class="checkout-sidebar-header">
                            <h3>Order Overview</h3>
                            <span class="checkout-sidebar-count">
                                <?php echo count($cart); ?> <?php echo count($cart) === 1 ? 'Piece' : 'Pieces'; ?>
                            </span>
                        </div>

                        <?php if ($is_buy_now): ?>
                            <div class="checkout-direct-badge">
                                <i class="fas fa-bolt"></i> Direct Buy Now Purchase
                            </div>
                        <?php endif; ?>

                        <!-- Products List -->
                        <div class="checkout-items-scroll">
                            <?php foreach ($cart as $item): 
                                $line_total = $item['pro_price'] * $item['quantity'];
                                $img_filename = $item['pro_img'] ?? 'logo.png';
                                $img_path = '../admin/uploads/' . htmlspecialchars($img_filename);
                            ?>
                                <div class="checkout-item-row">
                                    <div class="checkout-item-media">
                                        <img src="<?php echo $img_path; ?>" 
                                             alt="<?php echo htmlspecialchars($item['pro_name']); ?>" 
                                             class="checkout-item-thumb"
                                             onerror="this.src='assets/images/logo.png'">
                                        <span class="checkout-item-qty-pill"><?php echo (int)$item['quantity']; ?></span>
                                    </div>

                                    <div class="checkout-item-details">
                                        <div class="checkout-item-title" title="<?php echo htmlspecialchars($item['pro_name']); ?>">
                                            <?php echo htmlspecialchars($item['pro_name']); ?>
                                        </div>
                                        <div class="checkout-item-meta">
                                            <?php if (!empty($item['cat_name'])): ?>
                                                <span><?php echo htmlspecialchars($item['cat_name']); ?> &bull; </span>
                                            <?php endif; ?>
                                            <span>PKR <?php echo number_format($item['pro_price']); ?> each</span>
                                        </div>
                                        <?php if (!empty($item['option']) && $item['option'] !== 'Standard Piece'): ?>
                                            <div class="checkout-item-meta text-dark font-weight-500">
                                                Option: <?php echo htmlspecialchars($item['option']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="checkout-item-price">
                                        PKR <?php echo number_format($line_total); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Financial Calculation Breakdown -->
                        <div class="checkout-breakdown">
                            <div class="checkout-breakdown-row">
                                <span>Products Subtotal</span>
                                <span>PKR <?php echo number_format($subtotal); ?></span>
                            </div>

                            <div class="checkout-breakdown-row">
                                <span>Nationwide Courier Fee</span>
                                <span>
                                    <?php if ($shipping_fee > 0): ?>
                                        PKR <?php echo number_format($shipping_fee); ?>
                                    <?php else: ?>
                                        <span class="text-success font-weight-bold">COMPLIMENTARY</span>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <?php if ($discount > 0): ?>
                                <div class="checkout-breakdown-row discount-row">
                                    <span>Privilege Code (<?php echo htmlspecialchars($coupon['code'] ?? ''); ?>)</span>
                                    <span>&minus; PKR <?php echo number_format($discount); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="checkout-breakdown-row total-row">
                                <span>Final Payable Total</span>
                                <span class="total-amount">PKR <?php echo number_format($grand_total); ?></span>
                            </div>
                        </div>

                        <!-- Primary Confirm & Place Order Button -->
                        <button type="submit" name="place_order" id="btnPlaceOrder" class="btn-place-order">
                            <i class="fas fa-lock"></i>
                            <span>CONFIRM &amp; PLACE ORDER</span>
                        </button>

                        <div class="checkout-trust-notes">
                            <i class="fas fa-shield-alt"></i> 100% Original Pakistani Luxury Fabrics &bull; Inspected Dispatch
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . '/include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment Method Selection Toggle
    var paymentCards = document.querySelectorAll('.payment-method-option');
    paymentCards.forEach(function(card) {
        var radio = card.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function() {
                paymentCards.forEach(function(c) { c.classList.remove('selected'); });
                if (this.checked) {
                    card.classList.add('selected');
                }
            });
        }
    });

    // Form submission validation highlight
    var form = document.getElementById('checkoutForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            var requiredInputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            var firstInvalid = null;
            requiredInputs.forEach(function(inp) {
                if (!inp.value.trim()) {
                    inp.style.borderColor = '#A33B3B';
                    if (!firstInvalid) firstInvalid = inp;
                } else {
                    inp.style.borderColor = '';
                }
            });
            if (firstInvalid) {
                firstInvalid.focus();
            }
        });
    }
});
</script>

</body>
</html>
