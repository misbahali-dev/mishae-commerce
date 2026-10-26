<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

if (isset($_SESSION['customer_id'])) {
    header('Location: account.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in your name, email, and password.';
    } else {
        $check = mysqli_query($conn, "SELECT cus_id FROM customers WHERE cus_email = '$email' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $error = 'An account with this email address already exists. Please sign in.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO customers (cus_name, cus_email, cus_phone, cus_pass) VALUES ('$name', '$email', '$phone', '$hash')");

            if ($insert) {
                $cus_id = mysqli_insert_id($conn);
                $_SESSION['customer_id'] = $cus_id;
                $_SESSION['customer_name'] = $name;
                $_SESSION['customer_email'] = $email;
                $_SESSION['customer_phone'] = $phone;

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Welcome to MiSHAÉ! Your client profile has been created.'
                ];
                header('Location: account.php');
                exit();
            } else {
                $error = 'Could not register account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Client Account | MiSHAÉ Luxury Fabrics</title>

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
<body style="background: #FAF8F5;">

<?php include './include/topbar.php'; ?>
<?php include './include/navbar.php'; ?>

<main class="container py-5" style="min-height: 70vh;">
    <div class="auth-container">
        <!-- Separate Switcher Tabs -->
        <div class="mishae-auth-tabs">
            <a href="login.php" class="auth-tab">
                <i class="far fa-user mr-1"></i> Sign In
            </a>
            <a href="register.php" class="auth-tab active">
                <i class="fas fa-user-plus mr-1"></i> Create Account
            </a>
        </div>

        <h2>Create Account</h2>
        <p>Register for exclusive previews, bespoke invitations, and simplified checkout.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="checkout-form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required placeholder="e.g. Fatima Ali" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="checkout-form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required placeholder="e.g. fatima@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="checkout-form-group">
                <label>WhatsApp / Mobile Phone</label>
                <input type="tel" name="phone" placeholder="e.g. 0300 1234567" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="checkout-form-group">
                <label>Create Password *</label>
                <input type="password" name="password" required minlength="6" placeholder="At least 6 characters">
            </div>

            <button type="submit" class="btn-checkout" style="border: none;">CREATE ACCOUNT</button>
        </form>

        <div class="auth-switch-prompt">
            <span>Already have an account?</span>
            <a href="login.php">Sign In to Your Account &rarr;</a>
        </div>
    </div>
</main>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
