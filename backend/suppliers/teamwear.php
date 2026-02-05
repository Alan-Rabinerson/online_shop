<?php
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/functions/write_logJSON.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://remotehost.es/student024/Shop/APIs/other_shop/teamwear.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
$result = curl_exec($ch);
curl_close($ch);
$products= json_decode($result, true);
$sql = "DELETE FROM `024_products` WHERE supplier_id = 2";
$query_products = mysqli_query($conn, $sql);
$sql_delete_sizes = "DELETE FROM `024_product_sizes` WHERE product_id IN (SELECT product_id FROM `024_products` WHERE supplier_id = 2)";
$sql_products = "SELECT count(*) FROM `024_products`";
$query_products_count = mysqli_query($conn, $sql_products);
$count = mysqli_fetch_array($query_products_count)[0];

$query_sizes = mysqli_query($conn, $sql_delete_sizes);
if ($query_products && $query_sizes) { // insertar siempre que se haya borrado correctamente
    $sql_max = "SELECT IFNULL(MAX(product_id), 0) AS maxid FROM `024_products`";
    $res_max = mysqli_query($conn, $sql_max);
    $maxid = 0;
    if ($res_max) {
        $row_max = mysqli_fetch_assoc($res_max);
        $maxid = isset($row_max['maxid']) ? intval($row_max['maxid']) : 0;
    }
    $next_ai = $maxid + 1;
    $sql_alter = "ALTER TABLE `024_products` AUTO_INCREMENT = $next_ai";
    mysqli_query($conn, $sql);
    foreach ($products as $product) {
        $sql = "INSERT INTO `024_products` (name, description, long_description, price, supplier_id, product_code, image_url, available_sizes) VALUES ('" . mysqli_real_escape_string($conn, $product['product_name']) . "', '" . mysqli_real_escape_string($conn, $product['product_desc']) . "', '" . mysqli_real_escape_string($conn, $product['product_name']) . "', " . floatval($product['product_price']) . ", 2, '" . mysqli_real_escape_string($conn, $product['product_id']) . "', '" . mysqli_real_escape_string($conn, json_encode($product['product_image'])) . "', 'XS,S,M,L,XL')";
        if (mysqli_query($conn, $sql)) {
            $sql_get_id = "SELECT product_id FROM `024_products` WHERE product_code = '" . mysqli_real_escape_string($conn, $product['product_id']) . "'";
            $query_get_id = mysqli_query($conn, $sql_get_id);
            $product_id = mysqli_fetch_array($query_get_id)[0];
            $sql_sizes = "INSERT INTO `024_product_sizes` (product_id, size, stock) VALUES ($product_id, 'XS', $product[product_stock] ), ($product_id, 'S', $product[product_stock]), ($product_id, 'M', $product[product_stock]), ($product_id, 'L', $product[product_stock]), ($product_id, 'XL', $product[product_stock])";
            mysqli_query($conn, $sql_sizes);
            write_logJSON("New record created successfully for supplier Teamwear with product code " . $product['product_id'], "insert", "products", "changes_log.json");
        } else {
            write_logJSON("Error creating record for product: " . $product['product_name'] . " - " . mysqli_error($conn), "insert", "products", "changes_log.json");
            $message = urlencode("Error creating record for product: " . $product['product_name']);
            header("Location: /student024/Shop/backend/views/products.php?error=$message");
        }
    }
    // tras insertar todos los productos, insertar las tallas disponibles

    header('Location: /student024/Shop/backend/views/products.php?message=' . urlencode('Products from Teamwear supplier inserted successfully.'));
}
?>
