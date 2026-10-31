

<?php

session_start();

include './../config/config.php';

/** @var mysqli $conn*/





$query = "SELECT product.*, category.cat_name

          FROM product

          LEFT JOIN category ON product.cat_id = category.cat_id

          ORDER BY product.pro_id DESC";



$res = mysqli_query($conn, $query);



$total = mysqli_num_rows($res);



$stockRes = mysqli_query($conn, "SELECT * FROM product WHERE pro_quan >= 3");

$totalinstock = mysqli_num_rows($stockRes);



$lowRes = mysqli_query($conn, "SELECT * FROM product WHERE pro_quan > 0 AND pro_quan < 3");

$totallow = mysqli_num_rows($lowRes);





$catRes = mysqli_query($conn, "SELECT * FROM category WHERE cat_status = 1");















?>



<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | View Products</title>



  <link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">



<link rel="stylesheet" href="./../assets/css/layout.css?v=2.3">

<link rel="stylesheet" href="./../assets/css/product.css?v=2.3">



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</head>



<body>



<div class="admin-wrapper">



    <?php include './../include/sidebar.php'; ?>



    <div class="main-content">



        <?php include './../include/header.php'; ?>



        <main class="page-content">



            <div class="page-header">

                <div>

                    <div class="breadcrumb">

                        <a href="./../home.php">Dashboard</a>

                        <i class="fa-solid fa-chevron-right"></i>

                        <span>All Products</span>

                    </div>



                    <h1 class="page-title">Products</h1>

                    <p class="page-description">

                        Manage your MiSHAÉ clothing products.

                    </p>

                </div>



                <a href="addpro.php" class="product-btn product-btn-primary">

                    <i class="fa-solid fa-plus"></i> Add Product

                </a>

            </div>



            <?php if (isset($_SESSION['product_message'])) { 
                $pro_msg = (string)$_SESSION['product_message'];
                $is_pro_error = preg_match('/(not|failed|could not|error)/i', $pro_msg);
            ?>
                <div class="product-message <?= $is_pro_error ? 'product-message-error' : '' ?>" id="product_message" data-type="<?= $is_pro_error ? 'error' : 'success' ?>" role="alert">
                    <span><?= htmlspecialchars($pro_msg) ?></span>
                    <button type="button" class="alert-close-btn" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['product_message']); ?>
            <?php } ?>











            <div class="product-summary">



                <div class="product-summary-card">

                    <div class="product-summary-icon">

                        <i class="fa-solid fa-box-open"></i>

                    </div>

                    <div>

                        <p>Total Products</p>

                        <h3><?= $total ?></h3>

                    </div>

                </div>



                <div class="product-summary-card">

                    <div class="product-summary-icon">

                        <i class="fa-solid fa-check-circle"></i>

                    </div>

                    <div>

                        <p>In Stock</p>

                        <h3><?= $totalinstock ?></h3>

                    </div>

                </div>



                <div class="product-summary-card">

                    <div class="product-summary-icon">

                        <i class="fa-solid fa-triangle-exclamation"></i>

                    </div>

                    <div>

                        <p>Low Stock</p>

                        <h3><?= $totallow ?></h3>

                    </div>

                </div>







            </div>



            <section class="product-card product-table-card">



                <div class="product-table-header">

                    <div>

                        <h2>All Products</h2>

                        <p>View and manage your clothing inventory.</p>

                    </div>



                    <div class="product-search">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input type="search" id="productSearch"

                               placeholder="Search products...">

                    </div>

                </div>



                <div class="product-table-wrapper" style="scrollbar-width: none; -ms-overflow-style: none;">

                    <table class="premium-product-table">



                        <thead>

                            <tr>

                                <th>Sr.No.</th>

                                <th>Product</th>

                                <th>Category</th>

                                <th>Price</th>

                                <th>Quantity</th>

                                <th>Season</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>



                        <tbody id="productTableBody">



                        <?php

                        $i = 1;



                        while ($row = mysqli_fetch_assoc($res)) {

                        ?>



                            <tr>

                                <td><?= $i++ ?></td>



                                <td>

                                    <div class="product-table-info">

                                        <img
                                            src="./../uploads/<?= htmlspecialchars($row['pro_img']) ?>"
                                            alt="<?= htmlspecialchars($row['pro_name']) ?>"
                                            class="product-table-image"
                                        >



                                        <div>

                                            <h4><?= $row['pro_name'] ?></h4>



                                        </div>

                                    </div>

                                </td>



                                <td><?= $row['cat_name'] ?? 'Uncategorised' ?></td>



                                <td class="product-price">

                                    PKR <?= number_format((float)$row['pro_price']) ?>

                                </td>



                                <td><?= (int)$row['pro_quan'] ?></td>

                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size:11px;"><?= htmlspecialchars($row['pro_season'] ?? 'All Seasons') ?></span>
                                </td>

                                <td>

                                    <?php if ($row['pro_quan'] == 0) { ?>

                                        <span class="product-status low-stock">

                                            <span></span> Out of Stock

                                        </span>

                                    <?php } elseif ($row['pro_quan'] < 3) { ?>

                                        <span class="product-status low-stock">

                                            <span></span> Low Stock

                                        </span>

                                    <?php } else { ?>

                                        <span class="product-status in-stock">

                                            <span></span> In Stock

                                        </span>

                                    <?php } ?>

                                </td>



                                <td>

                                    <div class="product-actions">



                                     <button type="button"

    class="product-action edit-product"

    data-bs-toggle="modal"

    data-bs-target="#update<?= $row['pro_id'] ?>"

    data-id="<?= $row['pro_id'] ?>"

    data-name="<?=$row['pro_name'] ?>"

    data-cat="<?= $row['cat_id'] ?>"

    data-price="<?= $row['pro_price'] ?>"

    data-quan="<?= $row['pro_quan'] ?>"

    data-des="<?= $row['pro_des'] ?>"

     data-img="<?= $row['pro_img']?>"

    title="Edit">

    <i class="fa-regular fa-pen-to-square"></i>

</button>



<button type="button"

    class="product-action delete-action delete-product"

    data-bs-toggle="modal"

    data-bs-target="#delete<?= $row['pro_id'] ?>"

    data-id="<?= $row['pro_id'] ?>"

    data-name="<?=$row['pro_name'] ?>"

    title="Delete">

    <i class="fa-regular fa-trash-can"></i>

</button>

                                    </div>

                                </td>

                            </tr>



                        <?php } ?>



                        </tbody>

                    </table>



                    <div id="noProductsFound" class="product-empty" hidden>

                        <i class="fa-solid fa-box-open"></i>

                        <h3>No products found</h3>

                        <p>Try searching with a different product name.</p>

                    </div>

                </div>



                <div class="product-table-footer">

                    <span id="productCount">Showing <?= $total ?> products</span>

                    <span>MiSHAÉ Administration</span>

                </div>





