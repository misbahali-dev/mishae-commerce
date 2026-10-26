
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

    <title>MiSHAÉ | Add Product</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="./../assets/css/layout.css">
    <link rel="stylesheet" href="./../assets/css/product.css">
</head>

<body>

<div class="admin-wrapper">

    <?php include './../include/sidebar.php'; /** @var mysqli $conn*/?>

    <div class="main-content">

        <?php include './../include/header.php'; ?>

        <main class="page-content">

            <!-- PAGE HEADER -->
            <div class="page-header">

                <div>
                    <div class="breadcrumb">
                        <a href="./../home.php">Dashboard</a>
                         <i class="fa-solid fa-chevron-right"></i>
                        <span>Add Product</span>
                    </div>

                    <h1 class="page-title">Add Product</h1>

                    <p class="page-description">
                        Add a new clothing item to your MiSHAÉ collection.
                    </p>
                </div>

               <a href="viewpro.php" class="product-btn product-btn-primary">
                   View All Product
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

            <!-- PRODUCT FORM LAYOUT -->
            <div class="product-layout">

                <section class="product-card">

                    <div class="product-card-header">
                        <div>
                            <h2>Product Information</h2>
                            <p>Enter the product details below.</p>
                        </div>

                        <div class="product-heading-icon">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                    </div>

                    <form action="./../backend/product.php" method="POST" enctype="multipart/form-data">



                     <?php
                  require './../config/config.php';
                  $query="SELECT * FROM category";
                  $res=mysqli_query($conn,$query);
                

                  ?>
                        <!-- PRODUCT NAME -->
                        <div class="product-field">
                            <label for="product_name">
                                Product Name <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="pro_name"
                                class="product-input"
                                placeholder="e.g. Embroidered Lawn Suit"
                                required
                            >
                        </div>

                        <!-- CATEGORY -->
                        <div class="product-field">
                            <label for="category_id">
                                Category <span class="required">*</span>
                            </label>

                            <select
                                name="cat_id"
                                class="product-input"
                                required
                            >
                                <option value="">Select Category</option>
                                  <?php
    
    while($row=mysqli_fetch_array($res)){

        ?>
        <option value="<?=  $row['cat_id']?>"><?= $row['cat_name'] ?></option>
<?php
    } 
    ?>
                            </select>

                            <small>Select the category that best matches this product.</small>
                        </div>

                        <!-- SEASONAL COLLECTION -->
                        <div class="product-field">
                            <label for="pro_season">
                                Seasonal Collection
                            </label>

                            <select
                                name="pro_season"
                                id="pro_season"
                                class="product-input"
                            >
                                <option value="All Seasons" selected>All Seasons (Default)</option>
                                <option value="Summer">Summer Collection</option>
                                <option value="Winter">Winter Collection</option>
                            </select>

                            <small>Assign this product to Summer, Winter, or All Seasons.</small>
                        </div>

                        <!-- PRICE AND STOCK -->
                        <div class="product-form-grid">

                            <div class="product-field">
                                <label for="product_price">
                                    Price (PKR) <span class="required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="pro_price"
                                    class="product-input"
                                    placeholder="e.g. 4500"
                                    min="1"
                                    step="0.01"
                                    required
                                >
                            </div>

                            <div class="product-field">
                                <label for="product_stock">
                                    Stock Quantity <span class="required">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="pro_quan"
                                    class="product-input"
                                    placeholder="e.g. 20"
                                    min="0"
                                    step="1"
                                    required
                                >
                            </div>

                        </div>

                        <!-- DESCRIPTION -->
                        <div class="product-field">
                            <label for="product_description">
                                Product Description
                            </label>

                            <textarea
                                name="pro_des"
                                class="product-input product-textarea"
                                placeholder="Describe the fabric, design and product details..."
                            ></textarea>
                        </div>

                        <!-- PRODUCT IMAGE -->
                        <div class="product-field">
                            <label for="product_image">
                                Product Image <span class="required">*</span>
                            </label>

                            <div class="product-upload">

                                <i class="fa-solid fa-cloud-arrow-up"></i>

                                <h3>Upload Product Image</h3>

                                <p>Choose a clear image of your clothing item.</p>

                                <span>JPG, JPEG, PNG or WEBP</span>

                                <input
                                    type="file"
                                    name="pro_img"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    required
                                >

                            </div>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="product-form-actions">

                            <a href="viewpro.php"
                               class="product-btn product-btn-secondary">
                                Cancel
                            </a>

                            <button type="reset"
                                    class="product-btn product-btn-outline">
                                Reset
                            </button>

                            <button
                            
                            name="submit"
                            type="submit"
                                    class="product-btn product-btn-primary">
                                <i class="fa-solid fa-plus"></i>
                                Add Product
                            </button>

                        </div>

                    </form>

                </section>

                <!-- PRODUCT GUIDELINES -->
                <aside class="product-card product-tips-card">

                    <div class="product-card-header">
                        <div>
                            <h2>Product Guidelines</h2>
                            <p>Keep your product catalogue organised.</p>
                        </div>
                    </div>

                    <div class="product-tip">
                        <div class="product-tip-icon">
                            <i class="fa-solid fa-tag"></i>
                        </div>

                        <div>
                            <h3>Product Name</h3>
                            <p>Use a clear name that describes the clothing item.</p>
                        </div>
                    </div>

                    <div class="product-tip">
                        <div class="product-tip-icon">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>

                        <div>
                            <h3>Correct Category</h3>
                            <p>Assign the product to the appropriate clothing category.</p>
                        </div>
                    </div>

                    <div class="product-tip">
                        <div class="product-tip-icon">
                            <i class="fa-regular fa-image"></i>
                        </div>

                        <div>
                            <h3>Quality Images</h3>
                            <p>Use clear images that accurately represent your products.</p>
                        </div>
                    </div>

                    <div class="product-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <p>Fields marked with * are required.</p>
                    </div>

                </aside>

            </div>

        </main>

      
    </div>
  <?php include './../include/footer.php'; ?>

</div>



