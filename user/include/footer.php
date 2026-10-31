<?php
$current_script = basename($_SERVER['PHP_SELF'] ?? '');
$browsing_pages = ['home.php', 'index.php', 'products.php', 'kids.php', 'ucat.php', 'product-detail.php'];

// Allow page-specific override if explicitly defined via $show_benefits_section
if (!isset($show_benefits_section)) {
    $show_benefits_section = in_array($current_script, $browsing_pages);
}
?>

<?php if ($show_benefits_section): ?>
<!-- =========================================
     DISTINCT BENEFITS SECTION (Separate from Footer)
========================================= -->
<section class="mishae-benefits-section" style="background: #FAF8F5; border-top: 1px solid #EAE5DF; border-bottom: 1px solid #EAE5DF; padding: 40px 5%;">
    <div style="max-width: 1260px; margin: 0 auto;">
        <div class="row g-3 text-center align-items-center">
            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                <div style="background: #FFFFFF; border: 1px solid #EAE5DF; border-radius: 8px; padding: 20px 16px; box-shadow: 0 4px 15px rgba(14, 14, 16, 0.03); display: flex; align-items: center; gap: 14px; text-align: left; height: 100%;">
                    <div style="width: 44px; height: 44px; min-width: 44px; background: #FAF8F5; border: 1px solid #EAE5DF; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #C5A880; font-size: 18px;">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div>
                        <h6 style="color: #292526; font-size: 13px; font-weight: 600; margin: 0 0 3px; letter-spacing: 0.5px;">100% Pure Fabric</h6>
                        <p style="font-size: 11.5px; color: #6B645E; margin: 0; line-height: 1.35;">Finest combed lawn, silk, and cotton</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                <div style="background: #FFFFFF; border: 1px solid #EAE5DF; border-radius: 8px; padding: 20px 16px; box-shadow: 0 4px 15px rgba(14, 14, 16, 0.03); display: flex; align-items: center; gap: 14px; text-align: left; height: 100%;">
                    <div style="width: 44px; height: 44px; min-width: 44px; background: #FAF8F5; border: 1px solid #EAE5DF; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #C5A880; font-size: 18px;">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div>
                        <h6 style="color: #292526; font-size: 13px; font-weight: 600; margin: 0 0 3px; letter-spacing: 0.5px;">Express Delivery</h6>
                        <p style="font-size: 11.5px; color: #6B645E; margin: 0; line-height: 1.35;">2 to 4 business days nationwide</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                <div style="background: #FFFFFF; border: 1px solid #EAE5DF; border-radius: 8px; padding: 20px 16px; box-shadow: 0 4px 15px rgba(14, 14, 16, 0.03); display: flex; align-items: center; gap: 14px; text-align: left; height: 100%;">
                    <div style="width: 44px; height: 44px; min-width: 44px; background: #FAF8F5; border: 1px solid #EAE5DF; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #C5A880; font-size: 18px;">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h6 style="color: #292526; font-size: 13px; font-weight: 600; margin: 0 0 3px; letter-spacing: 0.5px;">Cash on Delivery</h6>
                        <p style="font-size: 11.5px; color: #6B645E; margin: 0; line-height: 1.35;">Doorstep payment across Pakistan</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6 mb-3 mb-lg-0">
                <div style="background: #FFFFFF; border: 1px solid #EAE5DF; border-radius: 8px; padding: 20px 16px; box-shadow: 0 4px 15px rgba(14, 14, 16, 0.03); display: flex; align-items: center; gap: 14px; text-align: left; height: 100%;">
                    <div style="width: 44px; height: 44px; min-width: 44px; background: #FAF8F5; border: 1px solid #EAE5DF; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #C5A880; font-size: 18px;">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                    <div>
                        <h6 style="color: #292526; font-size: 13px; font-weight: 600; margin: 0 0 3px; letter-spacing: 0.5px;">14 Days Easy Returns</h6>
                        <p style="font-size: 11.5px; color: #6B645E; margin: 0; line-height: 1.35;">Hassle-free inspection &amp; exchange</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Distinct Visible Separation Strip Below Benefits and Above Footer -->
<div class="benefits-footer-gap" style="height: 24px; background: #FAF8F5;"></div>
<?php endif; ?>

<!-- =========================================
     BLACK LUXURY FOOTER
