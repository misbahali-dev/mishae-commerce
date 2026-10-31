<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// =========================================
// 1. DATABASE CONNECTION
// =========================================
require_once __DIR__ . '/../admin/config/config.php';

// =========================================
// 2. FETCH ACTIVE CATEGORIES
// =========================================
$category_query = "SELECT * FROM category WHERE cat_status = 1 ORDER BY cat_id ASC";
$category_result = mysqli_query($conn, $category_query);

// =========================================
// 3. FETCH TRENDING PRODUCTS (Top Signature Creations)
// =========================================
$trending_query = "SELECT product.*, category.cat_name
                   FROM product
                   LEFT JOIN category ON product.cat_id = category.cat_id
                   WHERE product.pro_quan > 0
                   ORDER BY product.pro_price DESC
                   LIMIT 8";
$trending_result = mysqli_query($conn, $trending_query);

// =========================================
// 4. FETCH NEW ARRIVALS (Latest 8 additions)
// =========================================
$arrival_query = "SELECT product.*, category.cat_name
                  FROM product
                  LEFT JOIN category ON product.cat_id = category.cat_id
                  WHERE product.pro_quan > 0
                  ORDER BY product.pro_id DESC
                  LIMIT 8";
$arrival_result = mysqli_query($conn, $arrival_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Luxury Fabrics &amp; Haute Couture</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

    <!-- MiSHAÉ Stylesheets (cache-busted) -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=4.2">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body>

<!-- Announcement Bar -->
<?php include './include/topbar.php'; ?>

<!-- Navbar -->
<?php include './include/navbar.php'; ?>

<!-- Flash Message -->
<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mishae-alert mishae-alert-<?php echo $_SESSION['flash_message']['type']; ?>" id="homeFlashAlert">
        <span><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['flash_message']['text']); ?></span>
        <button type="button" class="close" onclick="dismissMishaeAlert(this);" aria-label="Close Alert">&times;</button>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<!-- =========================================
     HERO SLIDER
========================================= -->
<div id="mishaeHero" class="carousel slide mishae-hero" data-ride="carousel" data-interval="3500">
    <ol class="carousel-indicators">
        <li data-target="#mishaeHero" data-slide-to="0" class="active"></li>
        <li data-target="#mishaeHero" data-slide-to="1"></li>
        <li data-target="#mishaeHero" data-slide-to="2"></li>
    </ol>

    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/img/hero_1.jpg" alt="Women's collection" loading="eager" fetchpriority="high">
            <div class="mishae-hero-overlay">
                <div class="mishae-hero-text">
                    <p>THE WOMEN'S COLLECTION</p>
                    <h1>Elegance in Every Thread</h1>
                    <p>Timeless designs. Premium fabrics. Made for you.</p>
                    <a href="products.php" class="explore-btn">
                        Explore Collection <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="assets/img/hero_2.jpg" alt="Men's collection" loading="lazy">
            <div class="mishae-hero-overlay">
                <div class="mishae-hero-text">
                    <p>THE MEN'S COLLECTION</p>
                    <h1>Style That Speaks</h1>
                    <p>Classic looks. Quality fabrics. Everyday confidence.</p>
                    <a href="products.php" class="explore-btn">
                        Explore Collection <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="carousel-item">
            <img src="assets/img/hero_3.jpg" alt="Kids' collection" loading="lazy">
            <div class="mishae-hero-overlay">
                <div class="mishae-hero-text">
                    <p>THE KIDS' COLLECTION</p>
                    <h1>Little Looks, Lovely Moments</h1>
                    <p>Comfortable styles for little personalities.</p>
                    <a href="products.php" class="explore-btn">
                        Explore Collection <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <a class="carousel-control-prev" href="#mishaeHero" role="button" data-slide="prev" aria-label="Previous">
        <span class="carousel-control-prev-icon"></span>
    </a>
    <a class="carousel-control-next" href="#mishaeHero" role="button" data-slide="next" aria-label="Next">
        <span class="carousel-control-next-icon"></span>
    </a>
</div>

<!-- =========================================
     CATEGORIES SECTION
