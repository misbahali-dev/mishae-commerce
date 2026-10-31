<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/** @var mysqli $conn*/

require './../config/config.php';

// Target upload directory
$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755, true);
}

/**
 * Validates, sanitizes, and moves an uploaded product image.
 *
 * @param string $input_name The $_FILES input key
 * @param string $target_dir The destination directory with trailing slash
 * @param string &$error_message Output variable for user-friendly error messages
 * @return string|false Filename on success, false on failure
 */
function handle_product_image_upload($input_name, $target_dir, &$error_message) {
    if (!isset($_FILES[$input_name]) || empty($_FILES[$input_name]['name'])) {
        $error_message = "Please select a product image.";
        return false;
    }

    $file = $_FILES[$input_name];

    // Check PHP upload error code
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = "The uploaded file exceeds the maximum allowed file size.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message = "The file was only partially uploaded. Please try again.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = "No file was uploaded.";
                break;
            default:
                $error_message = "Image upload failed with error code: " . $file['error'];
                break;
        }
        return false;
    }

    // Validate extension
    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        $error_message = "Invalid image format. Allowed formats are JPG, JPEG, PNG, and WEBP.";
        return false;
    }

    // Validate that it's a legitimate image file
    $img_info = @getimagesize($file['tmp_name']);
    if ($img_info === false) {
        $error_message = "The uploaded file is not a valid image.";
        return false;
    }

    // Sanitize base filename: replace spaces and special characters with underscores
    $raw_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $clean_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $raw_name);
    $clean_name = trim(preg_replace('/_+/', '_', $clean_name), '_');
    if (empty($clean_name)) {
        $clean_name = 'product';
    }

    $final_name = $clean_name . '.' . $ext;

    // Avoid overwriting existing files with the same name
    if (file_exists($target_dir . $final_name)) {
        $final_name = $clean_name . '_' . time() . '.' . $ext;
    }

    // Move uploaded file to uploads directory (supports both HTTP POST upload and CLI/test environments)
    $moved = is_uploaded_file($file['tmp_name']) 
        ? move_uploaded_file($file['tmp_name'], $target_dir . $final_name) 
        : copy($file['tmp_name'], $target_dir . $final_name);

    if (!$moved) {
        $error_message = "Could not save the uploaded image to the server directory.";
        return false;
    }

    return $final_name;
}

/* ADD PRODUCT */
if (isset($_POST['submit'])) {

    $name = trim($_POST['pro_name'] ?? '');
    $cat = (int)($_POST['cat_id'] ?? 0);
    $price = (float)($_POST['pro_price'] ?? 0);
    $quan = (int)($_POST['pro_quan'] ?? 0);
    $des = trim($_POST['pro_des'] ?? '');
    $season = in_array($_POST['pro_season'] ?? '', ['Summer', 'Winter', 'All Seasons'], true) ? $_POST['pro_season'] : 'All Seasons';

    if (empty($name) || $cat <= 0 || $price <= 0) {
        $_SESSION['product_message'] = "Please provide a valid product name, category, and price.";
        header('Location: ./../product/addpro.php');
        exit();
    }

    $upload_error = '';
    $img = handle_product_image_upload('pro_img', $upload_dir, $upload_error);
    if ($img === false) {
        $_SESSION['product_message'] = $upload_error;
        header('Location: ./../product/addpro.php');
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO product (pro_name, pro_price, pro_quan, pro_des, pro_img, cat_id, pro_season) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        // Types: s (name), d (price), i (quan), s (des), s (img), i (cat), s (season)
        mysqli_stmt_bind_param($stmt, "sdissis", $name, $price, $quan, $des, $img, $cat, $season);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['product_message'] = "Product added successfully.";
            header('Location: ./../product/viewpro.php');
            exit();
        } else {
            $_SESSION['product_message'] = "Database error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['product_message'] = "Database preparation failed: " . mysqli_error($conn);
    }

    $_SESSION['product_message'] = "Product could not be added.";
    header('Location: ./../product/addpro.php');
    exit();
}


