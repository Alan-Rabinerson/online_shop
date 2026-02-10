<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';

if (!isset($_SESSION['customer_id'])) {
    echo "<p>Your shopping cart is empty.</p>";
    return;
}

$customer_id = intval($_SESSION['customer_id']);
$sql = "SELECT * FROM `024_shopping_cart` WHERE customer_id = $customer_id";
$result = mysqli_query($conn, $sql);
$cart_items = [];
if ($result) {
    $cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (!is_array($cart_items)) {
        $cart_items = [];
    }
}

$total_items = count($cart_items);
$cart_details = [];
$cart_total = 0;
$products = [];

if ($total_items > 0) {
    foreach ($cart_items as $item) {
        $product_id = isset($item['product_id']) ? intval($item['product_id']) : 0;
        if (!$product_id) {
            continue;
        }

        $sql = "SELECT * FROM `024_products` WHERE product_id = $product_id";
        $product_result = mysqli_query($conn, $sql);
        $product = $product_result ? mysqli_fetch_assoc($product_result) : null;

        $item_name = $product['name'] ?? ($item['name'] ?? 'Unknown Product');
        $item_price = isset($product['price']) ? floatval($product['price']) : (isset($item['price']) ? floatval($item['price']) : 0);

        $item['name'] = $item_name;
        $item['price'] = $item_price;
        $cart_details[] = $item;

        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
        $products[] = ['product_id' => $product_id, 'quantity' => $quantity, 'price' => $item_price];

        $item_total = $item_price * $quantity;
        $cart_total += $item_total;

        // use size in container id to support multiple sizes for same product
        $size_safe = htmlspecialchars($item['size'] ?? '');
        $container_id = 'product-' . $product_id . '-' . $size_safe;
        echo "<div class='product-card w-fit h-fit' id='" . $container_id . "' >";

        $supplier_id = $product['supplier_id'] ?? 0;
        if ($supplier_id == 1) {
            echo "<img src='/student024/Shop/assets/imagenes/foto" . $product_id . ".jpg' alt='" . htmlspecialchars($item_name) . "' class='w-48 h-48 object-cover mb-2 rounded-lg shadow-md'>";
        } else {
            $image_src = '/student024/Shop/assets/imagenes/foto' . $product_id . '.jpg';
            if (!empty($product['image_url'])) {
                $decoded = json_decode($product['image_url'], true);
                if (is_string($decoded)) {
                    $image_src = $decoded;
                } elseif (is_array($decoded)) {
                    $first = reset($decoded);
                    if (is_string($first)) {
                        $image_src = $first;
                    }
                }
            }
            echo "<img src='" . htmlspecialchars($image_src) . "' alt='" . htmlspecialchars($item_name) . "' class='w-48 h-48 object-cover mb-2 rounded-lg shadow-md'>";
        }

        echo "<h3 id='product-name-" . $product_id . "'>" . htmlspecialchars($item_name)  ."</h3>";
        echo "<p id='product-price-" . $product_id . "'>Price: " . htmlspecialchars(number_format($item_price, 2)) . "€ </p>";
        echo "<p>Size: " . $size_safe . "</p>";
        echo "<p id='subtotal-" . $product_id . "-" . $size_safe . "' >Subtotal: " . htmlspecialchars(number_format($item_total, 2)) . "€ </p>";
        echo "<span class='flex items-center gap-2'>";
        ?>
        <button onclick="removeQuantity(<?php echo $product_id; ?>, '<?php echo addslashes($item['size'] ?? ''); ?>', <?php echo $quantity; ?>, <?php echo $item_price; ?>)"  class="boton-rojo rounded-4xl">-</button>
        <?php echo "<p id='product-quantity-" . $product_id . "-" . $size_safe . "'>Quantity: " . $quantity . "</p>";?>
        <button onclick="addQuantity(<?php echo $product_id; ?>, '<?php echo addslashes($item['size'] ?? ''); ?>', <?php echo $quantity; ?>, <?php echo $item_price; ?>)" class="boton-rojo rounded-4xl">+</button>
        <?php
        echo "</span>";
        echo "</div><hr>";
    }
} else {
    echo "<p>Your shopping cart is empty.</p>";
}

?>