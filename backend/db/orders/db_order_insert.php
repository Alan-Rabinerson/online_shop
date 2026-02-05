<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';   
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/write_logJSON.php';
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/mail/mail.php';
    $customer_id = $_SESSION['customer_id'];
    $payment_method = $_POST['payment_method'];
    $cart_data = json_decode($_POST['cart_data'], true);
    $selected_address_id = $_POST['selected_address_id'];
    if (!$cart_data || !is_array($cart_data) || count($cart_data) === 0) {
        $sql = "SELECT * FROM 024_shopping_cart WHERE customer_id = $customer_id";
        $result = mysqli_query($conn, $sql);
        $cart_data = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $cart_data[] = [
                    'product_id' => $row['product_id'],
                    'size'       => $row['size'],
                    'quantity'   => $row['quantity']
                ];
            }
        } else {
            header("Location: /student024/Shop/backend/views/my_orders.php?error=Shopping+cart+is+empty");
            exit();
        }
    }

    foreach ($cart_data as $item) {
        $product_id = (int)$item['product_id'];
        $size = $item['size'] ?? '';
        $quantity = (int)$item['quantity'];
        $price_sql = "SELECT price, supplier_id FROM 024_products WHERE product_id = $product_id LIMIT 1";
        $price_res = mysqli_query($conn, $price_sql);
        $price_row = $price_res ? mysqli_fetch_assoc($price_res) : null;
        $price = $price_row ? (float)$price_row['price'] : 0.0;
        $supplier_id = $price_row ? (int)$price_row['supplier_id'] : 1; // default to 1 if not found
        $total_price = $price * $quantity;
        $sql = "INSERT INTO `024_orders_table` (`customer_id`, `product_id`, `size`, `quantity`, `price`, `address_id`, `method_id`, `status`, `order_date`) VALUES ($customer_id, $product_id, '$size', $quantity, $total_price, $selected_address_id, $payment_method, 'PROCESSING', NOW())";
        if ($supplier_id != 1) {
            $sql = "INSERT INTO `024_orders_table` (`customer_id`, `product_id`, `size`, `quantity`, `price`, `address_id`, `method_id`, `status`, `order_date`, `supplier_id`) VALUES ($customer_id, $product_id, '$size', $quantity, $total_price, $selected_address_id, $payment_method, 'PROCESSING', NOW(), $supplier_id)";
        }
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            header("Location: /student024/Shop/backend/views/my_orders.php?error=Failed+to+place+order+for+product+$product_id");
            exit();
        }
        // if (!$query) { DEBUGGING 
        //     echo "Error inserting order: " . mysqli_error($conn);
        // } else {
        //    echo "<p>Order for product $product_id inserted successfully.</p><br>";
        // }
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
        if ($_SESSION['role'] == 'admin') {
            write_logJSON("Order placed by customer " . $_SESSION['customer_id'] ." ". $_SESSION['username'], "insert" ,"order", "changes_log.json");
        }
        header("Location: /student024/Shop/backend/views/my_orders.php?message=Order+placed+successfully");
        send_email($_SESSION['email'], $_SESSION['username'], 'Order Confirmation - Your Order Has Been Placed', '<h1>Thank you for your order!</h1><p>Your order has been placed successfully and is being processed. We will notify you once it is shipped.</p>', 'Your order has been placed successfully and is being processed. We will notify you once it is shipped.');
    } else {
        header("Location: /student024/Shop/backend/views/my_orders.php?error=Failed+to+clear+shopping+cart");
    }
    exit();
    
    

    
?>  
<?php // include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';  ?>