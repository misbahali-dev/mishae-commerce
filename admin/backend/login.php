<?php
session_start();
require '../config/config.php';

if (isset($_POST['btn'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $pass = trim($_POST['password'] ?? '');

    $query = "SELECT * FROM ad_login WHERE ad_email='$email' LIMIT 1";
    $res = mysqli_query($conn, $query);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);

        if ($pass === $row['ad_password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['ad_name'];
            $_SESSION['admin_email'] = $row['ad_email'];

            header('location: ./../home.php');
            exit();
        } else {
            $_SESSION['res'] = array(
                'type' => 'danger',
                'msg' => 'Invalid password. Please check your credentials.'
            );
            header('location: ./../index.php');
            exit();
        }
    } else {
        $_SESSION['res'] = array(
            'type' => 'danger',
            'msg' => 'No administrator found with this email.'
        );
        header('location: ./../index.php');
        exit();
    }
}
?>
