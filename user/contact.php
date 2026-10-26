<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

$message = "";
$message_type = "success";

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['send_message'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject'] ?? ''));
    $msg = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($msg)) {
        $query = "INSERT INTO contact_messages (name, email, phone, subject, message)
                  VALUES ('$name', '$email', '$phone', '$subject', '$msg')";

        if (mysqli_query($conn, $query)) {
            $message = "Thank you, {$name}. Your message has reached our client concierge team. We will respond within 24 business hours.";
            $message_type = "success";
        } else {
            $message = "Apologies, your inquiry could not be sent at this moment. Please reach out to us directly via WhatsApp.";
            $message_type = "danger";
        }
    } else {
        $message = "Please complete all required fields.";
        $message_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact &amp; Concierge | MiSHAÉ Luxury Fabrics</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- MiSHAÉ Styles -->
    <link rel="stylesheet" href="assets/css/layout.css?v=3.7">
    <link rel="stylesheet" href="assets/css/contact.css?v=3.7">
    <link rel="stylesheet" href="assets/css/home.css?v=3.7">
    <link rel="stylesheet" href="assets/css/store.css?v=3.7">
</head>
<body>

<?php include "./include/topbar.php"; ?>
<?php include "./include/navbar.php"; ?>

<!-- Single Clean Page Header -->
<section class="page-intro-header">
    <div class="container text-center">
        <h1 class="page-intro-title">Contact Us</h1>
        <p class="page-intro-desc">We are here to assist with fabric inquiries, order tracking, and bespoke appointments.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="contact-container">
        <!-- Contact Information -->
        <div class="contact-info">
            <h3>Atelier Headquarters</h3>
            <p class="contact-info-text">
                Dedicated client specialists are available Monday through Saturday, 10:00 AM – 8:00 PM PKT.
            </p>

            <div class="contact-info-item">
                <span class="contact-icon">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <div>
                    <h4>Direct Email</h4>
                    <p>concierge@mishae.com</p>
                </div>
            </div>

            <div class="contact-info-item">
                <span class="contact-icon">
                    <i class="fa-solid fa-phone"></i>
                </span>
                <div>
                    <h4>Helpline &amp; WhatsApp</h4>
                    <p>+92 300 1234567</p>
                </div>
            </div>

            <div class="contact-info-item">
                <span class="contact-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </span>
                <div>
                    <h4>Flagship Salons</h4>
                    <p>Gulberg III, Lahore &bull; Clifton, Karachi &bull; Islamabad</p>
                </div>
            </div>

            <div class="contact-note">
                <p>MiSHAÉ</p>
                <span>Elegance in Every Thread &bull; Pakistan</span>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-box">
            <h3>Inquiry Form</h3>
            <p>Please provide details of your inquiry and our team will get back to you promptly.</p>

            <?php if (!empty($message)): ?>
                <div class="mishae-alert mishae-alert-<?php echo $message_type; ?>" style="margin: 0 0 20px;">
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <div class="contact-form-row">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="contact-form-row">
                    <div class="form-group">
                        <label for="phone">Phone / WhatsApp</label>
                        <input type="tel" id="phone" name="phone" placeholder="e.g. 0300 1234567">
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" placeholder="Fabric Inquiry, Order Tracking, etc." required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">Your Message *</label>
                    <textarea id="message" name="message" rows="5" placeholder="Please write your detailed message here..." required></textarea>
                </div>

                <button type="submit" name="send_message" class="contact-btn">
                    SEND INQUIRY <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<?php include "./include/footer.php"; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>