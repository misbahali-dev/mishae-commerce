<?php
require_once 'admin/config/config.php';

$products = [
    [
        'name' => 'Classic White Shalwar Kameez',
        'price' => 5800.00,
        'quan' => 15,
        'cat_id' => 14,
        'img' => 'mens_white_shalwar_kameez.jpg',
        'season' => 'All Seasons',
        'des' => "A pristine traditional white shalwar kameez tailored from high-count breathable combed cotton with an elegant band collar and fine cuffs.\nIdeal for Friday prayers, festive Eid celebrations, and daytime gatherings with timeless Pakistani comfort."
    ],
    [
        'name' => 'Navy Blue Shalwar Kameez',
        'price' => 6400.00,
        'quan' => 15,
        'cat_id' => 14,
        'img' => 'mens_navy_shalwar_kameez.jpg',
        'season' => 'All Seasons',
        'des' => "Crafted from premium blended fabric in a rich deep navy blue with refined tonal placket stitching and clean cuffs.\nA dignified eastern ensemble designed for evening dinners, family occasions, and formal elegance."
    ],
    [
        'name' => 'Classic Black Shalwar Kameez',
        'price' => 6900.00,
        'quan' => 15,
        'cat_id' => 14,
        'img' => 'mens_black_shalwar_kameez.jpg',
        'season' => 'All Seasons',
        'des' => "A sophisticated jet black shalwar kameez tailored in premium wrinkle-resistant fabric with a sleek band collar and dark metallic buttons.\nDelivers a crisp, sharp silhouette that stays immaculate for formal weddings, dinners, and special events."
    ],
    [
        'name' => 'Earth Brown Shalwar Kameez',
        'price' => 6200.00,
        'quan' => 15,
        'cat_id' => 14,
        'img' => 'mens_brown_shalwar_kameez.jpg',
        'season' => 'All Seasons',
        'des' => "Tailored in a warm earth mocha brown shade using soft, durable cotton blend with a classic band collar and tailored long sleeves.\nA versatile eastern staple delivering all-day ease and understated traditional charm for any occasion."
    ]
];

foreach ($products as $p) {
    // Check if already exists
    $check = mysqli_query($conn, "SELECT pro_id FROM product WHERE pro_name = '" . mysqli_real_escape_string($conn, $p['name']) . "' LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $pid = $row['pro_id'];
        $stmt = mysqli_prepare($conn, "UPDATE product SET pro_price = ?, pro_quan = ?, pro_des = ?, pro_img = ?, cat_id = ?, pro_season = ? WHERE pro_id = ?");
        mysqli_stmt_bind_param($stmt, "dissisi", $p['price'], $p['quan'], $p['des'], $p['img'], $p['cat_id'], $p['season'], $pid);
        mysqli_stmt_execute($stmt);
        echo "Updated existing product ID $pid: {$p['name']}\n";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO product (pro_name, pro_price, pro_quan, pro_des, pro_img, cat_id, pro_season) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sdissis", $p['name'], $p['price'], $p['quan'], $p['des'], $p['img'], $p['cat_id'], $p['season']);
        mysqli_stmt_execute($stmt);
        $new_id = mysqli_insert_id($conn);
        echo "Inserted new product ID $new_id: {$p['name']}\n";
    }
}
