<?php
header('Content-Type: application/json; charset=UTF-8');

require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';

$api_key = 'e888b918-330e-43c5-a103-111d57a4a28f';

// Validate method and presence of api_key
    if ( isset($_GET['apikey']) && $_GET['apikey'] === $api_key) {
    correctKey();
} else {
    wrongKey();
}

function correctKey() {
    global $conn;
    $sql = "SELECT product_id, name AS product_name, price as product_price, description as product_desc, available_sizes AS product_size, image_url as product_image FROM `024_products` WHERE supplier_id = 1 LIMIT 5";
    //$sql = "SELECT * FROM `024_products` LIMIT 5";
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        http_response_code(500);
        echo json_encode(['error' => 'DB query failed', 'detail' => mysqli_error($conn)]);
        return;
    }
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($products as $product) {
        $product['product_image'] = json_decode($product['product_image'])[0]; // Decodificar el JSON y tomar la primera imagen
    }
    echo json_encode($products);
}

function wrongKey() {
    http_response_code(403);
    echo json_encode(array('message' => 'Forbidden: Invalid or missing API Key'));
}

?>

