<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    $sql = "SELECT * FROM 024_order_view ORDER BY order_date DESC";
    $result = mysqli_query($conn, $sql);
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $order_id_list = [];
    foreach($orders as $order){
        $order_id_list [$order['order_id']][$order['product_id']] = $order;
    }
    foreach($order_id_list as $order_group ){
        showOrders($order_group);
    }
