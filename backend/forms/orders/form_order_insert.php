<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';  
    ;

?>
<main>
    <h1>Insertar nuevo pedido</h1>
    <form action="/student024/Shop/backend/db/db_order_insert.php" method="GET">
        <label for="customer_id">ID Cliente:</label>
        <input type="number" id="customer_id" name="customer_id" required><br><br>

        <label for="product_id">ID producto:</label>
        <input type="number" id="product_id" name="product_id" required><br><br>

        <label for="quantity">Cantidad:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <label for="address_id">Dirección de Envío:</label>
        <input type="number" id="address_id" name="address_id" required><br><br>

        <label for="method_id">Metodo de pago:</label>
        <input type="number" id="method_id" name="method_id" required><br><br>

        <input type="submit" value="Insertar Pedido">
    </form>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';  ?>