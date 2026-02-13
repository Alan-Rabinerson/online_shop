<?php
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/functions/write_logJSON.php';
include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/mail/mail.php';

$customer_id = isset($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : 0;
$payment_method = $_POST['payment_method'] ?? '';
// Safely parse cart_data: avoid passing null to json_decode (deprecated)
$cart_data_raw = $_POST['cart_data'] ?? null;
if (is_string($cart_data_raw) && $cart_data_raw !== '') {
    $cart_data = json_decode($cart_data_raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $cart_data = [];
    }
} elseif (is_array($cart_data_raw)) {
    $cart_data = $cart_data_raw;
} else {
    $cart_data = [];
}
// normalize selected address id: it may be posted as a plain id or as a JSON string/object
$selected_address_id_raw = $_POST['selected_address_id'] ?? '';
$selected_address = json_decode($_POST['full_selected_address'], true);
if (is_numeric($selected_address_id_raw)) {
    $selected_address_id = (int) $selected_address_id_raw;
} else {
    $decoded_addr = json_decode($selected_address_id_raw, true);
    if (is_array($decoded_addr) && isset($decoded_addr['address_id']) && is_numeric($decoded_addr['address_id'])) {
        $selected_address_id = (int) $decoded_addr['address_id'];
    } else {
        // fallback: try to get id from full_selected_address or set to 0
        $selected_address_id = isset($selected_address['address_id']) && is_numeric($selected_address['address_id']) ? (int) $selected_address['address_id'] : 0;
    }
}
$street = $selected_address['street'] ?? '';
$city = $selected_address['city'] ?? '';
$zip_code = $selected_address['postal_code'] ?? '';
$province = $selected_address['province'] ?? '';
if (!$cart_data || !is_array($cart_data) || count($cart_data) === 0) {
    if ($customer_id <= 0) {
        header("Location: /student024/Shop/backend/views/my_orders.php?error=Shopping+cart+is+empty");
        exit();
    }
    $sql = "SELECT * FROM 024_shopping_cart WHERE customer_id = " . (int) $customer_id;
    $result = mysqli_query($conn, $sql);
    $cart_data = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $cart_data[] = [
                'product_id' => $row['product_id'],
                'size' => $row['size'],
                'quantity' => $row['quantity']
            ];
        }
    } else {
        header("Location: /student024/Shop/backend/views/my_orders.php?error=Shopping+cart+is+empty");
        exit();
    }
}

