<?php 
// Establecer headers CORS ANTES de cualquier otra operación
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Manejar peticiones preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require $_SERVER["DOCUMENT_ROOT"] ."/student024/Shop/backend/config/db_connect_switch.php";

try {
    $sql = "SELECT product_id, name, price, description, image_url FROM `024_products`";
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Error en consulta de productos: ' . mysqli_error($conn));
    }

    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            'product_id' => intval($row['product_id']),
            'name' => $row['name'],
            'price' => number_format(floatval($row['price']), 2, '.', ''),
            'description' => $row['description'],
            'image_url' => json_decode($row['image_url'], true)[0] ?? null,
        ];
    }

    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar productos: ' . $e->getMessage()]);
}