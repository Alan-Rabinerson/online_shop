<?php
    $total_items = $_POST['total_items'] ?? 0;
    $cart_total = $_POST['cart_total'] ?? 0.0;
    $cart_data = $_POST['cart_data'] ?? '[]';
    $cart_items = json_decode($cart_data);
    $customer_id = $_SESSION['customer_id'] ?? null;

    // Inicializar valores por defecto
    $full_name = '';
    $email = '';
    $phone = '';
    $street = '';
    $city = '';
    $zip_code = '';
    $province = '';
    $payment_methods = [];
    $password_hash = '';
    $first_name = '';
    $last_name = '';

    if ($customer_id) {
        // recoger todos los registros relacionados (direcciones / métodos de pago)
        $sql = "SELECT * FROM `024_customers_view` WHERE customer_id = " . (int)$customer_id;
        $result = mysqli_query($conn, $sql);

        // Colecciones para admitir múltiples direcciones y métodos de pago
        $addresses = [];
        $payment_methods = []; // re-inicializamos aquí por seguridad            
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        ?> <script>// DEBUG console.log("Rows found: + <?php // echo count($rows); ?> ");</script>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            
            foreach ($rows as $row) {
                // Datos básicos (tomar del primer registro disponible)
                if (empty($full_name)) {
                    $full_name = trim((($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')));
                    $first_name = $row['first_name'] ?? '';
                    $last_name = $row['last_name'] ?? '';
                }
                if (empty($email)) {
                    $email = $row['email'] ?? '';
                }
                if (empty($phone)) {
                    $phone = $row['phone'] ?? '';
                }
                if (empty($password_hash)) {
                    $password_hash = hash('sha256', $row['password']);
                }
                // Normalizar/extraer dirección
                $addr = [
                    'address_id' => $row['address_id'] ?? null,
                    'address_name' => $row['address_name'] ?? '',
                    'street'     => $row['street'] ?? ($row['address'] ?? ''),
                    'city'       => $row['city'] ?? '',
                    'zip_code'   => ($row['zip_code'] ?? ''),
                    'province'   => $row['province'] ?? '',
                    'label'      => $row['address_label'] ?? ''
                ];
                 // Generar clave única para evitar duplicados (preferir address_id si existe)
                $addr_key ='addr_' . $addr['address_id'];
                if (!isset($addresses[$addr_key])) {
                    $addresses[$addr_key] = $addr;
                }

                // Recoger método(s) de pago (si hay datos en la fila)
                if (!empty($row['method_id']) || !empty($row['method_name']) || !empty($row['card_number']) || !empty($row['account_number'])) {
                    $method = [
                        'method_id'       => $row['method_id'] ?? null,
                        'method_name'     => $row['method_name'] ?? '',
                        'number'          => $row['number'] ?? '',
                        'expiration_date' => $row['expiration_date'] ?? '',
                        'security_code'   => $row['security_code'] ?? ''
                    ];

                    if (!empty($method['method_id'])) {
                        // usar method_id como clave si está presente (sobrescribe/actualiza ese método)
                        $payment_methods['m_' . $method['method_id']] = $method;
                    } 
                }
            }
            

            // Re-indexar como arrays numéricos para uso en frontend
            $payment_methods = array_values($payment_methods);
            $addresses = array_values($addresses);

            // Compatibilidad hacia atrás: rellenar fields simples con la primera dirección si existen
            if (!empty($addresses[0])) {
                $street = $addresses[0]['street'];
                $city = $addresses[0]['city'];
                $zip_code = $addresses[0]['zip_code'];
                $province = $addresses[0]['province'];
            }
        } else { // si no hay filas (el cliente no ha agregado datos aún), obtener datos básicos del cliente
                $sql = "SELECT * FROM `024_customers` WHERE customer_id = " . (int)$customer_id;
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $full_name = trim((($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')));
                    $email = $row['email'] ?? '';
                    $phone = $row['phone'] ?? '';
                    $password_hash = hash('sha256', $row['password']);
                    $addresses = [];
                    $payment_methods = [];
                }
        }
    } 
    