<?php mysqli_data_seek($res, 0); ?>



<?php while ($row = mysqli_fetch_assoc($res)) { ?>



<!-- UPDATE MODAL -->

<div class="modal fade" id="update<?= $row['pro_id'] ?>" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content misha-modal">



            <div class="modal-header">

                <div>

                    <h5 class="modal-title">Update Product</h5>

                    <small>Update your product details.</small>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>



            <form action="./../backend/product.php" method="POST" enctype="multipart/form-data">



                <input type="hidden" name="update" value="1">

                <input type="hidden" name="pro_id" value="<?= $row['pro_id'] ?>">



                <div class="modal-body">



                    <label>Product Name</label>

                    <input type="text" name="pro_name"

                           class="form-control"

                           value="<?= htmlspecialchars($row['pro_name'] ?? '') ?>"

                           required>

                    <br>



                    <label>Category</label>

                    <select name="cat_id" class="form-select" required>

                        <option value="">Select Category</option>



                        <?php

                        mysqli_data_seek($catRes, 0);

                        while ($cat = mysqli_fetch_assoc($catRes)) {

                        ?>

                            <option value="<?= $cat['cat_id'] ?>"

                                <?= ($cat['cat_id'] == $row['cat_id']) ? 'selected' : '' ?>>

                                <?= htmlspecialchars($cat['cat_name']) ?>

                            </option>

                        <?php } ?>



                    </select>

                    <br>



                    <label>Price (PKR)</label>

                    <input type="number" name="pro_price"

                           class="form-control"

                           value="<?= $row['pro_price'] ?>"

                           min="1" step="0.01" required>

                    <br>



                    <label>Stock Quantity</label>

                    <input type="number" name="pro_quan"

                           class="form-control"

                           value="<?= $row['pro_quan'] ?>"

                           min="0" required>

                    <br>



                    <label>Seasonal Collection</label>
                    <select name="pro_season" class="form-select">
                        <option value="All Seasons" <?= (($row['pro_season'] ?? '') === 'All Seasons') ? 'selected' : '' ?>>All Seasons</option>
                        <option value="Summer" <?= (($row['pro_season'] ?? '') === 'Summer') ? 'selected' : '' ?>>Summer Collection</option>
                        <option value="Winter" <?= (($row['pro_season'] ?? '') === 'Winter') ? 'selected' : '' ?>>Winter Collection</option>
                    </select>
                    <br>

                    <label>Description</label>

                    <textarea name="pro_des" class="form-control" rows="3"><?= htmlspecialchars($row['pro_des'] ?? '') ?></textarea>

                    <br>



                    <label>Current Image</label><br>



                    <?php if (!empty($row['pro_img'])) { ?>

                        <img src="./../uploads/<?= htmlspecialchars($row['pro_img']) ?>"

                             class="current-image"

                             alt="Current product image"

                             style="width:100px; height:100px; object-fit:cover;">

                    <?php } ?>



                    <input type="hidden" name="old_img"

                           value="<?= htmlspecialchars($row['pro_img'] ?? '') ?>">



                    <br><br>



                    <label>New Product Image (Optional)</label>

                    <input type="file" name="pro_img"

                           class="form-control"

                           accept=".jpg,.jpeg,.png,.webp">



                </div>



                <div class="modal-footer">

                    <button type="button" class="modal-btn btn btn-light"

                            data-bs-dismiss="modal">Cancel</button>



                    <button type="submit" class="modal-btn modal-btn-save">

                        Update Product

                    </button>

                </div>



            </form>

        </div>

    </div>

</div>





<!-- DELETE MODAL -->

<div class="modal fade" id="delete<?= $row['pro_id'] ?>" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content misha-modal">



            <div class="modal-header">

                <h5 class="modal-title">Delete Product?</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>



            <div class="modal-body">

                Are you sure you want to delete

                <strong><?= htmlspecialchars($row['pro_name']) ?></strong>?

            </div>



            <div class="modal-footer">

                <button type="button" class="modal-btn btn btn-light"

                        data-bs-dismiss="modal">Cancel</button>



                <form action="./../backend/product.php" method="POST">

                    <input type="hidden" name="pro_id"

                           value="<?= $row['pro_id'] ?>">



                    <button type="submit" name="delete" value="1"

                            class="modal-btn modal-btn-delete">

                        Delete Product

                    </button>

                </form>

            </div>



        </div>

    </div>

</div>



<?php } ?>



</section>



</main>



</div>



<?php include './../include/footer.php'; ?>



</div>