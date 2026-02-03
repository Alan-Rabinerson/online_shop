<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php'; 
    ;// Muestra mensajes de éxito o error
?>
<main class="bg-azul-oscuro min-h-screen p-6 text-beige">
    <h1 class="text-2xl font-bold mb-4 text-center text-beige">Productos</h1>
    <p>Gestión de productos.</p>
    <?php if ($_SESSION['role'] === 'admin'){ ?>
    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" onclick="location.href='/student024/Shop/backend/forms/products/form_product_insert.php'">Añadir Producto</button>
    <form action="./products.php" method="GET">
        <input type="text" name="searchTerm" id="searchTerm" placeholder="Buscar producto..." class="px-2 py-1 border border-gray-300 rounded" onkeyup="showProducts(this.value)" />
    </form>
    <?php } ?>
    <div id="products" class="flex flex-wrap items-center justify-center gap-2">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/db/products/db_product_list.php'; ?>
    </div>
</main>
<script src="/student024/Shop/JavaScript/showproducts.js"></script>
<script src="/student024/Shop/JavaScript/insert_shopping_cart.js"></script>

<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';  ?>