========================================= -->
<footer class="mishae-footer" style="background: #0E0E10; color: #E8DDD0; padding: 60px 5% 30px;">
    <div class="mishae-footer-main" style="display: grid; grid-template-columns: 1.6fr 1fr 1fr 1.4fr; gap: 40px; margin-bottom: 50px;">
        <div class="mishae-footer-brand">
            <a href="home.php" class="mishae-footer-logo" style="color: #fff; font-family: Georgia, serif; font-size: 28px; text-decoration: none; display: inline-block; margin-bottom: 15px;">
                MiSHAÉ
                <span style="display:block; font-size: 9px; letter-spacing: 4px; font-family: sans-serif; text-transform: uppercase; color: #C5A880;">Luxury Fabrics</span>
            </a>
            <p style="font-size: 13px; line-height: 1.8; color: #A69E96; max-width: 320px;">
                Crafting timeless heirlooms with pure yarns, master artisan threadwork, and modern couture silhouettes for discerning men, women, and kids.
            </p>
            <div class="mishae-social" style="display: flex; gap: 14px; margin-top: 20px;">
                <a href="#" style="width: 34px; height: 34px; border: 1px solid #3A3532; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #E8DDD0; text-decoration: none; transition: all 0.3s;" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" style="width: 34px; height: 34px; border: 1px solid #3A3532; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #E8DDD0; text-decoration: none; transition: all 0.3s;" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" style="width: 34px; height: 34px; border: 1px solid #3A3532; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #E8DDD0; text-decoration: none; transition: all 0.3s;" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                <a href="https://wa.me/923001234567" target="_blank" style="width: 34px; height: 34px; border: 1px solid #25D366; background: rgba(37,211,102,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #25D366; text-decoration: none;" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <div class="mishae-footer-links">
            <h5 style="color: #fff; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">The Collections</h5>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                <a href="products.php" style="color: #A69E96; text-decoration: none;">All Fabrics</a>
                <a href="products.php?cat_id=15" style="color: #A69E96; text-decoration: none;">Women's Couture &amp; Lawn</a>
                <a href="products.php?cat_id=14" style="color: #A69E96; text-decoration: none;">Men's Luxury Jacquard</a>
                <a href="products.php?cat_id=16" style="color: #A69E96; text-decoration: none;">Kids' Festive Edition</a>
                <a href="ucat.php" style="color: #A69E96; text-decoration: none;">Browse Collections</a>
            </div>
        </div>

        <div class="mishae-footer-links">
            <h5 style="color: #fff; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">Client Services</h5>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                <a href="cart.php" style="color: #A69E96; text-decoration: none;">Shopping Bag</a>
                <a href="wishlist.php" style="color: #A69E96; text-decoration: none;">Saved Wishlist</a>
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="account.php" style="color: #A69E96; text-decoration: none;">My Profile &amp; Orders</a>
                    <a href="logout.php" style="color: #A69E96; text-decoration: none;">Sign Out</a>
                <?php else: ?>
                    <a href="login.php" style="color: #A69E96; text-decoration: none;">Log In</a>
                    <a href="register.php" style="color: #A69E96; text-decoration: none;">Sign Up</a>
                <?php endif; ?>
                <a href="about.php" style="color: #A69E96; text-decoration: none;">Our Story &amp; Craft</a>
                <a href="contact.php" style="color: #A69E96; text-decoration: none;">Concierge &amp; Support</a>
            </div>
        </div>

        <div class="mishae-footer-contact">
            <h5 style="color: #fff; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;">Exclusive Privileges</h5>
            <p style="font-size: 12px; color: #A69E96; line-height: 1.7; margin-bottom: 15px;">
                Join our private salon list for early access to unstitched lawn drops, festive releases, and bespoke styling advice.
            </p>
            <form id="footerNewsletterForm" onsubmit="handleMishaeNewsletter(event, this);" style="display: flex; gap: 6px;">
                <input type="email" required placeholder="Enter your email" style="flex: 1; padding: 10px 14px; border: 1px solid #3A3532; background: #1A1A1E; color: #fff; border-radius: 4px; font-size: 12px; outline: none;">
                <button type="submit" class="btn-footer-newsletter">JOIN</button>
            </form>
            <div id="newsletterSuccessMsg" style="display: none; margin-top: 10px; font-size: 12px; color: #a78543;">
                <i class="fas fa-check-circle mr-1"></i> Thank you for subscribing to MiSHAÉ privileges.
            </div>
            <div style="margin-top: 15px; font-size: 11px; color: #8F8881;">
                <i class="fab fa-whatsapp text-success me-1"></i> WhatsApp: <strong>+92 300 1234567</strong>
            </div>
        </div>
    </div>

    <div class="mishae-footer-bottom" style="border-top: 1px solid #1E1E22; padding-top: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; font-size: 12px; color: #77716C;">
        <p style="margin: 0;">
            &copy; <?php echo date('Y'); ?> <strong>MiSHAÉ LUXURY FABRICS</strong>. All Rights Reserved.
        </p>
        <div style="display: flex; gap: 15px; align-items: center;">
            <span style="font-size: 11px; letter-spacing: 1px;">SECURE PAYMENTS:</span>
            <span style="background: #1A1A1E; padding: 3px 8px; border-radius: 3px; font-size: 10px; color: #E8DDD0;">CASH ON DELIVERY</span>
            <span style="background: #1A1A1E; padding: 3px 8px; border-radius: 3px; font-size: 10px; color: #E8DDD0;">BANK TRANSFER</span>
            <span style="background: #1A1A1E; padding: 3px 8px; border-radius: 3px; font-size: 10px; color: #E8DDD0;">JAZZCASH / EASYPAISA</span>
        </div>
    </div>
</footer>

<?php include __DIR__ . '/confirm-modal.php'; ?>

<style>
.btn-footer-newsletter {
    padding: 10px 18px;
    background: #29211f;
    border: 1px solid #3A3532;
    color: #FFFFFF !important;
    font-weight: 600;
    font-size: 11px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.btn-footer-newsletter:hover {
    background: #a78543 !important;
    border-color: #a78543 !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
}

@media (max-width: 900px) {
    .mishae-footer-main {
        grid-template-columns: 1fr 1fr !important;
        gap: 30px !important;
    }
}
@media (max-width: 600px) {
    .mishae-footer-main {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function handleMishaeNewsletter(e, form) {
    e.preventDefault();
    form.style.display = 'none';
    var successMsg = document.getElementById('newsletterSuccessMsg');
    if (successMsg) {
        successMsg.style.display = 'block';
    }
}
</script>