========================================= -->
<section class="mishae-section mishae-categories" id="categories">
    <div class="mishae-section-heading">
        <p class="section-subtitle">EXPLORE THE ATELIER</p>
        <h2>Shop by Collection</h2>
        <p>Distinguished fabrics tailored for women, men, and children.</p>
    </div>

    <div class="mishae-category-grid">
        <?php
        mysqli_data_seek($category_result, 0);
        while ($category = mysqli_fetch_assoc($category_result)) {
            $cat_id = (int)$category['cat_id'];
            $category_link = "products.php?cat_id=" . $cat_id;
            $category_image = "../admin/uploads/" . $category['cat_img'];
        ?>
            <a href="<?php echo htmlspecialchars($category_link); ?>" class="mishae-category-card">
                <img src="<?php echo htmlspecialchars($category_image); ?>" alt="<?php echo htmlspecialchars($category['cat_name']); ?>">
                <div class="mishae-category-overlay">
                    <h3><?php echo htmlspecialchars($category['cat_name']); ?></h3>
                    <span>Explore Collection <i class="fas fa-arrow-right ml-1"></i></span>
                </div>
            </a>
        <?php } ?>
    </div>
</section>

<!-- =========================================
     SEASONAL BANNER SECTION   
========================================= -->
<section class="mishae-season-section">
    <div class="mishae-section-heading">
        <p class="section-subtitle">SEASONAL EDIT</p>
        <h2>Summer <span class="mishae-amp">&amp;</span> Winter Editions</h2>
        <p>Curated fabrics crafted for warm weather lightness and cool festive warmth.</p>
    </div>

    <div class="mishae-season-grid">
        <a href="products.php?season=Summer" class="mishae-season-card spring-card">
            <div class="mishae-season-content">
                <p>WARM WEATHER EDIT</p>
                <h3>Summer Collection</h3>
                <span class="mishae-shop-btn">
                    Explore Summer <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </a>

        <a href="products.php?season=Winter" class="mishae-season-card winter-card">
            <div class="mishae-season-content">
                <p>COOL COUTURE EDIT</p>
                <h3>Winter Collection</h3>
                <span class="mishae-shop-btn">
                    Explore Winter <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </a>
    </div>
</section>

<!-- =========================================
     TRENDING PRODUCTS
