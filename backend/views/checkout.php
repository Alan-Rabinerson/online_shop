<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php'; 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/read_customer_data.php';

?>
<main class="container p-6">
    <h2 class="text-2xl font-bold mb-4">Checkout</h2>

    <div class="flex flex-col lg:flex-row lg:align-between gap-8 w-full">
        <!-- Left: Shipping & Payment Form -->
        <section class="checkout-form-section lg:w-2/3">
            <h3 id="checkout-form-title" class="text-xl font-semibold mb-3">Shipping & Payment</h3>
            <form id="checkout-form" method="POST" action="/student024/Shop/backend/db/orders/db_order_insert.php">
                <?php if (!empty($customer_id)): ?>
                    <input type="hidden" name="customer_id" value="<?php echo (int)$customer_id; ?>">
                <?php endif; ?>

                <div class="mb-4">
                    <h3 class="font-medium">Contact</h3>
                    <label class="block mt-2">Full name
                        <input name="full_name" type="text"  class="w-full p-2 mt-1 border rounded" placeholder="Nombre completo" value="<?php echo htmlspecialchars($full_name ?? '', ENT_QUOTES); ?>" required>
                    </label>
                    <label class="block mt-2">Email
                        <input name="email" type="email"  class="w-full p-2 mt-1 border rounded" placeholder="tu@correo.com" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>" required>
                    </label>
                    <label class="block mt-2">Phone
                        <input name="phone" type="tel" class="w-full p-2 mt-1 border rounded" placeholder="+34 600 000 000" value="<?php echo htmlspecialchars($phone ?? '', ENT_QUOTES); ?>">
                    </label>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium">Shipping address</h3>

                    <?php if (!empty($addresses)): ?>
                        <?php foreach ($addresses as $i => $addr): ?>
                            <?php
                                $addr_id = isset($addr['address_id']) ? (int)$addr['address_id'] : ($i + 1);
                                $street = htmlspecialchars($addr['street'] ?? $addr['address'] ?? '', ENT_QUOTES);
                                $city = htmlspecialchars($addr['city'] ?? '', ENT_QUOTES);
                                $postal = htmlspecialchars($addr['postal_code'] ?? $addr['zip_code'] ?? '', ENT_QUOTES);
                                $prov = htmlspecialchars($addr['province'] ?? '', ENT_QUOTES);
                                $label = htmlspecialchars($addr['address_name'] ?? ($street . ' ' . $city . ' ' . $postal), ENT_QUOTES);
                            ?>
                            <label class="block mt-2">
                                <input type="radio"
                                       name="selected_address"
                                       value="<?php echo $addr_id; ?>"
                                       data-street="<?php echo $street; ?>"
                                       data-city="<?php echo $city; ?>"
                                       data-postal="<?php echo $postal; ?>"
                                       data-province="<?php echo $prov; ?>"
                                       class="mr-2"
                                       <?php if ($i === 0) echo 'checked'; ?>/>
                                <?php echo $label; ?>
                            </label>
                        <?php endforeach; ?>

                        <label class="block mt-2">
                            <input type="radio" name="selected_address" value="new" class="mr-2" />
                            Use a new address
                        </label>
                    <?php endif; ?>
                    <input type="hidden" name="selected_address_id" id="selected_address_id" value="<?php echo htmlspecialchars($addr_id ?? '', ENT_QUOTES); ?>">        
                    <label class="block mt-2">Address
                        <input name="address" type="text" required class="w-full p-2 mt-1 border rounded" placeholder="Calle, número, piso" value="<?php echo htmlspecialchars($street ?? $street ?? $full_name ?? '', ENT_QUOTES); ?>">
                    </label>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <label>City
                            <input name="city" type="text" class="w-full p-2 mt-1 border rounded" placeholder="Ciudad" value="<?php echo htmlspecialchars($city ?? '', ENT_QUOTES); ?>">
                        </label>
                        <label>Postal code
                            <input name="postal_code" type="text" class="w-full p-2 mt-1 border rounded" placeholder="Código postal" value="<?php echo htmlspecialchars($postal ?? $zip_code ?? '', ENT_QUOTES); ?>">
                        </label>
                    </div>
                    <label class="block mt-2">Province
                        <input name="province" type="text" class="w-full p-2 mt-1 border rounded" placeholder="España" value="<?php echo htmlspecialchars($prov ?? $province ?? '', ENT_QUOTES); ?>">
                    </label>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium">Payment method</h3>
                    <?php
                        // If $payment_methods already provided, prefer it; otherwise try to load from DB
                        if (empty($payment_methods)) {
                            $payment_methods = [];
                            $res = mysqli_query($conn, "SELECT PM.* FROM 024_payment_method PM JOIN 024_payment_customer PC ON PM.method_id = PC.method_id WHERE PC.customer_id = " . (int)$customer_id);
                            if ($res) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $payment_methods[] = $row;
                                }
                            }
                        }

                        if (!empty($payment_methods)) {
                            foreach ($payment_methods as $method) {
                                $method_id = htmlspecialchars($method['method_id'] ?? $method['id'] ?? '', ENT_QUOTES);
                                $method_name = htmlspecialchars($method['method_name'] ?? $method['name'] ?? '', ENT_QUOTES);
                                echo "<label class='block mt-2'>";
                                echo "<input type='radio' name='payment_method' value='{$method_id}' required class='mr-2'/>";
                                echo $method_name; 
                                echo "</label>";
                            }
                        } else {
                            // Fallback single input if no methods found
                            echo "<label class='block mt-2'> <input type='radio' name='payment_method' value='manual' required class='mr-2' checked/> Pay by card / manual </label>";
                        }
                    ?>
                </div>
                    <!-- Hidden inputs to pass cart data -->
                <input type="hidden" name="cart_data" id="cart_data" value='<?php echo $_POST['cart_data'] ?? []; ?>'>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Place order</button>
            </form>
        </section>

        <!-- Right: Order summary -->
        <aside class="order-summary p-6  rounded ">
            <h3 id="order-summary-title" class="text-xl font-semibold mb-3">Order summary</h3>
            <div id="order-summary" class="p-4 border rounded flex flex-wrap gap-4">
                <?php
                    
                    require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/db/shopping_cart/db_shopping_cart_list.php';
                
                ?>
            </div>

            <div class="mt-4">
                <p class="font-medium">Shipping: <span id="shipping-amount">Free</span></p>
                <p class="font-bold text-lg">Total: <span id="order-total"><?php echo number_format($cart_total, 2) . "€"; ?></span></p>
            </div>
        </aside>
    </div>
</main>
<script src="/student024/Shop/JavaScript/checkout.js"></script>
<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php'; ?>