/* UPDATE PRODUCT */
if (isset($_POST['update'])) {

    $id = (int)($_POST['pro_id'] ?? 0);
    $name = trim($_POST['pro_name'] ?? '');
    $cat = (int)($_POST['cat_id'] ?? 0);
    $price = (float)($_POST['pro_price'] ?? 0);
    $quan = (int)($_POST['pro_quan'] ?? 0);
    $des = trim($_POST['pro_des'] ?? '');
    $season = in_array($_POST['pro_season'] ?? '', ['Summer', 'Winter', 'All Seasons'], true) ? $_POST['pro_season'] : 'All Seasons';

    // Preserve existing image if no replacement is uploaded
    $image = trim($_POST['old_img'] ?? '');

    // If a new image file was selected
    if (!empty($_FILES['pro_img']['name'])) {
        $upload_error = '';
        $new_img = handle_product_image_upload('pro_img', $upload_dir, $upload_error);
        if ($new_img === false) {
            $_SESSION['product_message'] = $upload_error;
            header('Location: ./../product/viewpro.php');
            exit();
        }
        $image = $new_img;
    }

    $stmt = mysqli_prepare($conn, "UPDATE product SET pro_name = ?, cat_id = ?, pro_price = ?, pro_quan = ?, pro_des = ?, pro_img = ?, pro_season = ? WHERE pro_id = ?");
    if ($stmt) {
        // Types: s (name), i (cat), d (price), i (quan), s (des), s (image), s (season), i (id)
        mysqli_stmt_bind_param($stmt, "sidisssi", $name, $cat, $price, $quan, $des, $image, $season, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['product_message'] = "Product updated successfully.";
        } else {
            $_SESSION['product_message'] = "Product could not be updated: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['product_message'] = "Product could not be updated.";
    }

    header('Location: ./../product/viewpro.php');
    exit();
}


/* DELETE PRODUCT */
if (isset($_POST['delete'])) {

    $id = (int)($_POST['pro_id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['product_message'] = "Invalid product ID.";
        header('Location: ./../product/viewpro.php');
        exit();
    }

    try {
        // Fetch current product image so we can optionally delete physical file if unused
        $pro_img_to_delete = null;
        $img_stmt = mysqli_prepare($conn, "SELECT pro_img FROM product WHERE pro_id = ?");
        if ($img_stmt) {
            mysqli_stmt_bind_param($img_stmt, "i", $id);
            mysqli_stmt_execute($img_stmt);
            $res = mysqli_stmt_get_result($img_stmt);
            if ($row = mysqli_fetch_assoc($res)) {
                $pro_img_to_delete = $row['pro_img'];
            }
            mysqli_stmt_close($img_stmt);
        }

        // Cleanly unlink any past order items referencing this product so foreign key constraint never blocks deletion
        $clean_stmt = mysqli_prepare($conn, "UPDATE order_items SET pro_id = NULL WHERE pro_id = ?");
        if ($clean_stmt) {
            mysqli_stmt_bind_param($clean_stmt, "i", $id);
            mysqli_stmt_execute($clean_stmt);
            mysqli_stmt_close($clean_stmt);
        }

        // Delete the product
        $stmt = mysqli_prepare($conn, "DELETE FROM product WHERE pro_id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['product_message'] = "Product deleted successfully.";

                // Safely remove image file if not shared by other products
                if (!empty($pro_img_to_delete)) {
                    $check_shared = mysqli_prepare($conn, "SELECT COUNT(*) FROM product WHERE pro_img = ?");
                    if ($check_shared) {
                        mysqli_stmt_bind_param($check_shared, "s", $pro_img_to_delete);
                        mysqli_stmt_execute($check_shared);
                        $s_res = mysqli_stmt_get_result($check_shared);
                        $count = $s_res ? (int)mysqli_fetch_row($s_res)[0] : 0;
                        mysqli_stmt_close($check_shared);

                        if ($count === 0) {
                            $filePath = __DIR__ . '/../uploads/' . $pro_img_to_delete;
                            if (file_exists($filePath) && is_file($filePath) && !in_array($pro_img_to_delete, ['logo.png', 'default.png'])) {
                                @unlink($filePath);
                            }
                        }
                    }
                }
            } else {
                $_SESSION['product_message'] = "Product could not be deleted.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['product_message'] = "Product could not be deleted due to a database preparation error.";
        }
    } catch (Throwable $e) {
        $_SESSION['product_message'] = "Product deletion error: " . $e->getMessage();
    }

    header('Location: ./../product/viewpro.php');
    exit();
}
