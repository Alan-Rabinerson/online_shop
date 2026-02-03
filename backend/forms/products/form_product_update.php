<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';  ?>
<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';// Llama al script para obtener los productos
    ;
    // capture product_id from POST
    $product_id = $_POST['product_id'];
    // fetch product data from database based on product_id
    $sql = "SELECT * FROM 024_product_view WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $product_id = $products[0]['product_id'];
    $product_name = $products[0]['name'];
    $description = $products[0]['description'];
    $price = $products[0]['price'];
    $tallas = $products[0]['available_sizes'];
    // fetch existing size stocks for this product
    $sizeStocks = [];
    $sql = "SELECT size, stock FROM 024_product_sizes WHERE product_id = $product_id";
    $query = mysqli_query($conn, $sql);
    if ($query) {
        while ($result = mysqli_fetch_assoc($query)) {
            $sizeStocks[$result['size']] = (int)$result['stock'];
        }
    }
    $supplier = $products[0]['supplier'];
   

?>
        <main>
            <h2 class="mt-4">Update Product</h2>
            <form action="/student024/Shop/backend/db/products/db_product_update.php" method="GET" class="mt-3">
                <div class="mb-3">
                    <label for="product_id" class="form-label">Product ID to Update:</label>
                    <input type="number" class="form-control" id="product_id" name="product_id" readonly value="<?php echo $product_id; ?>" >
                </div>
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name:</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" required value="<?php echo $product_name; ?>">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <input type="text" class="form-control" id="description" name="description" required value="<?php echo $description; ?>">
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price:</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required value="<?php echo $price; ?>">
                </div>
                <div class="mb-3">
                    <label for="supplier" class="form-label">Supplier:</label>
                    <input type="text" class="form-control" id="supplier" name="supplier" required value="<?php echo $supplier; ?>">
                </div>
                <div class="mb-3">
                    <label for="tallas" class="form-label">Sizes (Tallas):</label>
                    <?php
                        $sizes = explode(",", $tallas);
                        $all_sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                        foreach ($all_sizes as $size) {
                            $checked = in_array($size, $sizes) ? 'checked' : '';
                            $stock_val = isset($sizeStocks[$size]) ? $sizeStocks[$size] : '';
                            echo "<div class='inline-flex items-center mr-3 mb-2'>";
                            echo "<input class='form-check-input mr-2' type='checkbox' id='size_$size' name='tallas[]' value='$size' $checked>";
                            echo "<label class='form-check-label mr-2' for='size_$size'>$size</label>";
                            echo "<input type='number' min='0' name='tallas_stock[$size]' value='$stock_val' placeholder='stock' class='w-20 border rounded px-2 py-1'/>";
                            echo "</div>";
                        }
                    ?>
                    
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category:</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="1">Football</option>
                        <option value="2">Basketball</option>
                        <option value="3">Tennis</option>
                        <option value="4">Clothing</option>
                        <option value="5">Footwear</option>
                        <option value="6">Outdoor</option>
                        <option value="7">Running</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 mt-4">Update Product</button>
            </form>
        </main>

<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';  ?>