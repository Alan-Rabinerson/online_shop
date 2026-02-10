<?php 
    header('Content-Type: application/json; charset=UTF-8');
    require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/endpoints/guests/create_guest.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['api_key'] === $api_key_shift_and_go || $_GET['api_key'] === $api_key_teamwear)) {
        correctKey();
    } else {
        wrongKey();
    }
    
    function create_guest_and_get_customer_id() {
        global $conn;
        
        // Generar un guest_id único
        $guest_id = uniqid('guest_', true);
        $guest_id = str_replace('.', '_', $guest_id); // Reemplazar puntos para que sea válido
        
        // Crear datos del cliente guest
        $first_name = $_GET['customer_forename'] ?? $guest_id; // Usar guest_id como nombre si no se proporciona
        $last_name = $_GET['customer_surname'] ?? $guest_id; // Usar guest_id como apellido si no se proporciona
        $email = $_GET['customer_email'] ?? $guest_id . '@example.com'; // Usar guest_id para generar un email si no se proporciona
        $username = $conn->real_escape_string($guest_id);
        $password = $conn->real_escape_string('');
        $phone = $_GET['customer_phone'] ?? $conn->real_escape_string('123456789');
        $address = $_GET['customer_address'] . $_GET['customer_location'] . $_GET['customer_zip'] . $_GET['customer_country'] ?? $conn->real_escape_string('Unknown');
        $type = $conn->real_escape_string('customer');
        
        // Insertar el cliente guest en la base de datos
        $sql = "INSERT INTO `024_customers` (`first_name`, `last_name`, `email`, `username`, `password`, `phone`, `birth_date`, `type`) VALUES ("."'" . $first_name . "', '" . $last_name . "', '" . $email . "', '" . $username . "', '" . $password . "', '" . $phone . "', CURDATE(), '" . $type . "')";
        
        if ($conn->query($sql) === TRUE) {
            return $conn->insert_id; // Retornar el customer_id generado
        } else {
            throw new Exception('Error creando guest: ' . $conn->error);
        }
    }
    
    function correctKey() {
        global $conn;
        $order_date = date('Y-m-d H:i:s');
        $api_key_shift_and_go = '85c712e7-6a84-4a5a-87a3-47b25df6771b';
        $api_key_teamwear = 'e888b918-330e-43c5-a103-111d57a4a28f'; 
        $api_key_brand2 = 'ba6e471d-3721-4959-afba-d2f55d021b9f';
        // Crear el guest y obtener su customer_id
        $customer_id = create_guest_and_get_customer_id();
        $sql = "INSERT INTO 024_address (`street`, `city`, `postal_code`, `province`) VALUES ( '" .mysqli_real_escape_string($conn, $_GET['customer_address']) . "', '" .mysqli_real_escape_string($conn, $_GET['customer_location']) . "', '" .mysqli_real_escape_string($conn, $_GET['customer_zip']) . "', '" .mysqli_real_escape_string($conn, $_GET['customer_country']) . "')";
        
        $product_id = $_GET['product_id'];
        $quantity = $_GET['quantity'];
        $order_date = date('Y-m-d H:i:s');
        if ($_GET['api_key'] === $api_key_shift_and_go) {
            $supplier_id = 3; // Shift&Go
        } else if ($_GET['api_key'] === $api_key_teamwear) {
            $supplier_id = 2; // Teamwear
        } else if ($_GET['api_key'] === $api_key_brand2) {
            $supplier_id = 4; // Brand2
        }

        $sql = "INSERT INTO 024_orders (product_id, quantity, order_date, supplier_id, customer_id) VALUES (" .intval($product_id) . ", " .intval($quantity) . ", '" .mysqli_real_escape_string($conn, $order_date) . "', " .intval($supplier_id) . ", " .intval($customer_id) . ")";
            
        if (!mysqli_query($conn, $sql)) {
            throw new Exception('Error insertando orden: ' . mysqli_error($conn));
        }
        
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Ordenes insertadas exitosamente',
            'customer_id' => $customer_id
        ]);
    }
    
    function wrongKey() {
        http_response_code(403);
        echo json_encode(array("message" => "Forbidden: Invalid or missing API Key"));
    }