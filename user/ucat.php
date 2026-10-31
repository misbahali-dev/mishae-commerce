<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

$cat_query = "SELECT * FROM category WHERE cat_status = 1 ORDER BY cat_id ASC";
$cat_result = mysqli_query($conn, $cat_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collections &amp; Categories | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/ucat.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/about.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body>

<?php include "./include/topbar.php"; ?>
<?php include "./include/navbar.php"; ?>

<!-- Single Clean Page Header -->
<section class="page-intro-header">
    <div class="container text-center">
        <h1 class="page-intro-title">Collections</h1>
        <p class="page-intro-desc">Explore thoughtfully curated unstitched fabric collections for women, men, and children.</p>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="categories-grid">
        <?php if ($cat_result && mysqli_num_rows($cat_result) > 0) { ?>
            <?php while ($category = mysqli_fetch_assoc($cat_result)) { 
                $cat_link = ((int)$category['cat_id'] === 16 || strtolower(trim($category['cat_name'])) === 'kids')
                    ? 'kids.php'
                    : ('products.php?cat_id=' . (int)$category['cat_id']);
            ?>
                <a href="<?php echo $cat_link; ?>" class="category-card">
                    <div class="category-image">
                        <img src="./../admin/uploads/<?php echo htmlspecialchars($category['cat_img']); ?>"
                             alt="<?php echo htmlspecialchars($category['cat_name']); ?>">
                        <div class="category-overlay"></div>
                        <div class="category-info">
                            <h3><?php echo htmlspecialchars($category['cat_name']); ?></h3>
                            <p><?php echo htmlspecialchars($category['cat_des']); ?></p>
                            <span class="category-btn">
                                Explore Collection <span>&rarr;</span>
                            </span>
                        </div>
                    </div>
                </a>
            <?php } ?>
        <?php } else { ?>
            <div class="categories-empty">
                <h3>Collections In Preparation</h3>
                <p>Our upcoming seasonal fabrics will be unveiled here soon.</p>
            </div>
        <?php } ?>
    </div>
</section>

<!-- Bottom CTA -->
<div class="text-center py-5 border-top" style="background: #FAF8F5;">
    <a href="products.php" class="story-primary-btn">
        View All Fabrics &rarr;
    </a>
</div>

<?php include "./include/footer.php"; ?>

<!-- Scripts -->
<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>