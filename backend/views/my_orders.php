<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';
    require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/showOrders.php';
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/write_log.php';
    // DEBUG TEMPORAL: mostrar y loguear errores durante diagnóstico (quitar en producción)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    write_log("DEBUG checkout.php start - DOCUMENT_ROOT=" . ($_SERVER['DOCUMENT_ROOT'] ?? '') . " HTTP_HOST=" . ($_SERVER['HTTP_HOST'] ?? '' ), "error_log.txt");

    // Intentar incluir conexión y comprobar $conn
    require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    if (!isset($conn) || !$conn) {
        write_log("DEBUG checkout.php - DB connection not available. Conn var: " . var_export($conn ?? null, true), "error_log.txt");
        // Mostrar mensaje legible en HTML para el debug remoto
        http_response_code(500);
        echo "<h2>Debug: DB connection failed on remote host. Revisa los logs del servidor.</h2>";
        exit;
    } 
?>
<main class="min-h-screen p-4 flex flex-col items-center justify-start relative">
    <!-- Botón para abrir el popup de leyenda -->
    <button id="open-legend-btn" class="absolute top-4 right-4 px-4 py-2 bg-azul-claro text-white rounded hover:bg-blue-700 transition">
        Review Legend
    </button>
    
    <h1> Mis Pedidos</h1>
    <form  method="GET">
        <input type="text" name="order_id" placeholder="Nombre del producto" class="border-azul-claro border" onkeyup="showMyOrders(this.value)" required>
    </form>
    <div class="orders-container mt-4 h-fit w-full flex flex-col items-center justify-center gap-4 justify-self-center" id="orders-container">
        <?php 
            ;
            $orders_id_list = [];
            $customer_id = $_SESSION['customer_id'];
            require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
            $sql = "SELECT * FROM 024_order_view WHERE customer_id = $customer_id ORDER BY order_number DESC";
            $result = mysqli_query($conn, $sql);
            $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (count($orders) === 0) {
                echo "<p>You have no orders yet.</p>";
            } else {
                echo "<div class='flex flex-wrap items-center justify-center gap-4 mb-4'>";
                foreach($orders as $order){
                    $order_id = $order['order_id'];
                    $order_id_list[$order_id][$order['product_id']] = $order;
                    $order_number = $order['order_number'];
                }
                foreach($order_id_list as $order_id => $products){
                    showOrders($products);
                }
                echo "</div>";
            }
            
        ?>
    </div>
    
    <!-- Popup Modal de Leyenda -->
    <div id="legend-modal" class="fixed inset-0 bg-transparent hidden items-center justify-center z-50" style="display: none;">
        <div id="legend-content" class="bg-white border-2 border-azul-claro p-6 rounded-lg shadow-lg max-w-md w-full mx-4">
            <h2 class="text-xl font-bold mb-4 text-azul-oscuro">Review Legend</h2>
            <div class="flex flex-col gap-3">
                <p class="text-gray-700">Click on 'Review' to leave feedback for delivered products.</p>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-blue-600 text-white rounded">Review</span>
                    <span class="text-sm text-gray-600">Available to review</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 bg-gray-600 text-white rounded">Review</span>
                    <span class="text-sm text-gray-600">Already reviewed or not eligible</span>
                </div>
            </div>
            <button id="close-legend-btn" class="mt-4 w-full px-4 py-2 bg-azul-claro text-white rounded hover:bg-blue-700 transition">
                Close
            </button>
        </div>
    </div>
</main>
<script src="/student024/Shop/JavaScript/showorders.js"></script>
<script src="/student024/Shop/JavaScript/myOrders.js"></script>
<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';  ?>