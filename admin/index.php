<?php
session_start();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mishae | Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/login.css?v=2.2">

      </head>
<body>

    <div class="login-page">


        <!-- BRAND SIDE -->

        <div class="brand-section">

            <div class="brand-content">

                <p class="small-text">
                    WELCOME TO
                </p>

                <h1>
                    MiSHAÉ
                </h1>

                <div class="line"></div>

                <h2>
                    LUXURY FABRICS
                </h2>

                <p class="description">
                    Manage your fabrics, categories, products,
                    orders and customers from your exclusive
                    Mishae administration panel.
                </p>

            </div>

        </div>



        <!-- LOGIN SIDE -->

        <div class="login-section">

            <div class="login-box">

                <div class="logo">
                    M
                </div>

                <p class="admin-text">
                    ADMINISTRATOR
                </p>

                <h2>
                    Welcome Back
                </h2>

                <p class="subtitle">
                    Sign in to access Mishae Admin Panel
                </p>

              
<div class="message-slot">
<?php if (isset($_SESSION['res'])): ?>

    <?php
        $login_msg = $_SESSION['res']['msg'] ?? 'Something went wrong.';
        $login_type = $_SESSION['res']['type'] ?? 'danger';
        $is_login_error = ($login_type === 'danger' || preg_match('/(not|failed|could not|error|invalid|wrong)/i', $login_msg));
    ?>
    <div class="login-message" role="alert" data-type="<?= $is_login_error ? 'error' : 'success' ?>">
        <span class="login-message-text">
            <i class="fa-solid <?= $is_login_error ? 'fa-circle-exclamation' : 'fa-circle-check' ?>" style="font-size: 14px;"></i>
            <?= htmlspecialchars($login_msg, ENT_QUOTES, 'UTF-8') ?>
        </span>
        <button type="button" class="alert-close-btn" aria-label="Close" title="Dismiss">&times;</button>
    </div>

    <?php unset($_SESSION['res']); ?>

<?php endif; ?>

</div>
                <form action="./backend/login.php" method="POST">


                    <div class="input-group">

                        <label>
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                        >

                    </div>



                    <div class="input-group">

                        <label>
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                    </div>



                    <div class="login-options">

                        <label class="remember">

                            <input type="checkbox">

                            Remember me

                        </label>


                        <a href="#">
                            Forgot Password?
                        </a>

                    </div>



                    <button type="submit" name="btn" class="login-submit-btn">
                        LOGIN TO DASHBOARD
                    </button>


                </form>



                <p class="copyright">
                    © 2026 Mishae Fabrics. All Rights Reserved.
                </p>

            </div>

        </div>

    </div>

<script src="./assets/js/script.js"></script>

</body>
</html>