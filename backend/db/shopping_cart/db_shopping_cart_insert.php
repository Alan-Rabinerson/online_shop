<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Buffer output so that unexpected HTML/warnings don't break JSON parsing on the client
ob_start();

try {
    // Usar conexión remota
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    if (!$conn) {
        throw new Exception('Conexión a la base de datos rechazada: ' . mysqli_connect_error());
    }

    // Resolver customer_id (sesión > POST > cookies > username)
    $customer_id = 0;
    if (isset($_SESSION['customer_id'])) {
        $customer_id = (int) $_SESSION['customer_id'];
    } elseif (isset($_POST['customer_id'])) {
        $customer_id = (int) $_POST['customer_id'];
    } elseif (isset($_COOKIE['customer_id'])) {
        $customer_id = (int) $_COOKIE['customer_id'];
    } elseif (isset($_COOKIE['guest_id'])) {
        $customer_id = (int) $_COOKIE['guest_id'];
    } elseif (isset($_COOKIE['username'])) {
        $username = mysqli_real_escape_string($conn, $_COOKIE['username']);
        $q = "SELECT customer_id FROM `024_customers` WHERE username='" . $username . "' LIMIT 1";
        $r = mysqli_query($conn, $q);
        if ($r && mysqli_num_rows($r) > 0) {
            $row = mysqli_fetch_assoc($r);
            $customer_id = (int) $row['customer_id'];
        }
    }

    if ($customer_id <= 0) {
        throw new Exception('Cliente no identificado (customer_id no disponible).');
    }

    // Validar parámetros de producto
    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $size = isset($_POST['size']) ? mysqli_real_escape_string($conn, $_POST['size']) : '';
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    if ($product_id <= 0) {
        throw new Exception('ID de producto inválido: ' . ($_POST['product_id'] ?? 'no proporcionado'));
    }
    if ($size === '') {
        throw new Exception('Talla no proporcionada para el producto ' . $product_id);
    }
    if ($quantity <= 0) {
        $quantity = 1; // normalizar
    }

    // Comprobar stock para la talla
    $stock_sql = "SELECT stock FROM `024_product_sizes` WHERE product_id=$product_id AND size='" . $size . "' LIMIT 1";
    $stock_res = mysqli_query($conn, $stock_sql);
    if (!$stock_res) {
        throw new Exception('Error comprobando stock: ' . mysqli_error($conn));
    }
    $stock_row = mysqli_fetch_assoc($stock_res);
    $available_stock = $stock_row ? (int) $stock_row['stock'] : 0;
    if ($available_stock <= 0) {
        throw new Exception('Sin stock para la talla ' . $size . ' del producto ' . $product_id);
    }

    // ¿Ya existe en carrito?
    $check_sql = "SELECT quantity FROM `024_shopping_cart` WHERE customer_id=$customer_id AND product_id=$product_id AND size='" . $size . "' LIMIT 1";
    $check_res = mysqli_query($conn, $check_sql);
    if ($check_res === false) {
        throw new Exception('Error consultando carrito: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($check_res) > 0) {
        $row = mysqli_fetch_assoc($check_res);
        $current_qty = isset($row['quantity']) ? (int) $row['quantity'] : 0;
        $new_quantity = $current_qty + $quantity;

        $upd_sql = "UPDATE `024_shopping_cart` SET quantity=$new_quantity WHERE customer_id=$customer_id AND product_id=$product_id AND size='" . $size . "'";
        $upd_res = mysqli_query($conn, $upd_sql);
        if ($upd_res === false) {
            throw new Exception('Error actualizando cantidad: ' . mysqli_error($conn));
        }
        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada a ' . $new_quantity, 'product_id' => $product_id, 'quantity' => $new_quantity]);
        exit;
    } else {
        $ins_sql = "INSERT INTO `024_shopping_cart` (customer_id, product_id, quantity, size) VALUES ($customer_id, $product_id, $quantity, '" . $size . "')";
        $ins_res = mysqli_query($conn, $ins_sql);
        if ($ins_res === false) {
            throw new Exception('Error insertando en carrito: ' . mysqli_error($conn));
        }
        if ($_SESSION['role'] == 'admin'){
            write_logJSON("Producto " . $product_id . " añadido al carrito del cliente " . $customer_id . " por el admin " . $_SESSION['username'], "insert" ,"shopping_cart", "changes_log.json");
        }
        echo json_encode(['success' => true, 'message' => 'Producto ' . $product_id . ' añadido al carrito', 'product_id' => $product_id, 'quantity' => $quantity]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    $extra = trim((string) ob_get_clean());
    $response = ['success' => false, 'error' => 'Error al añadir al carrito: ' . $e->getMessage()];
    if ($extra !== '') {
        // include extra output for debugging (server warnings/HTML). Keep it short.
        $response['debug_output'] = mb_substr(strip_tags($extra), 0, 1000);
    }
    echo json_encode($response);
    exit;
}
// Clean any buffered output on success
ob_end_clean();
?>

