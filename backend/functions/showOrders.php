<?php

function showOrders($order_group)
{
    require_once $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
    // nothing to show
    if (empty($order_group))
        return;

    // Normalize input: accept either a grouped array (list of rows)
    // or a single associative array representing one row.
    if (is_array($order_group) && count($order_group) > 0 && is_array(reset($order_group))) {
        // $order_group is a group of rows (even if keys are 1,2,3 or random)
        $items = array_values($order_group);
        $first = $items[0];
    } else {
        // single-row associative array
        $items = [$order_group];
        $first = $order_group;
    }

    // Use safe defaults and escape output to avoid warnings and XSS
    $order_id = $first['order_id'];
    $first_name = $first['first_name'];
    $last_name = $first['last_name'];
    $order_date = $first['order_date'];
    $status = $first['status'];
    $method_name = $first['payment_method'];
    $address = $first['delivery_address'];
    $image_url = json_decode($first['image_url'] ?? '');
    // Display order information
    echo "<div class='order-card'>";
    echo "<h3>Order ID: " . $order_id . "</h3>";
    echo "<p>Customer Name: " . $first_name . " " . $last_name . "</p>";
    echo "<p>Order Date: " . $order_date . "</p>";
    echo "<p>Status: " . $status . "</p>";
    echo "<p>Method: " . $method_name . "</p>";
    echo "<p>Address: " . $address . "</p>";
    echo "<button class='px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700' onclick=\"location.href='/student024/Shop/backend/includes/buyAgain.php?order_id=" . $order_id . "'\">Buy Order Again</button>";
    foreach ($items as $product) {
        $order_number = $product['order_number'];
        $product_id = $product['product_id'];
        echo "<div class='order-product'>";
        echo "<div id='nombre-producto'>";
        echo "<h3> " . $product['product_name'] . "</h3>";
        echo "</div>";
        echo "<div id='detalles-producto' class='flex items-center justify-center gap-4'>";
        if ($first['supplier_id'] == 1) {
            echo "<img src='/student024/Shop/assets/imagenes/foto" . htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') . ".jpg' alt='" . htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') . "' class='w-32 h-32 object-cover mb-2'></img>";
        } else {
            // For products from other suppliers, you may want to display a placeholder image or skip the image altogether
            echo "<img src='" . htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8') . "' alt='Product Image Not Available' class='w-32 h-32 object-cover mb-2'></img>";
        }
        echo "<p>Quantity: " . $product['quantity'] . "</p>";
        echo "<p>Price: " . $product['price'] . "€</p>";

        echo "<span class='flex items-center justify-center gap-2'>";
        if (!empty($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/forms/orders/form_order_update_call.php';
            include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/forms/orders/form_order_delete.php';
        }
        include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/can_review.php';
        echo "<button class='px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700' onclick=\"location.href='/student024/Shop/backend/includes/buyAgain.php?product_id=" . $product_id . "&size=" . $product['size'] . "'\">Buy Product Again</button>";
        echo "</span>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
}
?>