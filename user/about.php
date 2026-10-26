<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Story | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="./assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="./assets/css/about.css?v=3.7">
    <link rel="stylesheet" href="./assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="./assets/css/store.css?v=3.7">
</head>
<body class="about-page-body">

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<main class="story-main">

    <!-- 1. Page Header (Single Clean Heading) -->
    <section class="page-intro-header">
        <div class="container text-center">
            <h1 class="page-intro-title">Our Story</h1>
            <p class="page-intro-desc">Timeless elegance, thoughtful craftsmanship, and genuine comfort in Pakistani fabrics.</p>
        </div>
    </section>

    <!-- 2. Our Story Section -->
    <section class="story-narrative-section">
        <div class="story-container">
            <div class="story-narrative-grid">
                <div class="story-narrative-content">
                    <p class="lead" style="font-size: 16px; font-weight: 500; color: #292526; line-height: 1.8;">
                        MiSHAÉ was created from an appreciation for Pakistan’s rich textile heritage and a commitment to making quality clothing accessible, wearable, and lasting. We believe that what you wear is an expression of grace and personal comfort.
                    </p>
                    <p>
                        Our collections celebrate clean silhouettes, tasteful palettes, and dependable fabrics. Whether you are selecting breathable lawn for sunny days, refined raw silk for family celebrations, or classic ensembles for men and children, our focus remains on delivering garments that feel as good as they look.
                    </p>
                    <p>
                        Every piece in our store is thoughtfully curated to ensure that traditional charm blends seamlessly with contemporary style, giving you versatile options for every occasion.
                    </p>
                </div>
                <div class="story-narrative-media">
                    <div class="story-image-frame">
                        <img src="./assets/img/about2.jpg" alt="MiSHAÉ textile and clothing collection" class="story-content-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Our Vision Section -->
    <section class="story-vision-section">
        <div class="story-container">
            <div class="story-vision-card">
                <span class="story-section-tag">OUR DIRECTION</span>
                <h2>Our Vision</h2>
                <div class="story-divider story-divider-center"></div>
                <p class="story-vision-lead">
                    To be a trusted name in Pakistani fashion by offering timeless designs and dependable fabric quality that bring elegance, poise, and confidence to everyday moments and celebrations.
                </p>
                <div class="story-vision-grid">
                    <div class="story-vision-point">
                        <div class="vision-point-icon"><i class="fas fa-feather-alt"></i></div>
                        <h3>Timeless Design</h3>
                        <p>Creating styles that outlast fleeting trends, ensuring your wardrobe stays graceful season after season.</p>
                    </div>
                    <div class="story-vision-point">
                        <div class="vision-point-icon"><i class="fas fa-gem"></i></div>
                        <h3>Accessible Elegance</h3>
                        <p>Providing well-crafted clothing and unstitched fabrics suited for everyday wear as well as festive celebrations.</p>
                    </div>
                    <div class="story-vision-point">
                        <div class="vision-point-icon"><i class="fas fa-users"></i></div>
                        <h3>Fashion for Everyone</h3>
                        <p>Offering carefully considered collections for women, men, and children so every family member finds pieces they cherish.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Our Commitment Section -->
    <section class="story-commitment-section">
        <div class="story-container">
            <div class="story-commitment-header">
                <span class="story-section-tag">WHAT WE STAND FOR</span>
                <h2>Our Commitment</h2>
                <div class="story-divider story-divider-center"></div>
                <p>
                    Our promise is straightforward: we put fabric integrity, genuine craftsmanship, and your satisfaction at the heart of everything we offer.
                </p>
            </div>

            <div class="story-commitment-grid">
                <div class="commitment-card">
                    <div class="commitment-card-number">01</div>
                    <h3>Fabric Quality</h3>
                    <p>
                        We select fine, breathable fibers and soft cotton blends to ensure long-wearing comfort, rich textures, and reliable color retention.
                    </p>
                </div>

                <div class="commitment-card">
                    <div class="commitment-card-number">02</div>
                    <h3>Thoughtful Craftsmanship</h3>
                    <p>
                        From neat stitchwork and clean hems to balanced prints and embroidery, each piece is checked to meet our quality standards.
                    </p>
                </div>

                <div class="commitment-card">
                    <div class="commitment-card-number">03</div>
                    <h3>Customer Satisfaction</h3>
                    <p>
                        Your trust matters most. We are committed to honest service, dependable delivery, and responsive support for every order.
                    </p>
                </div>
            </div>

            <!-- Call to Action Box -->
            <div class="story-cta-box">
                <h3>Experience MiSHAÉ</h3>
                <p>Explore our latest arrivals across unstitched fabrics, festive wear, and everyday essentials.</p>
                <a href="products.php" class="story-primary-btn">
                    Explore Collections <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

</main>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
