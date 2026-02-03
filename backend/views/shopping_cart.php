<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';
?>
<main class="bg-azul-oscuro min-h-screen p-6 text-beige">
    <h2 class="text-2xl font-bold mb-4 text-center">Shopping Cart</h2>
    <div class="text-center">
    
        <div class="flex flex-wrap items-center justify-center gap-4">
            <?php
                include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/db/shopping_cart/db_shopping_cart_list.php';
            ?>
        </div>
            
        <h3>Total Items:
            <?php echo $total_items; ?>
        </h3>
        <h3 id="cart-total">Cart Total:
            <?php echo number_format($cart_total, 2). "€"; ?>
        </h3>
        <form action="/student024/Shop/backend/views/checkout.php" method="post">
            <input type="hidden" name="total_items" value="<?php echo $total_items; ?>">
            <input type="hidden" name="cart_total" value="<?php echo $cart_total; ?>">
            <input type="hidden" id="hidden-cart-data" name="cart_data" value='<?php echo json_encode($cart_items); ?>'>
            <input type="submit" class="boton-rojo rounded-4xl mb-4" value="Proceed to Checkout">
        </form>
    </div>
    <script src="/student024/Shop/JavaScript/shopping_cart.js"></script>
<?php
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';
?>
