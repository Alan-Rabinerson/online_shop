<?php
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    // fetch all products from database
    $sql = "SELECT * FROM 024_products";
    $result = mysqli_query($conn, $sql);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // display each product
    foreach($products as $product){
        $product_id = $product['product_id'];
        // Display product details
        echo "<div class='product-card mt-2'>";
        if ($product['supplier_id'] == 2){
            echo "<h4 class='text-sm mb-1'>Supplier: Teamwear</h4>";
            echo "<img src='" . json_decode($product['image_url']) . "' alt='" . $product['name'] . "' class='w-48 h-48 object-cover mb-2 rounded-lg shadow-md'>";
        }
        elseif ($product['supplier_id'] == 3){
            echo "<h4 class='text-sm mb-1'>Supplier: Shift&Go</h4>";
            echo "<img src='" . json_decode($product['image_url']) . "' alt='" . $product['name'] . "' class='w-48 h-48 object-cover mb-2 rounded-lg shadow-md'>";
        }
        else{
            echo "<img src='/student024/Shop/assets/imagenes/foto" . $product_id . ".jpg' alt='" . $product['name'] . "' class='w-48 h-48 object-cover mb-2 rounded-lg shadow-md'>";
        }
        echo "<h3>" . $product['name']  ."</h3>";
        echo "<p>Price: $" . $product['price'] . "</p>";
        echo "<p>Description: " . $product['description'] . "</p>";
        if ($_SESSION['role'] === 'admin') {
            include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/forms/products/form_product_update_call.php';
            include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/forms/products/form_product_delete.php';
        }
        include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/forms/shopping_cart/form_shopping_cart_insert.php';
        echo "</div><hr>";
    }

?>