<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';// Llama al script para obtener los productos
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/write_logJSON.php';

    // capturar datos del producto a actualizar
    $product_id = $_GET['product_id'];
    $product_name = $_GET['product_name'];
    $description = $_GET['description'];
    $price = $_GET['price'];
    $supplier_id = $_GET['supplier_id'];
    $tallas = isset($_GET['tallas']) ? $_GET['tallas'] : [];
    $tallas_stock = isset($_GET['tallas_stock']) ? $_GET['tallas_stock'] : [];
    // fetch product data from database based on product_id
    // prepare available_sizes CSV for SET column
    $available_sizes = '';
    if (!empty($tallas) && is_array($tallas)) {
        $available_sizes = implode(',', array_map(function($s){ return $s; }, $tallas));
    }

    $sql = "UPDATE 024_products SET name='$product_name', description='$description', price=$price, supplier_id='$supplier_id', available_sizes='$available_sizes' WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);

    // update product sizes table: remove existing sizes for product and insert the selected ones
    if ($result) {
        $del = "DELETE FROM 024_product_sizes WHERE product_id = $product_id";
        mysqli_query($conn, $del);
        if (!empty($tallas) && is_array($tallas)) {
            foreach ($tallas as $size) {
                $size_clean = $conn->real_escape_string($size);
                $stock_val = isset($tallas_stock[$size]) ? (int)$tallas_stock[$size] : $stock;
                $insert_size_sql = "INSERT INTO 024_product_sizes (product_id, size, stock) VALUES ($product_id, '$size_clean', $stock_val)";
                mysqli_query($conn, $insert_size_sql);
            }
        }
    }
 
    // send confirmation or error message
    if ( mysqli_query($conn, $sql) === TRUE) {
        if ($_SESSION['role'] == 'admin') {
            write_logJSON("Product with ID " . $product_id . " updated by customer " . $_SESSION['customer_id'] ." ". $_SESSION['username'], "update" ,"product", "changes_log.json");
        }
        header("Location: /student024/Shop/backend/views/products.php?message=".urlencode("Product $product_name updated successfully"));
        exit();
    } else {
        header("Location: /student024/Shop/backend/views/form_product_update.php?product_id=$product_id&error=".urlencode("Error updating product: " . mysqli_error($conn)));
        exit();;
    }

    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php'; 
?>