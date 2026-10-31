<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['flash_message'] = [
        'type' => 'warning',
        'text' => 'Please log in to your account or register to view your saved wishlist.'
    ];
    $_SESSION['redirect_after_login'] = 'wishlist.php';
    header('Location: login.php');
    exit();
}

$wishlist_ids = array_keys($_SESSION['wishlist'] ?? []);
$products = [];

if (!empty($wishlist_ids)) {
    $ids_str = implode(',', array_map('intval', $wishlist_ids));
    $query = "SELECT product.*, category.cat_name 
              FROM product 
              LEFT JOIN category ON product.cat_id = category.cat_id 
              WHERE product.pro_id IN ($ids_str)";
    $res = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($res)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Wishlist | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/upro.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body style="background: #FAF8F5;">

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<main class="container py-4 py-md-5" style="min-height: 75vh;">
    <!-- Flash Notifications -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mishae-alert mishae-alert-<?php echo $_SESSION['flash_message']['type']; ?>">
            <span><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['flash_message']['text']); ?></span>
            <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close Alert">&times;</button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="page-intro-header mb-4">
        <div class="container text-center">
            <h1 class="page-intro-title">Saved Wishlist</h1>
            <p class="page-intro-desc">Curate your private selection of signature fabrics for upcoming occasions.</p>
        </div>
    </div>

    <?php if (!empty($products)): ?>
        <div class="wishlist-wrapper">
            <div class="wishlist-toolbar">
                <div class="wishlist-toolbar-count">
                    <span>Saved Pieces (<?php echo count($products); ?>)</span>
                </div>
                <div class="wishlist-toolbar-link">
                    <a href="products.php" class="wishlist-continue-link">
                        Continue Exploring <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="wishlist-grid">
                <?php foreach ($products as $pro): ?>
                    <div class="wishlist-card">
                        <!-- Media Section -->
                        <div class="wishlist-card-media">
                            <a href="product-detail.php?id=<?php echo (int)$pro['pro_id']; ?>" class="wishlist-card-img-link" title="<?php echo htmlspecialchars($pro['pro_name']); ?>">
                                <img src="../admin/uploads/<?php echo htmlspecialchars($pro['pro_img']); ?>" 
                                     alt="<?php echo htmlspecialchars($pro['pro_name']); ?>" 
                                     loading="lazy">
                            </a>

                            <!-- Category Pill -->
                            <?php if (!empty($pro['cat_name'])): ?>
                                <span class="wishlist-cat-pill"><?php echo htmlspecialchars($pro['cat_name']); ?></span>
                            <?php endif; ?>

                            <!-- Floating Remove Cross Button -->
                            <button type="button" 
                                    class="wishlist-remove-btn" 
                                    title="Remove from Wishlist" 
                                    aria-label="Remove from Wishlist"
                                    onclick="confirmWishlistRemove('cart-action.php?action=wishlist_toggle&pro_id=<?php echo (int)$pro['pro_id']; ?>', '<?php echo htmlspecialchars($pro['pro_name'], ENT_QUOTES); ?>');">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Content Section -->
                        <div class="wishlist-card-content">
                            <div class="wishlist-card-header">
                                <div class="wishlist-stock-status">
                                    <?php if ((int)$pro['pro_quan'] > 0): ?>
                                        <span class="stock-badge in-stock"><i class="fas fa-circle"></i> In Stock</span>
                                    <?php else: ?>
                                        <span class="stock-badge out-stock"><i class="fas fa-circle"></i> Sold Out</span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="wishlist-card-title">
                                    <a href="product-detail.php?id=<?php echo (int)$pro['pro_id']; ?>">
                                        <?php echo htmlspecialchars($pro['pro_name']); ?>
                                    </a>
                                </h3>

                                <div class="wishlist-card-price">
                                    <span class="price-curr">PKR</span>
                                    <span class="price-val"><?php echo number_format((float)$pro['pro_price']); ?></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="wishlist-card-footer">
                                <?php if ((int)$pro['pro_quan'] > 0): ?>
                                    <form action="cart-action.php" method="POST" class="w-100 m-0">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="pro_id" value="<?php echo (int)$pro['pro_id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-wishlist-move">
                                            <i class="fas fa-shopping-bag mr-2"></i> Move to Bag
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="btn-wishlist-move disabled" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>

                                <div class="wishlist-card-quick-links">
                                    <a href="product-detail.php?id=<?php echo (int)$pro['pro_id']; ?>" class="wishlist-view-link">
                                        View Details &rarr;
                                    </a>
                                    <button type="button" 
                                            class="wishlist-inline-remove"
                                            onclick="confirmWishlistRemove('cart-action.php?action=wishlist_toggle&pro_id=<?php echo (int)$pro['pro_id']; ?>', '<?php echo htmlspecialchars($pro['pro_name'], ENT_QUOTES); ?>');">
                                        <i class="far fa-trash-alt mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="wishlist-empty-card">
            <div class="wishlist-empty-icon-wrap">
                <i class="far fa-heart"></i>
            </div>
            <span class="wishlist-empty-eyebrow">Your Private Selection</span>
            <h2 class="wishlist-empty-title">Your Wishlist is Currently Empty</h2>
            <p class="wishlist-empty-desc">
                You haven't saved any luxury fabric designs to your wishlist yet. Discover our signature lawn, festive chiffon, and raw silk collections.
            </p>
            <a href="products.php" class="btn-wishlist-explore">
                Explore Collections <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    <?php endif; ?>
</main>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
