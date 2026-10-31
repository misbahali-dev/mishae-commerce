<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

$ord_id = (int)($_GET['ord_id'] ?? 0);
if ($ord_id <= 0) {
    header('Location: home.php');
    exit();
}

// Fetch Order
$ord_res = mysqli_query($conn, "SELECT * FROM orders WHERE ord_id = $ord_id LIMIT 1");
if (!$ord_res || mysqli_num_rows($ord_res) === 0) {
    header('Location: home.php');
    exit();
}
$order = mysqli_fetch_assoc($ord_res);

// Fetch Items
$items_res = mysqli_query($conn, "SELECT order_items.*, product.pro_name, product.pro_img, category.cat_name 
                                  FROM order_items 
                                  LEFT JOIN product ON order_items.pro_id = product.pro_id 
                                  LEFT JOIN category ON product.cat_id = category.cat_id 
                                  WHERE order_items.ord_id = $ord_id");

$items = [];
$items_subtotal = 0;
while ($it = mysqli_fetch_assoc($items_res)) {
    $items[] = $it;
    $items_subtotal += ((float)$it['item_price'] * (int)$it['item_quan']);
}
$shipping_fee = max(0, (float)$order['ord_total'] - $items_subtotal);

// Helper function to parse shipping address
function parseShippingAddress($blob) {
    $info = [
        'name' => '',
        'phone' => '',
        'address' => '',
        'city' => '',
        'postal' => '',
        'notes' => ''
    ];
    $lines = explode("\n", str_replace("\r", "", (string)$blob));
    foreach ($lines as $line) {
        $line = trim($line);
        if (stripos($line, 'Name:') === 0) {
            $info['name'] = trim(substr($line, 5));
        } elseif (stripos($line, 'Phone:') === 0) {
            $info['phone'] = trim(substr($line, 6));
        } elseif (stripos($line, 'Address:') === 0) {
            $info['address'] = trim(substr($line, 8));
        } elseif (stripos($line, 'City:') === 0) {
            $info['city'] = trim(substr($line, 5));
        } elseif (stripos($line, 'Postal Code:') === 0 || stripos($line, 'Postal:') === 0) {
            $info['postal'] = trim(substr($line, strpos($line, ':') + 1));
        } elseif (stripos($line, 'Notes:') === 0) {
            $info['notes'] = trim(substr($line, 6));
        }
    }
    return $info;
}
$shipping_info = parseShippingAddress($order['ship_address'] ?? '');

$ord_status_raw = strtolower(trim($order['ord_status'] ?? 'pending'));
$status_badge_class = 'badge-warning';
if (in_array($ord_status_raw, ['delivered', 'completed'])) {
    $status_badge_class = 'badge-success';
} elseif (in_array($ord_status_raw, ['shipped', 'dispatched'])) {
    $status_badge_class = 'badge-info';
} elseif (in_array($ord_status_raw, ['cancelled'])) {
    $status_badge_class = 'badge-danger';
}

$step1_done = true;
$step2_done = in_array($ord_status_raw, ['processing', 'shipped', 'dispatched', 'delivered', 'completed']);
$step3_done = in_array($ord_status_raw, ['shipped', 'dispatched', 'delivered', 'completed']);
$step4_done = in_array($ord_status_raw, ['delivered', 'completed']);

$is_logged_in = isset($_SESSION['customer_id']);
$wa_message = "Hello MiSHAÉ Concierge, I would like to inquire regarding my order #ORD-{$ord_id}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Order #ORD-<?php echo $ord_id; ?> | MiSHAÉ Luxury Fabrics</title>

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

<main class="mishae-success-wrapper">
    <div class="mishae-success-container">
        <!-- Breadcrumb Navigation -->
        <div class="checkout-breadcrumb">
            <a href="home.php">Home</a>
            <span>/</span>
            <a href="cart.php">Shopping Bag</a>
            <span>/</span>
            <strong>Order #ORD-<?php echo $ord_id; ?> Confirmation</strong>
        </div>

        <div class="success-card">
            <!-- Hero Header -->
            <div class="success-hero">
                <div class="success-icon-badge">
                    <i class="fas fa-check"></i>
                </div>

                <span class="success-hero-overline">Order Successfully Placed &bull; Atelier Dispatch</span>
                <h1 class="success-hero-title">Thank You for Your Order</h1>
                <p class="success-hero-message">
                    Your bespoke unstitched order has been safely placed and recorded. Our team is preparing your pieces with signature care and packaging.
                </p>

                <div class="success-ref-pill">
                    <span>ORDER REFERENCE:</span>
                    <code>#ORD-<?php echo $ord_id; ?></code>
                    <button type="button" class="btn-copy-ref" onclick="copyOrderRef('#ORD-<?php echo $ord_id; ?>', this)" title="Copy Reference">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
            </div>

            <!-- Visual Progress Stepper -->
            <div class="success-stepper-wrap">
                <div class="success-stepper">
                    <!-- Step 1: Placed -->
                    <div class="stepper-step completed">
                        <div class="stepper-circle"><i class="fas fa-check"></i></div>
                        <span class="stepper-name">Order Placed</span>
                        <span class="stepper-detail"><?php echo date('M d, h:i A', strtotime($order['ord_date'])); ?></span>
                    </div>

                    <!-- Step 2: Processing -->
                    <div class="stepper-step <?php echo $step2_done ? 'completed' : 'active'; ?>">
                        <div class="stepper-circle">
                            <?php if ($step2_done): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <i class="fas fa-cog fa-spin"></i>
                            <?php endif; ?>
                        </div>
                        <span class="stepper-name">Processing</span>
                        <span class="stepper-detail"><?php echo $step2_done ? 'Completed' : 'In Preparation'; ?></span>
                    </div>

                    <!-- Step 3: Packaging -->
                    <div class="stepper-step <?php echo $step3_done ? 'completed' : ($step2_done ? 'active' : ''); ?>">
                        <div class="stepper-circle">
                            <?php if ($step3_done): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <i class="fas fa-box-open"></i>
                            <?php endif; ?>
                        </div>
                        <span class="stepper-name">Packaging</span>
                        <span class="stepper-detail">Signature Box</span>
                    </div>

                    <!-- Step 4: Express Delivery -->
                    <div class="stepper-step <?php echo $step4_done ? 'completed' : ''; ?>">
                        <div class="stepper-circle">
                            <?php if ($step4_done): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <i class="fas fa-truck"></i>
                            <?php endif; ?>
                        </div>
                        <span class="stepper-name">Delivery</span>
                        <span class="stepper-detail">Express Courier</span>
                    </div>
                </div>
            </div>

            <!-- 3-Column Highlights Grid -->
            <div class="success-info-grid">
                <!-- Card 1: Order Details -->
                <div class="success-info-card">
                    <div class="card-head">
                        <i class="far fa-calendar-alt"></i> Order Details
                    </div>
                    <strong><?php echo date('M d, Y', strtotime($order['ord_date'])); ?></strong>
                    <p style="font-size: 11.5px; color: #888;"><?php echo date('h:i A', strtotime($order['ord_date'])); ?></p>
                    <div class="mt-2">
                        <span class="badge <?php echo $status_badge_class; ?> px-2 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                            Status: <?php echo htmlspecialchars($order['ord_status']); ?>
                        </span>
                    </div>
                </div>

                <!-- Card 2: Payment Details -->
                <div class="success-info-card">
                    <div class="card-head">
                        <i class="fas fa-wallet"></i> Payment Details
                    </div>
                    <strong><?php echo htmlspecialchars($order['pay_method'] ?? 'Cash on Delivery'); ?></strong>
                    <p>
                        <?php if (strtolower($order['pay_method'] ?? '') === 'cash on delivery'): ?>
                            Payable in cash upon parcel handover
                        <?php else: ?>
                            Status: <?php echo htmlspecialchars($order['pay_status'] ?? 'Verified'); ?>
                        <?php endif; ?>
                    </p>
                    <div class="mt-2">
                        <span class="badge badge-light border px-2 py-1 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                            Payment: <?php echo htmlspecialchars($order['pay_status'] ?? 'Pending'); ?>
                        </span>
                    </div>
                </div>

                <!-- Card 3: Shipping Destination -->
                <div class="success-info-card">
                    <div class="card-head">
                        <i class="fas fa-map-marker-alt"></i> Delivery Destination
                    </div>
                    <?php if (!empty($shipping_info['name'])): ?>
                        <strong><?php echo htmlspecialchars($shipping_info['name']); ?></strong>
                        <p><?php echo htmlspecialchars($shipping_info['address']); ?></p>
                        <p><?php echo htmlspecialchars($shipping_info['city']); ?><?php echo !empty($shipping_info['postal']) ? ' - ' . htmlspecialchars($shipping_info['postal']) : ''; ?></p>
                        <small class="text-muted d-block mt-1"><i class="fas fa-phone-alt mr-1"></i><?php echo htmlspecialchars($shipping_info['phone']); ?></small>
                    <?php else: ?>
                        <pre style="font-family: inherit; font-size: 12px; margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($order['ship_address']); ?></pre>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Itemized Receipt Section -->
            <div class="success-receipt-section">
                <div class="success-receipt-heading">
                    <h3>Ordered Articles (<?php echo count($items); ?>)</h3>
                    <span>100% Guaranteed Original</span>
                </div>

                <div class="table-responsive">
                    <table class="success-table">
                        <thead>
                            <tr>
                                <th>Article &amp; Collection</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-right" style="width: 120px;">Unit Price</th>
                                <th class="text-right" style="width: 130px;">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): 
                                $line = (float)$item['item_price'] * (int)$item['item_quan'];
                                $img_filename = $item['pro_img'] ?? 'logo.png';
                                $img_path = '../admin/uploads/' . htmlspecialchars($img_filename);
                            ?>
                                <tr>
                                    <td>
                                        <div class="success-table-item">
                                            <img src="<?php echo $img_path; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['pro_name'] ?? ''); ?>" 
                                                 class="success-table-thumb"
                                                 onerror="this.src='assets/images/logo.png'">
                                            <div>
                                                <div class="success-table-item-name"><?php echo htmlspecialchars($item['pro_name'] ?? 'MiSHAÉ Atelier Article'); ?></div>
                                                <div class="success-table-item-cat"><?php echo htmlspecialchars($item['cat_name'] ?? 'Luxury Fabric'); ?></div>
                                                <?php if (!empty($item['item_option']) && $item['item_option'] !== 'Standard Piece'): ?>
                                                    <small class="text-muted d-block" style="font-size: 11px;">Option: <?php echo htmlspecialchars($item['item_option']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold" style="color: #292526;">
                                        &times; <?php echo (int)$item['item_quan']; ?>
                                    </td>
                                    <td class="text-right text-muted">
                                        PKR <?php echo number_format($item['item_price']); ?>
                                    </td>
                                    <td class="text-right font-weight-bold" style="color: #292526;">
                                        PKR <?php echo number_format($line); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pricing Summary Box -->
                <div class="success-pricing-box">
                    <div class="success-pricing-row">
                        <span>Items Subtotal:</span>
                        <span>PKR <?php echo number_format($items_subtotal); ?></span>
                    </div>
                    <div class="success-pricing-row">
                        <span>Courier &amp; Packaging:</span>
                        <span>
                            <?php if ($shipping_fee > 0): ?>
                                PKR <?php echo number_format($shipping_fee); ?>
                            <?php else: ?>
                                <span class="text-success font-weight-bold">COMPLIMENTARY</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="success-pricing-row total-row">
                        <span>Grand Total Payable:</span>
                        <span class="total-amount">PKR <?php echo number_format($order['ord_total']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Atelier Guarantees Strip -->
            <div class="success-guarantees-strip">
                <div class="success-guarantee-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>100% Original Brand Guarantee</span>
                </div>
                <div class="success-guarantee-item">
                    <i class="fas fa-gem"></i>
                    <span>Bespoke Quality Inspected</span>
                </div>
                <div class="success-guarantee-item">
                    <i class="fas fa-truck-fast"></i>
                    <span>Express Dispatch Across Pakistan</span>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="success-actions-bar">
                <a href="https://wa.me/923001234567?text=<?php echo urlencode($wa_message); ?>" 
                   target="_blank" 
                   class="btn-success-whatsapp">
                    <i class="fab fa-whatsapp" style="font-size: 16px;"></i> WhatsApp Concierge
                </a>

                <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                    <button type="button" onclick="window.print()" class="btn-success-print">
                        <i class="fas fa-print"></i> Print Invoice
                    </button>

                    <?php if ($is_logged_in): ?>
                        <a href="account.php" class="btn-success-orders">
                            <i class="far fa-user"></i> View My Orders
                        </a>
                    <?php endif; ?>

                    <a href="products.php" class="btn-success-continue">
                        Continue Shopping &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/include/footer.php'; ?>

<!-- Scripts -->
<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function copyOrderRef(text, btn) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
            setTimeout(function() { btn.innerHTML = orig; }, 2000);
        });
    }
}
</script>

</body>
</html>
