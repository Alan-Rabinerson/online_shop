<?php
header('Content-Type: application/json; charset=UTF-8');

require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';

$api_key_shift_and_go = '85c712e7-6a84-4a5a-87a3-47b25df6771b';
$api_key_teamwear = 'e888b918-330e-43c5-a103-111d57a4a28f'; 
$api_key_brand2 = 'ba6e471d-3721-4959-afba-d2f55d021b9f';
// Validate method and presence of api_key
    if ( isset($_GET['apikey']) && ($_GET['apikey'] === $api_key_shift_and_go || $_GET['apikey'] === $api_key_teamwear || $_GET['apikey'] === $api_key_brand2)) {
    correctKey();
} else {
    wrongKey();
}

function correctKey() {
    global $conn;
    $sql = "SELECT product_id, name AS product_name, price AS product_price, description AS product_desc, available_sizes AS product_size, image_url AS product_image FROM `024_products` WHERE supplier_id = 1 LIMIT 5";
    //$sql = "SELECT * FROM `024_products` LIMIT 5";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        header('Content-Type: application/json', true, 500);
        echo json_encode(['error' => 'DB query failed', 'detail' => mysqli_error($conn)]);
        exit;
    }
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($products as &$product) {
        $images = json_decode($product['product_image'], true);
        if (is_array($images) && count($images) > 0) {
            $first = reset($images);
            $product['product_image'] = $first !== false ? $first : null;
        } else {
            $product['product_image'] = null;
        }
    }
    unset($product);
    echo json_encode($products);
}

function wrongKey() {
    http_response_code(403);
    echo json_encode(array('message' => 'Forbidden: Invalid or missing API Key'));
}

?>

