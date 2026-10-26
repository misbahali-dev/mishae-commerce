<?php
session_start();
/** @var mysqli $conn*/
include './../config/config.php';

if (isset($_POST['submit'])) {

    $cat_name = $_POST['catname'];
    $cat_des = $_POST['catdes'];
    $cat_img = $_FILES['catimg']['name'];

    $path='./../uploads/'.$cat_img;
    move_uploaded_file($_FILES['catimg']['tmp_name'], $path);

    $query = "INSERT INTO category (cat_name, cat_img, cat_des)
              VALUES ('$cat_name', '$cat_img' ,'$cat_des')";

    $res = mysqli_query($conn, $query);

    if ($res) {
        $_SESSION['category_message'] = "Category added successfully.";
        header('Location: ./../category/viewcat.php');
        exit();
    } else {
        $_SESSION['category_message'] = "Category could not be added.";
        header('Location: ./../category/addcat.php');
        exit();
    }
}

if (isset($_POST['change_status'])) {

    $cat_id = (int) $_POST['cat_id'];
    $status = (int) $_POST['cat_status'];

    $new_status = ($status == 1) ? 0 : 1;

    $query = "UPDATE category SET cat_status = $new_status WHERE cat_id = $cat_id";
    $res = mysqli_query($conn, $query);

    header('Content-Type: application/json');

    echo json_encode([
        'success' => $res
    ]);

    exit();
}

// UPDATE CATEGORY
if (isset($_POST['update']) && isset($_GET['update'])) {

    $id = $_GET['update'];
    $cat_name = $_POST['cat_name'];
    $cat_des = $_POST['cat_des'];

    if ($_FILES['cat_img']['name'] != "") {
        $image = $_FILES['cat_img']['name'];
        $path = './../uploads/' . $image;

        move_uploaded_file($_FILES['cat_img']['tmp_name'], $path);
    } else {
        $image = $_POST['old_img'];
    }

    $stmt = mysqli_prepare($conn, "UPDATE category SET cat_name = ?, cat_des = ?, cat_img = ? WHERE cat_id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $cat_name, $cat_des, $image, $id);
        $res = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $res = false;
    }

    if ($res) {
        $_SESSION['category_message'] = "Category updated successfully.";
        header('Location: ./../category/viewcat.php');
        exit();
    } else {
        $_SESSION['category_message'] = "Category not Updated.";
        header('Location: ./../category/addcat.php');
        exit();
    }
}

// DELETE CATEGORY
if (isset($_POST['delete']) && isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $query = "DELETE FROM category WHERE cat_id='$id'";
    $res=mysqli_query($conn, $query);
    if ($res) {
        $_SESSION['category_message'] = "Category deleted successfully.";
        header('Location: ./../category/viewcat.php');
        exit();
    } else {
        $_SESSION['category_message'] = "Category not Updated.";
        header('Location: ./../category/addcat.php');
        exit();
    }
}
?>
