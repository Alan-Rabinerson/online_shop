<?php
try {
    if ($_SERVER['DOCUMENT_ROOT'] === 'localhost'  || $_SERVER['DOCUMENT_ROOT'] === '') {
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/html';
    }
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';

    header('Content-Type: application/json ; charset=utf-8');
    session_start();

    if (!$conn) {
        throw new Exception('Conexión a la base de datos rechazada: ' . mysqli_connect_error());
    }
    if (isset($_POST['guest_id'])) {
        $guest_id_raw = trim($_POST['guest_id']);
    } else if (isset($_GET['guest_id'])) {
        $guest_id_raw = trim($_GET['guest_id']);
    } else {
        throw new Exception('guest_id no proporcionado en POST o GET');
    }
    // Validar que guest_id sea numérico y no esté vacío
    if ($guest_id_raw === '' || !ctype_digit($guest_id_raw)) {
        throw new Exception('guest_id inválido o no proporcionado: ' . $guest_id_raw);
    }

    $first_name = $conn->real_escape_string('guest');
    $last_name = $conn->real_escape_string($guest_id_raw);
    $email = $conn->real_escape_string($guest_id_raw . '@guest.com');
    $username = $conn->real_escape_string('guest_' . $guest_id_raw);
    $password = $conn->real_escape_string('');
    $phone = $conn->real_escape_string('123456789');
    $type = $conn->real_escape_string('customer');

    // No insertar customer_id explícitamente, dejar que AUTO_INCREMENT lo genere
    $sql = "INSERT INTO `024_customers` (`first_name`, `last_name`, `email`, `username`, `password`, `phone`, `birth_date`, `type`) VALUES (".
            "'" . $first_name . "', '" . $last_name . "', '" . $email . "', '" . $username . "', '" . $password . "', '" . $phone . "', CURDATE(), '" . $type . "')";

    if ($conn->query($sql) === TRUE) {
        $customer_id = $conn->insert_id; // Obtener el ID generado
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Guest created successfully.',
            'customer_id' => $customer_id
        ]);
    } else {
        throw new Exception('Error insertando invitado: ' . $conn->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error creando invitado: ' . $e->getMessage()
    ]);
    exit;
}
?>