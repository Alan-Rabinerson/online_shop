<?php 
    // Avoid sending headers or printing output when this file is included.
    $is_direct_call = (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? ''));
    if ($is_direct_call) {
        header('Content-Type: application/json; charset=UTF-8');
    }
    $api_key_shift_and_go = '3c25f95c-e89f-4a9c-b61f-61c8cd435bc9';
    $api_key_teamwear = '12345Alan'; 
    $api_key_brand2 = '10203040B';
    $url = ''; // URL base del endpoint del proveedor (ajustar según el entorno)

    if ($supplier_id == 2) {
        $apiKey = $api_key_teamwear;
        $url = "https://remotehost.es/student014/shop/backend/endpoints/product_seller.php";
    } elseif ($supplier_id == 3) {
        $apiKey = $api_key_shift_and_go;
        $url = "https://remotehost.es/student012/shop/backend/endpoints/seller_orders.php";
    } elseif ($supplier_id == 4) {
        $apiKey = $api_key_brand2;
        $url = "https://remotehost.es/student022/backend/apis/sellers/api_endpoint_recibe_orders.php";
    } 
    else {
        header("Location: /student024/Shop/backend/views/my_orders.php?error=Invalid+supplier+ID");
        exit();
    }
    // Ensure variables exist and are strings to avoid warnings/deprecations
    $first_name = isset($first_name) ? (string)$first_name : (isset($_SESSION['first_name']) ? (string)$_SESSION['first_name'] : (isset($_SESSION['username']) ? (string)$_SESSION['username'] : ''));
    $last_name = isset($last_name) ? (string)$last_name : (isset($_SESSION['last_name']) ? (string)$_SESSION['last_name'] : '');
    $customer_email = isset($_SESSION['customer_email']) ? (string)$_SESSION['customer_email'] : (isset($_SESSION['email']) ? (string)$_SESSION['email'] : '');
    $product_id = isset($product_id) ? (int)$product_id : 0;
    $quantity = isset($quantity) ? (int)$quantity : 0;
    $street = isset($street) ? (string)$street : '';
    $city = isset($city) ? (string)$city : '';
    $zip_code = isset($zip_code) ? (string)$zip_code : '';
    $province = isset($province) ? (string)$province : '';

    $query_url = $url . '?apikey=' . rawurlencode($apiKey)
        . '&product_id=' . rawurlencode((string)$product_id)
        . '&product_quantity=' . rawurlencode((string)$quantity)
        . '&customer_forename=' . rawurlencode($first_name)
        . '&customer_surname=' . rawurlencode($last_name)
        . '&customer_email=' . rawurlencode($customer_email)
        . '&customer_address=' . rawurlencode($street)
        . '&customer_location=' . rawurlencode($city)
        . '&customer_zip=' . rawurlencode($zip_code)
        . '&customer_country=' . rawurlencode($province);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $result = curl_exec($ch);
    curl_close($ch);
    if ($is_direct_call) {
        echo $result;
    }