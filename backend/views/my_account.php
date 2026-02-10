<?php include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/config/db_connect_switch.php'; ?>
<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/read_customer_data.php'; ?>


<main class="bg-azul-oscuro min-h-screen text-beige p-6">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-semibold mb-6">Mi cuenta</h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <aside class="md:col-span-1 bg-azul-claro p-4 rounded">
                <nav>
                    <ul class="space-y-2 text-sm">
                        <li><a class="block px-3 py-2 rounded hover:bg-azul-oscuro" href="#account-summary">Resumen</a>
                        </li>
                        <li><a class="block px-3 py-2 rounded hover:bg-azul-oscuro"
                                href="/student024/Shop/backend/views/my_orders.php">Mis pedidos</a></li>
                        <li><a class="block px-3 py-2 rounded hover:bg-azul-oscuro" href="#addresses">Direcciones</a>
                        </li>
                        <li><a class="block px-3 py-2 rounded hover:bg-azul-oscuro" href="#payment-methods">Métodos de
                                pago</a></li>
                        <li><a class="block px-3 py-2 rounded hover:bg-azul-oscuro"
                                href="/student024/Shop/backend/login/change_password.php">Cambiar contraseña</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- Content -->
            <section class="md:col-span-3 bg-azul-oscuro p-6 rounded border border-azul-claro">
                <!-- Account summary -->
                <div id="account-summary" class="mb-8">
                    <h2 class="text-2xl font-medium mb-3">Resumen de cuenta</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                        <div class="p-4 bg-azul-claro rounded">
                            <p class="text-sm text-beige opacity-80">Nombre</p>
                            <p class="font-semibold text-white">
                                <?php echo htmlspecialchars($full_name ?? ($_SESSION['username'] ?? 'Usuario'), ENT_QUOTES); ?>
                            </p>
                        </div>
                        <div class="p-4 bg-azul-claro rounded">
                            <p class="text-sm text-beige opacity-80">Email</p>
                            <p class="font-semibold text-white">
                                <?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?></p>
                        </div>
                        <div class="p-4 bg-azul-claro rounded">
                            <p class="text-sm text-beige opacity-80">Teléfono</p>
                            <p class="font-semibold text-white">
                                <?php echo htmlspecialchars($phone ?? '', ENT_QUOTES); ?></p>
                        </div>
                        <div class="p-4 bg-azul-claro rounded">
                            <p class="text-sm text-beige opacity-80">Pedidos</p>
                            <p class="font-semibold text-white"><a class="underline"
                                    href="/student024/Shop/backend/views/my_orders.php">Ver mis pedidos</a></p>
                        </div>
                    </div>
                    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/forms/customers/form_customer_update_call.php'; ?>
                </div>

                <!-- Addresses -->
                <div id="addresses" class="mb-8">
                    <h3 class="text-xl font-medium mb-2">Direcciones</h3>
                    <?php if (!empty($addresses)) { ?>
                        <ul class="space-y-2">
                            <?php foreach ($addresses as $addr) { ?>
                                <li class="p-3 bg-azul-claro rounded flex flex-col md:flex-row md:justify-between">

                                    <?php echo htmlspecialchars($addr['address_name'] ?? '', ENT_QUOTES); ?> :
                                    <?php echo htmlspecialchars(($addr['street'] ?? '') . ', ' . ($addr['city'] ?? '') . ' ' . ($addr['zip_code'] ?? ''), ENT_QUOTES); ?>
                                    <div class="mt-2 md:mt-0 flex gap-2 items-center">
                                        <form action="/student024/Shop/backend/db/addresses/db_address_delete.php" method="POST"
                                            class="inline">
                                            <input type="hidden" name="address_id" value="<?php echo $addr['address_id']; ?>">
                                            <button type="submit" class="boton-rojo">Eliminar dirección</button>
                                        </form>
                                        <form action="/student024/Shop/backend/forms/addresses/form_address_update.php"
                                            method="POST" class="flex items-center ">
                                            <input type="hidden" name="address_id" value="<?php echo $addr['address_id']; ?>">
                                            <button id="manage-addresses-btn" class="boton-rojo bg-azul-oscuro"
                                                type="submit">Actualizar direccion</button>
                                        </form>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p class="text-beige opacity-80">No hay direcciones guardadas.</p>
                    <?php } ?>
                    <button type="button"
                        onClick="window.location.href='/student024/Shop/backend/forms/addresses/form_address_insert.php'"
                        id="add-address-btn"
                        class="mt-4 px-4 py-2 bg-green-600 rounded text-white hover:bg-green-700">Agregar nueva
                        dirección</button>

                </div>

                <!-- Payment methods -->
                <div id="payment-methods" class="mb-8">
                    <h3 class="text-xl font-medium mb-2">Métodos de pago</h3>
                    <button
                        onClick="window.location.href='/student024/Shop/backend/forms/payment_methods/form_payment_method_insert.php'"
                        id="add-payment-method-btn"
                        class="mb-4 px-4 py-2 bg-green-600 rounded text-white hover:bg-green-700">Añadir nuevo método de
                        pago</button>
                    <?php if (!empty($payment_methods)) { ?>
                        <ul class="space-y-2">
                            <?php foreach ($payment_methods as $payment_method) {
                                $method_id = $payment_method['method_id'];
                                ?>
                                <li class="p-3 bg-azul-claro rounded flex flex-col md:flex-row md:justify-between">
                                    <?php echo htmlspecialchars($payment_method['method_name'] ?? 'Método', ENT_QUOTES); ?>
                                    <div class="mt-2 md:mt-0 flex gap-2 items-center">
                                        <?php
                                        include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/forms/payment_methods/form_method_update_call.php';
                                        ?>
                                        <form action="/student024/Shop/backend/db/payment_methods/db_payment_method_delete.php"
                                            method="POST" class="inline">
                                            <input type="hidden" name="method_id" value="<?php echo $method_id; ?>">
                                            <button type="submit" class="boton-rojo">Eliminar método de pago</button>
                                        </form>
                                    </div>
                                </li>

                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p class="text-beige opacity-80">No hay métodos de pago registrados.</p>
                    <?php } ?>
                </div>


            </section>
        </div>
    </div>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/footer.php'; ?>