foreach ($cart_data as $item) {
    $product_id = (int) $item['product_id'];
    $size = $item['size'] ?? '';
    $quantity = (int) $item['quantity'];
    $price_sql = "SELECT price, supplier_id FROM 024_products WHERE product_id = $product_id LIMIT 1";
    $price_res = mysqli_query($conn, $price_sql);
    $price_row = $price_res ? mysqli_fetch_assoc($price_res) : null;
    $price = $price_row ? (float) $price_row['price'] : 0.0;
    $supplier_id = $price_row ? (int) $price_row['supplier_id'] : 1; // default to 1 if not found
    $total_price = $price * $quantity;
    // Ensure payment method is properly quoted or cast for SQL
    if (is_numeric($payment_method)) {
        $payment_sql_value = (int) $payment_method;
    } else {
        $payment_sql_value = "'" . mysqli_real_escape_string($conn, $payment_method) . "'";
    }
    $sql = "INSERT INTO `024_orders_table` (`customer_id`, `product_id`, `size`, `quantity`, `price`, `address_id`, `method_id`, `status`, `order_date`) VALUES (" . (int) $customer_id . ", " . (int) $product_id . ", '" . mysqli_real_escape_string($conn, $size) . "', " . (int) $quantity . ", " . (float) $total_price . ", " . (int) $selected_address_id . ", " . $payment_sql_value . ", 'PROCESSING', NOW())";
    try {
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            $err = mysqli_error($conn);
            $logMsg = "[SQL ERROR] " . date('Y-m-d H:i:s') . " - error: $err - sql: $sql\n";
            @file_put_contents(rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, DIRECTORY_SEPARATOR) . '/student024/Shop/backend/logs/error_log.txt', $logMsg, FILE_APPEND);
            header("Location: /student024/Shop/backend/views/my_orders.php?error=Failed+to+place+order+for+product+$product_id");
            exit();
        }
    } catch (Throwable $e) {
        if (!headers_sent()) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => 'SQL exception', 'message' => $e->getMessage(), 'sql' => $sql]);
        }
        exit();
    }
    // After successful insert, handle supplier-specific order if needed
    $last_order_id = mysqli_insert_id($conn);
    if ($supplier_id != 1) {
        // suppliers_orders_insert.php can use $last_order_id, $product_id, $quantity, etc.
        require_once $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/suppliers/suppliers_orders_insert.php';
    }
    // update stock in 024_product_sizes
    $stock_sql = "SELECT stock FROM 024_product_sizes WHERE product_id = $product_id AND size = '$size' LIMIT 1"; // get current stock
    $stock_res = mysqli_query($conn, $stock_sql);
    $stock_row = $stock_res ? mysqli_fetch_assoc($stock_res) : null;
    $available_stock = $stock_row ? $stock_row['stock'] : 0;
    $new_stock = max(0, $available_stock - $quantity); // calculate new stock (if negative, set to 0)
    $update_stock_sql = "UPDATE 024_product_sizes SET stock = $new_stock WHERE product_id = $product_id AND size = '$size'";
    if (!mysqli_query($conn, $update_stock_sql)) {
        header("Location: /student024/Shop/backend/views/my_orders.php?error=Failed+to+update+stock+for+product+$product_id+size+$size");
        exit();
    }
    // if (mysqli_query($conn, $update_stock_sql)) { // DEBUGGING
    //     echo "<p>Stock for product $product_id size $size updated to $new_stock.</p><br>";
    // } else {
    //     echo "<p>Error updating stock for product $product_id size $size: " . mysqli_error($conn) . "</p><br>";
    // }
}
// Clear the shopping cart after order is placed
$clear_cart_sql = "DELETE FROM 024_shopping_cart WHERE customer_id = $customer_id";
if (mysqli_query($conn, $clear_cart_sql)) {
    $session_role = $_SESSION['role'] ?? null;
    $session_customer_id = $_SESSION['customer_id'] ?? '';
    $session_username = $_SESSION['username'] ?? '';
    if ($session_role === 'admin') {
        write_logJSON("Order placed by customer " . $session_customer_id . " " . $session_username, "insert", "order", "changes_log.json");
    }
   
    $user_email = $_SESSION['email'] ?? ($_SESSION['customer_email'] ?? '');
    $user_name = $_SESSION['username'] ?? ($_SESSION['user'] ?? '');
    $sql_order_number = "SELECT order_id FROM 024_orders_table WHERE order_number = $last_order_id LIMIT 1";
    $order_number_res = mysqli_query($conn, $sql_order_number);
    $order_number = mysqli_fetch_assoc($order_number_res);
    if (!empty($user_email)) {
        send_email("$user_email", "$user_name", 'Order Confirmation - Your Order Has Been Placed', '<h1>Thank you for your order!</h1><p>Your order has been placed successfully and is being processed. We will notify you once it is shipped.</p><br><p>Order ID: ' . $order_number['order_id'] . '</p>', 'Your order has been placed successfully and is being processed. We will notify you once it is shipped. Order ID: ' . $order_number['order_id']);
    }
    if (json_last_error() === JSON_ERROR_NONE) {
    if (isset($response_data['status']) && $response_data['status'] === 'success') {
        header("Location: /student024/Shop/backend/views/my_orders.php?success=Order+placed+successfully");
        exit();
    } else {
        $error_message = isset($response_data['message']) ? $response_data['message'] : 'Unknown error';
        header("Location: /student024/Shop/backend/views/my_orders.php?error=" . urlencode($error_message));
        exit();
    }

    header("Location: /student024/Shop/backend/views/my_orders.php?error=Invalid+response+from+supplier");
    exit();
}
    header("Location: /student024/Shop/backend/views/my_orders.php?message=Order+placed+successfully");
} else {
    header("Location: /student024/Shop/backend/views/my_orders.php?error=Failed+to+clear+shopping+cart");
}
exit();




?>
