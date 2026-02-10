<?php
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/functions/write_logJSON.php';

// Fetch products JSON from remote API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://remotehost.es/student024/Shop/APIs/other_shop/shift_and_go.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
$curl_err = curl_error($ch);
curl_close($ch);

if ($curl_err) {
    write_logJSON("cURL error fetching Shift&Go products: $curl_err", "error", "products", "changes_log.json");
    header("Location: /student024/Shop/backend/views/products.php?error=" . urlencode('Error fetching supplier data'));
    exit;
}

$products = json_decode($result, true);
if (!is_array($products)) {
    write_logJSON("Invalid JSON from Shift&Go API: " . json_last_error_msg(), "error", "products", "changes_log.json");
    header("Location: /student024/Shop/backend/views/products.php?error=" . urlencode('Invalid supplier JSON'));
    exit;
}

// Remove existing sizes and products for this supplier
$sql_delete_sizes = "DELETE FROM `024_product_sizes` WHERE product_id IN (SELECT product_id FROM `024_products` WHERE supplier_id = 3)";
if (!mysqli_query($conn, $sql_delete_sizes)) {
    write_logJSON("Error deleting product sizes: " . mysqli_error($conn), "error", "products", "changes_log.json");
    header("Location: /student024/Shop/backend/views/products.php?error=" . urlencode('DB error deleting sizes'));
    exit;
}

$sql_delete_products = "DELETE FROM `024_products` WHERE supplier_id = 3";
if (!mysqli_query($conn, $sql_delete_products)) {
    write_logJSON("Error deleting products: " . mysqli_error($conn), "error", "products", "changes_log.json");
    header("Location: /student024/Shop/backend/views/products.php?error=" . urlencode('DB error deleting products'));
    exit;
}

// Insert products (let DB assign AUTO_INCREMENT IDs)
foreach ($products as $product) {
    $name = mysqli_real_escape_string($conn, $product['product_name']);
    $desc = mysqli_real_escape_string($conn, $product['product_desc']);
    $price = floatval($product['product_price']);
    $code = mysqli_real_escape_string($conn, $product['product_id']);
    $image = mysqli_real_escape_string($conn, json_encode($product['product_image']));

    $sql_ins = "INSERT INTO `024_products` (name, description, long_description, price, supplier_id, product_code, image_url, available_sizes) VALUES ('" . $name . "', '" . $desc . "', '" . $desc . "', " . $price . ", 3, '" . $code . "', '" . $image . "', '40,41,42,43,44,45,46')";
    if (mysqli_query($conn, $sql_ins)) {
        $product_id = mysqli_insert_id($conn);
        $sql_sizes = "INSERT INTO `024_product_sizes` (product_id, size, stock) VALUES ($product_id, '40', 10 ), ($product_id, '41', 10), ($product_id, '42', 10), ($product_id, '43', 10), ($product_id, '44', 10), ($product_id, '45', 10), ($product_id, '46', 10)";
        if (!mysqli_query($conn, $sql_sizes)) {
            write_logJSON("Error inserting sizes for product_id $product_id: " . mysqli_error($conn), "error", "products", "changes_log.json");
        } else {
            write_logJSON("New record created successfully for supplier Shift&Go with product code " . $product['product_id'], "insert", "products", "changes_log.json");
        }
    } else {
        write_logJSON("Error creating record for product: " . $product['product_name'] . " - " . mysqli_error($conn), "insert", "products", "changes_log.json");
        $message = urlencode("Error creating record for product: " . $product['product_name']);
        header("Location: /student024/Shop/backend/views/products.php?error=$message");
    }
}

header('Location: /student024/Shop/backend/views/products.php?message=' . urlencode('Products from Shift&Go supplier inserted successfully.'));

?>
