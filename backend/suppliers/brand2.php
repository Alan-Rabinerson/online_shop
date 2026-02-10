<?php
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/functions/write_logJSON.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://remotehost.es/student024/Shop/APIs/other_shop/brand2.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
$result = curl_exec($ch);
curl_close($ch);
$products= json_decode($result, true);
$sql_delete_sizes = "DELETE FROM `024_product_sizes` WHERE product_id IN (SELECT product_id FROM `024_products` WHERE supplier_id = 4)";
$sql_products = "SELECT count(*) FROM `024_products`";
$query_sizes = mysqli_query($conn, $sql_delete_sizes);
$query_products_count = mysqli_query($conn, $sql_products);
$count = mysqli_fetch_array($query_products_count)[0];
$sql = "DELETE FROM `024_products` WHERE supplier_id = 4";
$query = mysqli_query($conn, $sql);

if ($query_sizes && $query ) { // insertar siempre que se haya borrado correctamente
    // determine highest existing product_id and set AUTO_INCREMENT to max+1
    $sql_max = "SELECT IFNULL(MAX(product_id), 0) AS maxid FROM `024_products`";
    $res_max = mysqli_query($conn, $sql_max);
    $maxid = 0;
    if ($res_max) {
        $row_max = mysqli_fetch_assoc($res_max);
        $maxid = isset($row_max['maxid']) ? intval($row_max['maxid']) : 0;
    }
    $next_ai = $maxid + 1;
    $sql_alter = "ALTER TABLE `024_products` AUTO_INCREMENT = $next_ai";
    if (!mysqli_query($conn, $sql_alter)) {
        write_logJSON("Failed to set AUTO_INCREMENT for 024_products: " . mysqli_error($conn), "error", "products", "changes_log.json");
    }
    foreach ($products as $product) {
        $sql = "INSERT INTO `024_products` (name, description, long_description, price, supplier_id, product_code, image_url, available_sizes) VALUES ('" . mysqli_real_escape_string($conn, $product['product_name']) . "', '" . mysqli_real_escape_string($conn, $product['product_desc']) . "', '" . mysqli_real_escape_string($conn, $product['product_desc']) . "', " . floatval($product['product_price']) . ", 4, '" . mysqli_real_escape_string($conn, $product['product_id']) . "', '" . mysqli_real_escape_string($conn, json_encode($product['product_image'])) . "', 'S,L')";
        if (mysqli_query($conn, $sql)) {
            // get last inserted id (more reliable than SELECT by product_code)
            $product_id = mysqli_insert_id($conn);

            // insert sizes with error checking
            $sql_sizes = "INSERT INTO `024_product_sizes` (product_id, size, stock) VALUES ($product_id, 'S', 10 ), ($product_id, 'L', 10)";
            if (!mysqli_query($conn, $sql_sizes)) {
                write_logJSON("Error inserting sizes for product_id $product_id: " . mysqli_error($conn), "error", "products", "changes_log.json");
            } else {
                write_logJSON("New record created successfully for supplier brand2 with product code " . $product['product_id'], "insert", "products", "changes_log.json");
            }
        } else {
            write_logJSON("Error creating record for product: " . $product['product_name'] . " - " . mysqli_error($conn), "insert", "products", "changes_log.json");
            $message = urlencode("Error creating record for product: " . $product['product_name']);
            header("Location: /student024/Shop/backend/views/products.php?error=$message");
        }
    }
    header('Location: /student024/Shop/backend/views/products.php?message=' . urlencode('Products from brand2 supplier inserted successfully.'));
}