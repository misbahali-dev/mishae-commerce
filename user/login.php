<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../admin/config/config.php';

// If already logged in, redirect to account.php
if (isset($_SESSION['customer_id'])) {
    header('Location: account.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both your email address and password.';
    } else {
        $query = "SELECT * FROM customers WHERE cus_email = '$email' LIMIT 1";
        $res = mysqli_query($conn, $query);

        if ($res && mysqli_num_rows($res) > 0) {
            $user = mysqli_fetch_assoc($res);
            // Verify password (plain or hash fallback)
            $valid = false;
            if (!empty($user['cus_pass'])) {
                if (password_verify($password, $user['cus_pass']) || $password === $user['cus_pass']) {
                    $valid = true;
                }
            } else {
                // If customer registered via checkout without password, grant access & update pass
                $hash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE customers SET cus_pass = '$hash' WHERE cus_id = {$user['cus_id']}");
                $valid = true;
            }

            if ($valid) {
                $_SESSION['customer_id'] = (int)$user['cus_id'];
                $_SESSION['customer_name'] = $user['cus_name'];
                $_SESSION['customer_email'] = $user['cus_email'];
                $_SESSION['customer_phone'] = $user['cus_phone'];

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => "Welcome back, {$user['cus_name']}!"
                ];
                header('Location: account.php');
                exit();
            } else {
                $error = 'Invalid password. Please check your login details and try again.';
            }
        } else {
            $error = 'No client account found with this email. Please register.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Sign In | MiSHAÉ Luxury Fabrics</title>

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
            <a href="login.php" class="auth-tab active">
                <i class="far fa-user mr-1"></i> Sign In
            </a>
            <a href="register.php" class="auth-tab">
                <i class="fas fa-user-plus mr-1"></i> Create Account
            </a>
        </div>

        <h2>Sign In</h2>
        <p>Access your bespoke orders, wishlist, and private privileges.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="checkout-form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="checkout-form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn-checkout" style="border: none;">SIGN IN</button>
        </form>

        <div class="auth-switch-prompt">
            <span>New to MiSHAÉ?</span>
            <a href="register.php">Create a New Account &rarr;</a>
        </div>
    </div>
</main>

<?php include './include/footer.php'; ?>

<!-- Bootstrap Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
