<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../admin/config/config.php';

// Initialize Cart and Wishlist if not set
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['wishlist']) || !is_array($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || isset($_POST['ajax']) || isset($_GET['ajax']);

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)($item['quantity'] ?? 1);
        }
    }
    return $count;
}

switch ($action) {
    case 'add':
        $pro_id = (int)($_POST['pro_id'] ?? $_GET['pro_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1);
        if ($quantity < 1) $quantity = 1;

        $selected_option = trim($_POST['selected_option'] ?? $_GET['selected_option'] ?? 'Unstitched (Standard 3-Piece)');
        if (empty($selected_option)) {
            $selected_option = 'Unstitched (Standard 3-Piece)';
        }

        $action_type = $_POST['action_type'] ?? (isset($_POST['buy_now']) ? 'buy_now' : 'add_cart');

        if ($pro_id > 0) {
            $query = "SELECT product.*, category.cat_name 
                      FROM product 
                      LEFT JOIN category ON product.cat_id = category.cat_id 
                      WHERE product.pro_id = $pro_id AND product.pro_quan > 0 LIMIT 1";
            $res = mysqli_query($conn, $query);

            if ($res && mysqli_num_rows($res) > 0) {
                $pro = mysqli_fetch_assoc($res);
                $unit_price = (float)$pro['pro_price'];
                $max_stock = (int)$pro['pro_quan'];
                $quantity = min($quantity, $max_stock);

                // Deterministic cart key based on product ID
                $cart_key = (string)$pro_id;
                if (!empty($selected_option) && $selected_option !== 'Standard Piece') {
                    $cart_key .= '_' . substr(md5($selected_option), 0, 8);
                }

                // Branch based on Action Type: Buy Now vs Add to Cart
                if ($action_type === 'buy_now') {
                    // BUY NOW: Immediate direct checkout for this specific item and quantity
                    $_SESSION['is_buy_now'] = true;
                    $_SESSION['buy_now_item'] = [
                        'cart_key' => 'buy_now_' . $pro_id,
                        'pro_id' => (int)$pro['pro_id'],
                        'pro_name' => $pro['pro_name'],
                        'pro_price' => $unit_price,
                        'pro_img' => $pro['pro_img'],
                        'cat_name' => $pro['cat_name'] ?? 'Luxury Collection',
                        'option' => $selected_option,
                        'quantity' => $quantity, // Exactly the requested quantity; never accumulated or doubled!
                        'max_stock' => $max_stock
                    ];

                    if ($is_ajax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'status' => 'success',
                            'message' => "Proceeding to checkout...",
                            'cart_count' => getCartCount(),
                            'redirect' => 'checkout.php'
                        ]);
                        exit();
                    }

                    header('Location: checkout.php');
                    exit();
                } else {
                    // ADD TO CART: Standard shopping bag addition
                    unset($_SESSION['is_buy_now'], $_SESSION['buy_now_item']);

                    if (isset($_SESSION['cart'][$cart_key])) {
                        $new_qty = min($_SESSION['cart'][$cart_key]['quantity'] + $quantity, $max_stock);
                        $_SESSION['cart'][$cart_key]['quantity'] = $new_qty;
                    } else {
                        $_SESSION['cart'][$cart_key] = [
                            'cart_key' => $cart_key,
                            'pro_id' => (int)$pro['pro_id'],
                            'pro_name' => $pro['pro_name'],
                            'pro_price' => $unit_price,
                            'pro_img' => $pro['pro_img'],
                            'cat_name' => $pro['cat_name'] ?? 'Luxury Collection',
                            'option' => $selected_option,
                            'quantity' => $quantity,
                            'max_stock' => $max_stock
                        ];
                    }

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => "“{$pro['pro_name']}” has been added to your shopping cart."
                    ];

                    if ($is_ajax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'status' => 'success',
                            'message' => "Added to shopping cart successfully!",
                            'cart_count' => getCartCount(),
                            'redirect' => ''
                        ]);
                        exit();
                    }

                    $redirect = "product-detail.php?id=$pro_id";
                    header("Location: $redirect");
                    exit();
                }
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'danger',
                    'text' => 'Selected piece is currently out of stock.'
                ];
            }
        }
        break;

    case 'update':
        $key = trim($_POST['cart_key'] ?? $_POST['pro_id'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!empty($key) && isset($_SESSION['cart'][$key])) {
            $max = (int)($_SESSION['cart'][$key]['max_stock'] ?? 99);
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Item removed from bag.'
                ];
            } else {
                $_SESSION['cart'][$key]['quantity'] = min($quantity, $max);
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Bag updated successfully.'
                ];
            }
        }
        header('Location: cart.php');
        exit();

    case 'remove':
        $key = trim($_GET['key'] ?? $_GET['pro_id'] ?? '');
        if (!empty($key) && isset($_SESSION['cart'][$key])) {
            $name = $_SESSION['cart'][$key]['pro_name'];
            unset($_SESSION['cart'][$key]);
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => "“{$name}” has been removed from your bag."
            ];
        }
        header('Location: cart.php');
        exit();

    case 'clear':
        $_SESSION['cart'] = [];
        unset($_SESSION['coupon']);
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'Your shopping bag has been cleared.'
        ];
        header('Location: cart.php');
        exit();

    case 'apply_coupon':
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
        if ($code === 'MISHAE15') {
            $_SESSION['coupon'] = [
                'code' => 'MISHAE15',
                'discount_percent' => 15,
                'type' => 'percent',
                'desc' => '15% Off Exclusive Privilege'
            ];
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => 'Coupon MISHAE15 applied! 15% discount has been deducted.'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'text' => 'Invalid promotional code.'
            ];
        }
        header('Location: cart.php');
        exit();

    case 'remove_coupon':
        unset($_SESSION['coupon']);
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'Promotional coupon removed.'
        ];
        header('Location: cart.php');
        exit();

    case 'wishlist_toggle':
        $p_id = (int)($_GET['pro_id'] ?? 0);
        $status = 'error';
        $p_name = 'Piece';
        $message = '';

        if ($p_id > 0) {
            $p_query = mysqli_query($conn, "SELECT pro_name FROM product WHERE pro_id = $p_id LIMIT 1");
            if ($p_query && $p_row = mysqli_fetch_assoc($p_query)) {
                $p_name = $p_row['pro_name'];
            }

            if (isset($_SESSION['wishlist'][$p_id])) {
                unset($_SESSION['wishlist'][$p_id]);
                $status = 'removed';
                $message = "\"$p_name\" removed from your wishlist.";
                $_SESSION['flash_message'] = [
                    'type' => 'info',
                    'text' => $message,
                    'action' => 'wishlist_removed',
                    'pro_id' => $p_id,
                    'pro_name' => $p_name
                ];
            } else {
                $_SESSION['wishlist'][$p_id] = true;
                $status = 'added';
                $message = "\"$p_name\" added to your wishlist.";
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => $message,
                    'action' => 'wishlist_added',
                    'pro_id' => $p_id,
                    'pro_name' => $p_name
                ];
            }
        }

        // Check if requested via AJAX
        $is_ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || isset($_GET['ajax']) 
                   || isset($_POST['ajax']);

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => ($p_id > 0),
                'status' => $status,
                'pro_id' => $p_id,
                'pro_name' => $p_name,
                'count' => count($_SESSION['wishlist'] ?? []),
                'message' => $message
            ]);
            exit();
        }

        $ref = $_SERVER['HTTP_REFERER'] ?? 'wishlist.php';
        header("Location: $ref");
        exit();

    default:
        header('Location: products.php');
        exit();
}
