<?php
header('Content-Type: application/json; charset=UTF-8');
require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
$api_key_shift_and_go = '85c712e7-6a84-4a5a-87a3-47b25df6771b';
$api_key_teamwear = 'e888b918-330e-43c5-a103-111d57a4a28f';
$api_key_brand2 = 'ba6e471d-3721-4959-afba-d2f55d021b9f';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['apikey'] === $api_key_shift_and_go || $_GET['apikey'] === $api_key_teamwear || $_GET['apikey'] === $api_key_brand2)) {
    // correctKey inline
    $order_date = date('Y-m-d H:i:s');
    $orders_array = json_decode($_GET['orders_json'] ?? null, true);
    
    // Si es un array, tomar el primer elemento
    if (is_array($orders_array) && isset($orders_array[0])) {
        $order = $orders_array[0];
    } else {
        $order = $orders_array;
    }

    // create_guest_and_get_customer_id inline
    // Generar un guest_id único
    $guest_id = intval(microtime(true) * 1000) % 9999999999;
    
    $first_name = $order['customer_forename'] ?? 'guest_' . $guest_id;
    $last_name = $order['customer_surname'] ?? 'guest_' . $guest_id;
    $email = $order['customer_email'] ?? 'guest_' . $guest_id . '@guest.local';
    $phone = $order['customer_phone'] ?? $guest_id;
    $username = 'guest_' . $guest_id;
    $password = '';
    $city = ($order['customer_location'] ?? 'guest city');
    $street = ($order['customer_address'] ?? 'guest street 123');
    $postal_code = ($order['customer_zip'] ?? '12345');
    $province = ($order['customer_country'] ?? 'guest country');
    $type = 'customer';

    // Insertar el cliente guest en la base de datos
    $sql = "INSERT INTO `024_customers` (`customer_id`, `first_name`, `last_name`, `email`, `username`, `password`, `phone`, `birth_date`, `type`) VALUES ($guest_id, '" . $first_name . "', '" . $last_name . "', '" . $email . "', '" . $username . "', '" . $password . "', '" . $phone . "', CURDATE(), '" . $type . "')";

    if ($conn->query($sql) === TRUE) {
        $customer_id = $conn->insert_id; // Obtener el customer_id generado
    } else {
        throw new Exception('Error creando guest: ' . $conn->error);
    }
    // fin create_guest_and_get_customer_id inline
    
    $sql = "INSERT INTO 024_address (`address_name`, `street`, `city`, `zip_code`, `province`) VALUES ( 'guest address' ,'" . mysqli_real_escape_string($conn, $street ?? '') . "', '" . mysqli_real_escape_string($conn, $city ?? '') . "', '" . mysqli_real_escape_string($conn, $postal_code ?? '') . "', '" . mysqli_real_escape_string($conn, $province ?? '') . "')";
    if (!mysqli_query($conn, $sql)) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/logs/error_log.txt', date('Y-m-d H:i:s') . " - Error insertando dirección: " . mysqli_error($conn) . " - SQL: $sql\n", FILE_APPEND);
        throw new Exception('Error insertando dirección: ' . mysqli_error($conn));
    }
    $address_id = mysqli_insert_id($conn);

    $product_id = $order['product_code'] ?? 0;
    $quantity = $order['product_quantity'] ?? 0;
    $order_date = date('Y-m-d H:i:s');
    $sql = 'SELECT price FROM 024_products WHERE product_id = ' . intval($product_id);
    $result = mysqli_query($conn, $sql);
    if (!$result || mysqli_num_rows($result) == 0) {
        throw new Exception('Producto ' . $product_id . ' no encontrado');
    }
    $product_price = mysqli_fetch_assoc($result)['price'];
    $sql = "INSERT INTO 024_orders_table (product_id, quantity, order_date , customer_id, size, price, address_id, payment_method, status) VALUES (" . intval($product_id) . ", " . intval($quantity) . ", '" . mysqli_real_escape_string($conn, $order_date) . "', " . intval($customer_id) . ", 'M', " . floatval($product_price) . ", " . intval($address_id) . ", 'cash', 'PROCESSING')";

    if (!mysqli_query($conn, $sql)) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/logs/error_log.txt', date('Y-m-d H:i:s') . " - Error insertando orden: " . mysqli_error($conn) . " - SQL: $sql\n", FILE_APPEND);
        throw new Exception('Error insertando orden: ' . mysqli_error($conn));
    }
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/logs/sellers_orders.log', date('Y-m-d H:i:s') . " - Order created for customer ID: $customer_id\n", FILE_APPEND);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Ordenes insertadas exitosamente',
        'customer_id' => $customer_id
    ]);
    // fin correctKey inline
} else {
    // wrongKey inline
    http_response_code(403);
    echo json_encode(array("message" => "Forbidden: Invalid or missing API Key"));
}