<?php
try {
    session_start();

    require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';

    header('Content-Type: application/json');

    if (!$conn) {
        throw new Exception('Conexión a la base de datos rechazada: ' . mysqli_connect_error());
    }

    $product_id = isset($_GET['productId']) ? intval($_GET['productId']) : 0;
    if ($product_id <= 0) {
        throw new Exception('ID de producto inválido: ' . (isset($_GET['productId']) ? $_GET['productId'] : 'no proporcionado'));
    }

    $sql = "SELECT p.product_id, p.name, p.price, p.long_description
            FROM `024_products` p
            WHERE p.product_id = $product_id
            LIMIT 1";

    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception('Error consultando producto (ID: ' . $product_id . '): ' . mysqli_error($conn));
    }

    $product = null;
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // available sizes with stock
        $sizes_sql = "SELECT size, stock FROM `024_product_sizes` WHERE product_id = $product_id";
        $sizes_result = mysqli_query($conn, $sizes_sql);
        if (!$sizes_result) {
            throw new Exception('Error consultando tallas: ' . mysqli_error($conn));
        }
        
        $available_sizes = [];
        $total_stock = 0;
        while ($size_row = mysqli_fetch_assoc($sizes_result)) {
            $size = $size_row['size'];
            $stock = isset($size_row['stock']) ? (int)$size_row['stock'] : 0;
            $available_sizes[] = ['size' => $size, 'stock' => $stock];
            $total_stock += $stock;
        }

        // product materials
        $materials_sql = "SELECT material_name, percentage FROM `024_product_materials` WHERE product_id = $product_id";
        $materials_result = mysqli_query($conn, $materials_sql);
        if (!$materials_result) {
            throw new Exception('Error consultando materiales: ' . mysqli_error($conn));
        }
        
        $materials = [];
        while ($material_row = mysqli_fetch_assoc($materials_result)) {
            $materials[] = [
                'material_name' => $material_row['material_name'],
                'percentage' => $material_row['percentage']
            ];
        }

        $product = [
            'product_id' => intval($row['product_id']),
            'product_name' => $row['name'],
            'price' => floatval($row['price']),
            'description' => $row['long_description'],
            'sizes' => $available_sizes,
            'total_stock' => $total_stock,
            'materials' => $materials
        ];
    } else {
        throw new Exception('Producto no encontrado (ID: ' . $product_id . ')');
    }

    echo json_encode($product);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar detalle del producto: ' . $e->getMessage()]);
    exit;
}
?>
