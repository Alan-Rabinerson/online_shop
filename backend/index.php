<?php include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/header.php'; ?>
<main class="bg-azul-oscuro h-100vh  text-white p-6 flex flex-col items-center">
    <h2 class="mt-4">Página Principal</h2>
    <p class="mt-2">Este es el contenido principal de la página.</p>

    <nav class="mt-4">
        <h3>Products</h3>
        <ul class="flex space-x-2 list-none p-0 m-0">
            <li class="boton-rojo"><a class="text-beige hover:underline"
                    href="/student024/Shop/backend/views/products.php"> View Product</a></li>
        </ul>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
            <h3 class="mt-4">Customers</h3>
            <ul class="flex space-x-2 list-none p-0 m-0">
                <li class="boton-rojo"><a class="text-beige hover:underline"
                        href="/student024/Shop/backend/views/customers.php"> View Customer</a></li>
            </ul>
            <h3 class="mt-4">My Account</h3>
            <ul class="flex space-x-2 list-none p-0 m-0">
                <li class="boton-rojo"><a class="text-beige hover:underline"
                        href="/student024/Shop/backend/views/my_account.php"> View My Account</a></li>
            </ul>
            <h3 class="mt-4">Review Management</h3>
            <ul class="flex space-x-2 list-none p-0 m-0">
                <li class="boton-rojo"><a class="text-beige hover:underline"
                        href="/student024/Shop/backend/views/reviews.php"> View Reviews</a></li>
            </ul>
            <h3 class="mt-4">Supplier Dashboard</h3>
            <ul class="flex space-x-2 list-none p-0 m-0">
                <li class="boton-rojo"><a class="text-beige hover:underline"
                        href="/student024/Shop/backend/views/supplier_dashboard.php"> Supplier Dashboard</a></li>
            </ul>
        <?php } else { ?>
            <h3 class="mt-4">My Account</h3>
            <ul class="flex space-x-2 list-none p-0 m-0">
                <li class="boton-rojo"><a class="text-beige hover:underline"
                        href="/student024/Shop/backend/views/my_account.php"> View My Account</a></li>
            </ul>
        <?php } ?>
        <h3 class="mt-4">Orders</h3>
        <ul class="flex space-x-2 list-none p-0 m-0">
            <li class="boton-rojo"><a class="text-beige hover:underline"
                    href="/student024/Shop/backend/views/orders.php">View Orders</a></li>

        </ul>
        <h3 class="mt-4">Shopping Cart</h3>
        <ul class="flex space-x-2 list-none p-0 m-0">
            <li class="boton-rojo"><a class="text-beige hover:underline"
                    href="/student024/Shop/backend/views/shopping_cart.php">View Shopping Cart</a></li>
        </ul>
        <h3 class="mt-4">sandbox</h3>
        <ul class="flex space-x-2 list-none p-0 m-0">
            <li class="boton-rojo"><button id="WeatherButton">Weather Button</button></li>
        </ul>
    </nav>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/footer.php'; ?>