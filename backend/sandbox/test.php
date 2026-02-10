<?php 
    require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/header.php';
   // require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/read_customer_data.php';

    echo $_SESSION['username'];
    echo '<br><br>';
    /*$sql = 'SELECT pm.method_name FROM `024_payment_method` pm
            JOIN `024_payment_customer` pc ON pm.method_id = pc.method_id
            WHERE pc.customer_id = ' . (int)$_SESSION['customer_id'];*/
    $sql = 'SELECT * FROM `024_customers_view` WHERE customer_id = ' . (int)$_SESSION['customer_id'];
    $result = mysqli_query($conn, $sql);
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if ($rows)  {
        foreach($rows as $row){ 
            echo $row['first_name'] . ' ' . $row['last_name'] . ' - ' . $row['email'] . ' - ' . $row['phone'];
            echo '<br>';
            echo $row['customer_id'];
            echo '<br>';
            echo $row['address_name']. ' - ' . $row['street'] . ', ' . $row['city'] . ', ' . $row['province'] . ' ' . $row['zip_code'];
            echo '<br>';
            echo $row['method_name'] ;
            echo '<br>';
        };  
    } else{
        echo 'No se encontraron métodos de pago para este cliente.';

    }/*
    
    echo '<br><br>';
    if ($addresses)  {
        foreach($addresses as $address){ 
            echo $address['street'] . ', ' . $address['city'] . ', ' . $address['province'] . ' ' . $address['zip_code'];
            echo '<br>';
        };  
    } else{
        echo 'No se encontraron direcciones para este cliente.';
    }*/


    require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/footer.php';

        