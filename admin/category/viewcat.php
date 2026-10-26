
<?php
include './../config/config.php';


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
/** @var mysqli $conn*/

$query = "SELECT * FROM category ORDER BY cat_id DESC";
$res = mysqli_query($conn, $query);

$total = mysqli_num_rows($res);

$activequery = "SELECT * FROM category WHERE cat_status = 1";
$activeRes = mysqli_query($conn, $activequery);
$totalActive = mysqli_num_rows($activeRes);

$imageQuery = "SELECT * FROM category WHERE cat_img != ''";
$imageRes = mysqli_query($conn, $imageQuery);
$totalImages = mysqli_num_rows($imageRes);

$i = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | View Categories</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="./../assets/css/layout.css">
    <link rel="stylesheet" href="./../assets/css/category.css">

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
                        <span>All Categories</span>
                    </div>

                    <h1 class="page-title">Categories</h1>

                    <p class="page-description">
                        Manage and organise your clothing collections.
                    </p>
                </div>

                <a href="addcat.php" class="category-btn category-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    Add Category
                </a>
            </div>


            <?php if (isset($_SESSION['category_message'])) { 
                $cat_msg = (string)$_SESSION['category_message'];
                $is_cat_error = preg_match('/(not|failed|could not|error)/i', $cat_msg);
            ?>
                <div class="category_message <?= $is_cat_error ? 'category_message_error' : '' ?>" id="category_message" data-type="<?= $is_cat_error ? 'error' : 'success' ?>" role="alert">
                   <span><?= htmlspecialchars($cat_msg) ?></span> 
                   <button type="button" class="alert-close-btn" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php unset($_SESSION['category_message']); } ?>



            <!-- SUMMARY CARDS -->

            <div class="category-summary">

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>

                    <div>
                        <p>Total Categories</p>
                        <h3><?= $total ?></h3>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div>
                        <p>Active Categories</p>
                        <h3><?= $totalActive ?></h3>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fa-regular fa-image"></i>
                    </div>

                    <div>
                        <p>Category Images</p>
                        <h3><?= $totalImages ?></h3>
                    </div>
                </div>

            </div>

            <!-- CATEGORY TABLE -->


           


            <section class="category-card category-table-card">

                <div class="category-table-header">
                    <div>
                        <h2>All Categories</h2>
                        <p>View and manage your clothing categories.</p>
                    </div>

                    <div class="category-search">
                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input type="search"
                               placeholder="Search categories..."
                               id="categorySearch">
                    </div>
                </div>

                <div class="category-table-wrapper">

                    <table class="premium-category-table">

                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="categoryTableBody">

                        <?php while ($row = mysqli_fetch_assoc($res)) { ?>

                            <tr>
                                <td><?= $i++ ?></td>

                                <td>
                                    <?php if ($row['cat_img']) { ?>
                                        <img
                                            src="./../uploads/<?=$row['cat_img']?>"
                                            class="table-category-image"
                                            alt="Category image">
                                    <?php } ?>

                                    <div class="table-category-name">
                                        <?= $row['cat_name'] ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="table-category-description">
                                        <?= htmlspecialchars($row['cat_des']) ?>
                                    </div>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="category-status <?= ($row['cat_status'] == 1) ? 'active' : 'inactive' ?>"
                                        data-id="<?= $row['cat_id'] ?>"
                                        data-status="<?= $row['cat_status'] ?>">

                                        <?= ($row['cat_status'] == 1) ? 'Active' : 'Inactive' ?>

                                    </button>
                                </td>

                                <td>
                                    <div class="category-actions">

                                        <!-- UPDATE BUTTON -->
                                        <button
                                            type="button"
                                            class="table-action edit-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#update<?= $row['cat_id'] ?>">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        <!-- DELETE BUTTON -->
                                        <button
                                            type="button"
                                            class="table-action edit-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#delete<?= $row['cat_id'] ?>">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
  <?php }  ?>
</tbody>
</table>
                       


                  
 <div id="noCategoriesFound" class="category-empty" hidden>
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <h3>No categories found</h3>
                        <p>Try searching with a different name.</p>
                    </div>

                </div>


                 <div class="category-table-footer">
                    <span id="categoryCount">
                        Showing <?= $total ?> categories
                    </span>

                    <span>MiSHAÉ Administration</span>
                </div>

              
                       
<?php mysqli_data_seek($res, 0); ?>
<?php while ($row = mysqli_fetch_assoc($res)) { ?>
                        <div class="modal fade"
                             id="update<?= $row['cat_id'] ?>"
                             tabindex="-1">

                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content misha-modal">

                                    <div class="modal-header">
                                        <div>
                                            <h5 class="modal-title">Update Category</h5>
                                            <small>Update your collection details.</small>
                                        </div>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>

                                    <form
                                        action="./../backend/category.php?update=<?= $row['cat_id'] ?>"
                                        method="POST"
                                        enctype="multipart/form-data">

                                        <div class="modal-body">

                                            <label>Category Name</label>
                                            <input
                                                type="text"
                                                name="cat_name"
                                                class="form-control"
                                                value="<?= $row['cat_name']?>"
                                                required>

                                            <br>

                                            <label>Description</label>
                                            <textarea
                                                name="cat_des"
                                                class="form-control"
                                                rows="3"
                                                required><?= $row['cat_des'] ?></textarea>

                                            <br>

                                            <label>Current Image</label><br>

                                            <?php if (!empty($row['cat_img'])) { ?>
                                                <img
                                                    src="./../uploads/<?= $row['cat_img'] ?>"
                                                    class="current-image"
                                                    alt="Current category image">
                                            <?php } ?>

                                            <input
                                                type="hidden"
                                                name="old_img"
                                                value="<?= htmlspecialchars($row['cat_img']) ?>">

                                            <br>

                                            <label>Change Image (Optional)</label>
                                            <input
                                                type="file"
                                                name="cat_img"
                                                class="form-control"
                                                accept="image/jpeg,image/png,image/webp">

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                    class="modal-btn btn btn-light"
                                                    data-bs-dismiss="modal">
                                                Cancel
                                            </button>

                                            <button type="submit"
                                                    name="update"
                                                    class="modal-btn modal-btn-save">
                                                Update Category
                                            </button>
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>


                        <!-- DELETE MODAL -->

                        <div class="modal fade"
                             id="delete<?= $row['cat_id'] ?>"
                             tabindex="-1">

                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content misha-modal">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Category?</h5>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        Are you sure you want to delete
                                        <strong><?=$row['cat_name'] ?></strong>?
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button"
                                                class="modal-btn btn btn-light"
                                                data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <form
                                            action="./../backend/category.php?delete=<?= $row['cat_id'] ?>"
                                            method="POST">

                                            <button type="submit"
                                                    name="delete"
                                                    class="modal-btn modal-btn-delete">
                                                Delete Category
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

 <?php
    include './../include/footer.php'; 
   ?>  

</div>

 

