<?php
    $apiKey = '10203040B';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://remotehost.es/student022/backend/apis/sellers/api_endpoint_send_products.php?apikey=" . $apiKey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $result = curl_exec($ch);
    curl_close($ch);
    echo $result;
?>
