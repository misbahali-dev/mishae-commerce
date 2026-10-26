<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
unset($_SESSION['customer_email']);
unset($_SESSION['customer_phone']);
unset($_SESSION['cart']);
unset($_SESSION['wishlist']);
unset($_SESSION['coupon']);
unset($_SESSION['is_buy_now']);
unset($_SESSION['buy_now_item']);

$_SESSION['flash_message'] = [
    'type' => 'success',
    'text' => 'You have been successfully signed out.'
];

header('Location: home.php');
exit();
?>
