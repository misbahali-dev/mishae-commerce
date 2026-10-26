<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

// Dynamically count available products
$boys_count = 0;
$girls_count = 0;

$boys_q = mysqli_query($conn, "SELECT COUNT(*) FROM product WHERE cat_id = 16 AND (pro_name LIKE '%boy%' OR pro_des LIKE '%boy%') AND pro_quan > 0");
if ($boys_q) {
    $boys_count = (int)mysqli_fetch_row($boys_q)[0];
}

$girls_q = mysqli_query($conn, "SELECT COUNT(*) FROM product WHERE cat_id = 16 AND (pro_name LIKE '%girl%' OR pro_des LIKE '%girl%') AND pro_quan > 0");
if ($girls_q) {
    $girls_count = (int)mysqli_fetch_row($girls_q)[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kids’ Collection | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/ucat.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">

    <style>
        .kids-collection-section {
            padding: 42px 20px 70px;
            background: #FAF8F5;
        }

        .kids-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 26px;
            max-width: 860px;
            margin: 0 auto;
        }

        .kids-card {
            background: #FFFFFF;
            border: 1px solid #EAE5DF;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(14, 14, 16, 0.06);
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            text-decoration: none !important;
            color: inherit;
            cursor: pointer;
        }

        .kids-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 32px rgba(14, 14, 16, 0.1);
            border-color: #C5A880;
        }

        .kids-image-wrapper {
            position: relative;
            width: 100%;
            height: 360px;
            max-height: 360px;
            overflow: hidden;
            background: #F4EFEA;
        }

        .kids-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
            transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .kids-card:hover .kids-image-wrapper img {
            transform: scale(1.04);
        }

        .kids-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(14, 14, 16, 0.85) 0%,
                rgba(14, 14, 16, 0.38) 50%,
                rgba(14, 14, 16, 0.05) 100%
            );
            pointer-events: none;
        }

        .kids-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 22px 24px;
            color: #FFFFFF;
            z-index: 2;
        }

        .kids-badge {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #E8DDD0;
            background: rgba(197, 168, 128, 0.25);
            border: 1px solid rgba(197, 168, 128, 0.45);
            backdrop-filter: blur(4px);
            padding: 3px 10px;
            border-radius: 16px;
            margin-bottom: 8px;
        }

        .kids-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            font-weight: 500;
            color: #FFFFFF;
            margin: 0 0 6px;
            line-height: 1.25;
            letter-spacing: 0.3px;
        }

        .kids-desc {
            font-size: 12.5px;
            line-height: 1.5;
            color: #E8DDD0;
            margin-bottom: 14px;
            max-width: 95%;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .kids-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 20px;
            background: #FAF8F5;
            color: #292526 !important;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            border-radius: 4px;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .kids-card:hover .kids-btn {
            background: #a78543;
            color: #FFFFFF !important;
            transform: translateX(3px);
        }

        @media (max-width: 767.98px) {
            .kids-collection-section {
                padding: 30px 16px 50px;
            }

            .kids-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                max-width: 440px;
            }

            .kids-image-wrapper {
                height: 320px;
                max-height: 320px;
            }

            .kids-card-content {
                padding: 18px 18px;
            }

            .kids-title {
                font-size: 21px;
            }

            .kids-desc {
                font-size: 12px;
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>

<?php include "./include/topbar.php"; ?>
<?php include "./include/navbar.php"; ?>

<!-- Clean Page Intro Header -->
<section class="page-intro-header">
    <div class="container text-center">
        <span class="story-eyebrow">Little Heirlooms &amp; Timeless Craft</span>
        <h1 class="page-intro-title">Kids’ Collection</h1>
        <p class="page-intro-desc">
            Thoughtfully tailored traditional wear, festive kurtas, and handcrafted ensembles created for little boys and girls with pure eastern comfort.
        </p>
    </div>
</section>

<!-- Kids Collection Cards Section -->
<section class="kids-collection-section">
    <div class="kids-grid">
        <!-- Boys' Collection Card -->
        <a href="products.php?cat_id=16&gender=boys" class="kids-card">
            <div class="kids-image-wrapper">
                <img src="./../admin/uploads/Smiling_Boy_in_Traditional_White_Attire.png"
                     alt="Boys’ Collection - Traditional Kurtas &amp; Shalwar Suits"
                     loading="lazy">
                <div class="kids-card-overlay"></div>
                <div class="kids-card-content">
                    <span class="kids-badge"><?php echo $boys_count; ?> Designs Available</span>
                    <h2 class="kids-title">Boys’ Collection</h2>
                    <p class="kids-desc">
                        Classic band-collar kurtas, crisp cotton shalwars, and royal stitched suits designed for family festivities and Friday prayers.
                    </p>
                    <span class="kids-btn">
                        Explore Boys’ Collection <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </a>

        <!-- Girls' Collection Card -->
        <a href="products.php?cat_id=16&gender=girls" class="kids-card">
            <div class="kids-image-wrapper">
                <img src="./../admin/uploads/mishae_luxury_editorial_girl_1792821980771.jpg"
                     alt="Girls’ Collection - Festive Frocks &amp; Traditional Ensembles"
                     loading="lazy">
                <div class="kids-card-overlay"></div>
                <div class="kids-card-content">
                    <span class="kids-badge"><?php echo $girls_count; ?> Designs Available</span>
                    <h2 class="kids-title">Girls’ Collection</h2>
                    <p class="kids-desc">
                        Handcrafted celebratory frocks, delicate zarri embroideries, and flowing silk ensembles designed for celebrations and weddings.
                    </p>
                    <span class="kids-btn">
                        Explore Girls’ Collection <i class="fas fa-arrow-right"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>

    <!-- Secondary Navigation Callout -->
    <div class="text-center mt-5 pt-4">
        <a href="products.php?cat_id=16" class="story-primary-btn mr-md-3 mb-2 mb-md-0">
            View All Kids’ Products (<?php echo ($boys_count + $girls_count); ?>) &rarr;
        </a>
        <a href="ucat.php" class="btn btn-outline-dark" style="padding: 12px 28px; font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; border-radius: 4px;">
            All Collections
        </a>
    </div>
</section>

<?php include "./include/footer.php"; ?>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
