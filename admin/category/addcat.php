<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Add Category</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS Files -->
    <link rel="stylesheet" href="./../assets/css/layout.css">
    <link rel="stylesheet" href="./../assets/css/category.css">
</head>

<body>

<div class="admin-wrapper">

    <?php include './../include/sidebar.php'; ?>

    <div class="main-content">

        <?php include './../include/header.php'; ?>

        <main class="page-content">

            <!-- PAGE HEADER -->
            <div class="page-header">

                <div>
                    <div class="breadcrumb">
                        <a href="./../home.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Add Category</span>
                    </div>

                    <h1 class="page-title">Add Category</h1>

                    <p class="page-description">
                        Create and organise categories for your clothing collections.
                    </p>
                </div>

                <a href="viewcat.php" class="category-btn category-btn-primary">
           
                    View All Categories
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

            <!-- MAIN CONTENT -->
            <div class="category-layout">

                <!-- CATEGORY FORM -->
                <section class="category-card">

                    <div class="category-card-header">
                        <div>
                            <h2>Category Information</h2>
                            <p>Enter the details for your new category.</p>
                        </div>

                        <div class="category-heading-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                    </div>

                    <form action="./../backend/category.php" method="POST" enctype="multipart/form-data">

                        <div class="category-field">
                            <label for="category_name">
                                Category Name <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="catname"
                                class="category-input"
                                placeholder="e.g. Casual Wear"
                                required
                            >

                            <small>Enter a clear and meaningful category name.</small>
                        </div>

                        <div class="category-field">
                            <label for="category_description">
                                Description
                                <span class="optional-label">(Optional)</span>
                            </label>

                            <textarea
                                name="catdes"
                                class="category-input category-textarea"
                                placeholder="Write a short description of this category..."
                            ></textarea>

                            <small>Describe the clothing collection briefly.</small>
                        </div>

                        <div class="category-field">
                            <label for="category_image">
                                Category Image <span class="required">*</span>
                            </label>

                            <div class="category-upload">
                                <i class="fa-solid fa-cloud-arrow-up"></i>

                                <h3>Upload Category Image</h3>

                                <p>Choose an image from your device.</p>

                                <span>JPG, JPEG, PNG or WEBP</span>

                                <input
                                    type="file"
                                    name="catimg"
                                  accept=".jpg,.jpeg,.png,.webp"
                                    required
                                >
                            </div>
                        </div>

                        <div class="category-form-actions">

                            <a href="./viewcat.php"
                               class="category-btn category-btn-secondary">
                                Cancel
                            </a>

                            <button type="reset"
                                    class="category-btn category-btn-outline">
                                Reset
                            </button>

                            <button type="submit"
                                 name="submit"
                                    class="category-btn category-btn-primary">
                                <i class="fa-solid fa-plus"></i>
                                Add Category
                            </button>

                        </div>

                    </form>

                </section>

                <!-- GUIDELINES -->
                <aside class="category-card category-tips-card">

                    <div class="category-card-header">
                        <div>
                            <h2>Category Guidelines</h2>
                            <p>Keep your collections organised.</p>
                        </div>
                    </div>

                    <div class="category-tip">
                        <div class="tip-icon">
                            <i class="fa-solid fa-tag"></i>
                        </div>

                        <div>
                            <h3>Clear Naming</h3>
                            <p>Use simple names such as Casual Wear, Formal Wear or Unstitched.</p>
                        </div>
                    </div>

                    <div class="category-tip">
                        <div class="tip-icon">
                            <i class="fa-regular fa-image"></i>
                        </div>

                        <div>
                            <h3>Quality Images</h3>
                            <p>Choose a clear image that represents the collection.</p>
                        </div>
                    </div>

                    <div class="category-tip">
                        <div class="tip-icon">
                            <i class="fa-solid fa-folder-tree"></i>
                        </div>

                        <div>
                            <h3>Stay Organised</h3>
                            <p>Use meaningful categories to make products easier to manage.</p>
                        </div>
                    </div>

                    <div class="category-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <p>Fields marked with * are required.</p>
                    </div>

                </aside>

            </div>

        </main>

       

    </div>
 <?php include './../include/footer.php'; ?>
</div>



