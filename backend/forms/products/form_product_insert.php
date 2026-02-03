<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php'; 
    ;
?>
    <main class="container">
        <h2>Insertar Nuevo Producto</h2>
        <form method="GET" action="/student024/Shop/backend/db/products/db_product_insert.php">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="block w-full border border-gray-300 rounded px-3 py-2" id="product_name" name="product_name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="block w-full border border-gray-300 rounded px-3 py-2" id="description" name="description" required>
            </div>
            <div class="mb-3">
                <label for="long-description" class="form-label">Description</label>
                <input type="text" class="block w-full border border-gray-300 rounded px-3 py-2" id="long-description" name="long-description" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="block w-full border border-gray-300 rounded px-3 py-2" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="tallas" class="form-label">Sizes (Tallas)</label>
                <?php
                    $all_sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                    foreach ($all_sizes as $size) {
                        echo "<div class='inline-flex items-center mr-3 mb-2'>";
                        echo "<label class='inline-flex items-center mr-2'><input type='checkbox' name='tallas[]' value='$size' class='mr-1'/>$size</label> ";
                        echo "<label class='sr-only' for='stock_$size'>Stock $size</label>";
                        echo "<input type='number' min='0' name='tallas_stock[$size]' id='stock_$size' placeholder='stock' class='w-20 border rounded px-2 py-1'/>";
                        echo "</div>";
                    }
                ?>
            </div>
            <div class="mb-3">
                <label for="supplier" class="form-label">Supplier</label>
                <input type="text" class="block w-full border border-gray-300 rounded px-3 py-2" id="supplier" name="supplier" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <?php 
                    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
                    $sql = "SELECT * FROM 024_category";
                    $result = mysqli_query($conn, $sql);
                    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
                    foreach ($categories as $category) {
                        echo "<div class='flex gap-1 mr-4 mb-2'>";
                        echo "
                        <input type='checkbox' name='categories' class='mr-1 ' value='" . htmlspecialchars($category['category_id'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</input>";
                        echo "</div>";
                    }
                    
                ?>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Insertar Producto</button>
        </form>
    </main>
<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php'; ?>