========================================= -->
<section class="mishae-section mishae-products-section">
    <div class="mishae-section-heading">
        <p class="section-subtitle">SIGNATURE CREATIONS</p>
        <h2>Trending Now</h2>
        <p>Most admired fabric pieces from our couture studio.</p>
    </div>

    <div class="mishae-product-grid">
        <?php while ($product = mysqli_fetch_assoc($trending_result)) { 
            $p_id = (int)$product['pro_id'];
            $is_fav = isset($_SESSION['wishlist'][$p_id]);
        ?>
            <div class="mishae-product-card">
                <div style="position: relative;">
                    <a href="product-detail.php?id=<?php echo $p_id; ?>" class="mishae-product-image">
                        <img src="<?php echo htmlspecialchars("../admin/uploads/" . $product['pro_img']); ?>"
                             alt="<?php echo htmlspecialchars($product['pro_name']); ?>">
                        <span class="mishae-product-tag">Trending</span>
                    </a>
                    <a href="cart-action.php?action=wishlist_toggle&pro_id=<?php echo $p_id; ?>" 
                       style="position: absolute; top: 12px; right: 12px; width: 30px; height: 30px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: <?php echo $is_fav ? '#C62828' : '#222'; ?>; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12);"
                       title="Save to Wishlist">
                        <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart" style="font-size: 13px;"></i>
                    </a>
                </div>

                <div class="mishae-product-info">
                    <p class="mishae-product-category">
                        <?php echo htmlspecialchars($product['cat_name'] ?? 'Luxury Collection'); ?>
                    </p>
                    <h3>
                        <a href="product-detail.php?id=<?php echo $p_id; ?>" style="color: inherit; text-decoration: none;">
                            <?php echo htmlspecialchars($product['pro_name']); ?>
                        </a>
                    </h3>
                    <p class="mishae-product-price">
                        PKR <?php echo number_format((float)$product['pro_price']); ?>
                    </p>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top mishae-product-card-footer">
                        <a href="product-detail.php?id=<?php echo $p_id; ?>" class="mishae-view-product">
                            Buy Now &rarr;
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
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="mishae-section-button">
        <a href="products.php" class="mishae-outline-btn">
            View All Fabrics &rarr;
        </a>
    </div>
</section>

<!-- =========================================
     FABRIC MASTERY STORYTELLING
========================================= -->
<section class="mishae-weaving-story">
    <div class="row align-items-center weaving-inner-row">
        <!-- On Mobile: order-2 (Narrative follows visual). On Desktop: order-lg-1 (Left Column) -->
        <div class="col-lg-6 order-2 order-lg-1">
            <p class="weaving-eyebrow">THE ART OF LUXURY WEAVING</p>
            <h2 class="weaving-title">
                Where Master Weaving Meets Modern Pakistani Haute Couture
            </h2>
            <p class="weaving-desc">
                At MiSHAÉ, every fabric begins with handpicked raw fibers. From high-thread-count Egyptian combed cotton and organic mulmul to artisanal raw silk and flowing chiffon, each yard undergoes meticulous quality grading before being transformed by our master embroidery artisans.
            </p>
            <div class="weaving-stats-grid">
                <div class="weaving-stat-card">
                    <div class="weaving-stat-number">80/80</div>
                    <span class="weaving-stat-label">Pure Combed Lawn</span>
                </div>
                <div class="weaving-stat-card">
                    <div class="weaving-stat-number">100%</div>
                    <span class="weaving-stat-label">Colorfast Guaranteed</span>
                </div>
            </div>
            <a href="about.php" class="story-primary-btn" style="display:inline-block;">
                Discover Our Heritage &rarr;
            </a>
        </div>

        <!-- On Mobile: order-1 (Visual Header). On Desktop: order-lg-2 (Right Column) -->
        <div class="col-lg-6 order-1 order-lg-2 text-center mb-4 mb-lg-0">
            <div class="weaving-image-wrapper">
                <img src="assets/img/about.jpg" alt="Fabric artistry & luxury weaving" loading="lazy">
                <span class="weaving-image-badge">
                    <i class="fas fa-certificate"></i> Master Artisan Weave
                </span>
            </div>
        </div>
    </div>
</section>

<!--=========================================
     JUST ARRIVED
=========================================-->
<section class="mishae-section mishae-arrivals-section">
    <div class="mishae-section-heading">
        <p class="section-subtitle">FRESH FROM THE LOOMS</p>
        <h2>Just Arrived</h2>
        <p>Newest additions to our seasonal capsule collections.</p>
    </div>

    <div class="mishae-product-grid">
        <?php while ($product = mysqli_fetch_assoc($arrival_result)) { 
            $p_id = (int)$product['pro_id'];
            $is_fav = isset($_SESSION['wishlist'][$p_id]);
        ?>
            <div class="mishae-product-card">
                <div style="position: relative;">
                    <a href="product-detail.php?id=<?php echo $p_id; ?>" class="mishae-product-image">
                        <img src="<?php echo htmlspecialchars("../admin/uploads/" . $product['pro_img']); ?>"
                             alt="<?php echo htmlspecialchars($product['pro_name']); ?>">
                        <span class="mishae-product-tag" style="background: #2E7D32;">New</span>
                    </a>
                    <a href="cart-action.php?action=wishlist_toggle&pro_id=<?php echo $p_id; ?>" 
                       style="position: absolute; top: 12px; right: 12px; width: 30px; height: 30px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: <?php echo $is_fav ? '#C62828' : '#222'; ?>; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12);"
                       title="Save to Wishlist">
                        <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart" style="font-size: 13px;"></i>
                    </a>
                </div>

                <div class="mishae-product-info">
                    <p class="mishae-product-category">
                        <?php echo htmlspecialchars($product['cat_name'] ?? 'Collection'); ?>
                    </p>
                    <h3>
                        <a href="product-detail.php?id=<?php echo $p_id; ?>" style="color: inherit; text-decoration: none;">
                            <?php echo htmlspecialchars($product['pro_name']); ?>
                        </a>
                    </h3>
                    <p class="mishae-product-price">
                        PKR <?php echo number_format((float)$product['pro_price']); ?>
                    </p>
                    <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top mishae-product-card-footer">
                        <a href="product-detail.php?id=<?php echo $p_id; ?>" class="mishae-view-product">
                            Buy Now &rarr;
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
                </div>
            </div>
        <?php } ?>
    </div>
</section>



<!-- Footer -->
<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('#mishaeHero').carousel({
        interval: 3500,
        ride: 'carousel',
        pause: 'hover'
    });
});
</script>

</body>
</html>
