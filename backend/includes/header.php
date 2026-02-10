<?php
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Quick debug responder: if __debug=1 provided, return JSON diagnostics and exit
    if ((isset($_GET['__debug']) && $_GET['__debug'] == '1') || (isset($_POST['__debug']) && $_POST['__debug'] == '1')) {
        $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $localConfig = $docRoot . '/student024/Shop/backend/config/db_connect.php';
        $remoteConfig = $docRoot . '/student024/Shop/backend/config/db_connect_remotehost.php';
        $diag = [
            'script_name' => $script_name ?? ($_SERVER['SCRIPT_NAME'] ?? ''),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'host' => $_SERVER['HTTP_HOST'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'session_keys' => isset($_SESSION) ? array_keys($_SESSION) : [],
            'session_sample' => isset($_SESSION) ? array_intersect_key($_SESSION, array_flip(array_slice(array_keys($_SESSION),0,10))) : [],
            'local_config_exists' => file_exists($localConfig),
            'remote_config_exists' => file_exists($remoteConfig),
            'local_config_readable' => is_readable($localConfig),
            'remote_config_readable' => is_readable($remoteConfig),
        ];
        if (file_exists($remoteConfig) && is_readable($remoteConfig)) {
            $diag['remote_config_head'] = substr(file_get_contents($remoteConfig), 0, 400);
        }
        header('Content-Type: application/json');
        echo json_encode($diag, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }


    // Determine requested path. Prefer SCRIPT_NAME to avoid rewrite/query issues.
    $script_name = strtolower($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = $script_name;

    // Debug logging: record host, REQUEST_URI, SCRIPT_NAME and computed path to PHP error log
    if (isset($_SERVER['HTTP_HOST'])) {
        error_log("DEBUG header.php host=" . $_SERVER['HTTP_HOST'] . " request_uri=" . $request_uri . " script_name=" . $script_name . " path=" . $path);
    }


    // Polyfill for str_contains on older PHP versions
    if (!function_exists('str_contains')) {
        function str_contains(string $haystack, string $needle): bool
        {
            return strpos($haystack, $needle) !== false;
        }
    }

    // // Restore session from cookie if available (do this before redirect check)
    // if (isset($_COOKIE['user']) && !isset($_SESSION['username']) && $path !== '/student024/Shop/backend/login/logout.php' && $path !== '/student024/Shop/backend/login/login.php') {
    //     $userData = json_decode($_COOKIE['user'], true);
    //     if ($userData && isset($userData['username']) && isset($userData['role'])) {
    //         $_SESSION['username'] = $userData['username'];
    //         $_SESSION['role'] = $userData['role'];
    //         error_log("DEBUG header.php: restored session from cookie for user=" . $_SESSION['username']);
    //     }
    // }

    // Whitelist of public paths (no login required)
    // Recognize common public pages by filename or by the login folder
    $is_public = (
        str_contains($path, '/login.php') ||
        str_contains($path, '/register.php') ||
        str_contains($path, '/logout.php') ||
        str_contains($path, '/backend/login/') ||
        str_contains($path, '/login/')
    );

    if (!isset($_SESSION['username']) && !$is_public) {
        header('Location: /student024/Shop/backend/login/login.php');
        exit;
    }

    // whitelist of admin-only paths
    $admin_paths = [
        '/student024/Shop/backend/views/customers.php',
        '/student024/Shop/backend/views/reviews.php',
        '/student024/Shop/backend/views/orders.php',
        '/student024/Shop/backend/forms/products/form_product_insert.php',
        '/student024/Shop/backend/forms/products/form_product_delete.php',
        '/student024/Shop/backend/forms/products/form_product_update.php',
        '/student024/Shop/backend/forms/orders/form_order_insert.php',
        '/student024/Shop/backend/forms/orders/form_order_update.php',
        '/student024/Shop/backend/forms/shopping_cart/form_shopping_cart_insert.php',
        '/student024/Shop/backend/forms/shopping_cart/form_shopping_cart_update.php',
    ];
    if (in_array($path, $admin_paths) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
        // User is not admin, redirect to login page or show error
        header('Location: /student024/Shop/backend/index.php');
        exit;
    }
    // Include helper to display success / error messages in pages
    require $_SERVER['DOCUMENT_ROOT'] . '/student024/Shop/backend/includes/show_success_error_msg.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mi Proyecto</title>
        <link rel="stylesheet" href="/student024/Shop/styles/css/output.css">
        <script src="/student024/Shop/JavaScript/weather.js"></script>
    </head>
    <body class="bg-azul-oscuro text-beige">
        <header class="header">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="/student024/Shop/assets/logos/logo_sin_fondo_rectangular.png" alt="Logo" class="h-auto max-h-24 object-contain"/>
                </div>
                <nav class="flex items-center justify center">
                    <ul class="flex space-x-2 list-none p-0 m-0">
                        <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/index.php">Home</a></li>
                        <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/products.php">Products</a></li>
                        
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { // if user is admin show the option to manage customers and reviews ?>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/customers.php">Customers</a></li>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/my_account.php">My Account</a></li>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/reviews.php">Review Management</a></li>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/orders.php">Orders</a></li>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/stats.php">Stats</a></li>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/supplier_dashboard.php">Supplier Dashboard</a></li>
                        <?php } else { // if user is not admin show only my account ?>
                            <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/my_account.php">My Account</a></li>

                        <?php } ?>
                        <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/my_orders.php">My Orders</a></li>
                        <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/backend/views/shopping_cart.php">Shopping Cart</a></li>
                        <li class="px-3 py-1 bg-azul-claro border border-gray-200 rounded"><a class="text-beige hover:underline" href="/student024/Shop/index.html">Homepage</a></li>
                    </ul>
                    <div class="ml-4">
                        <?php if (isset($_SESSION['username'])){ // if user is logged in show avatar, name and logout button ?>
                            <span class="mr-2 text-white">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                            <img src="/student024/Shop/assets/avatars/avatar.png" alt="Avatar" class="inline-block h-8 w-8 rounded-full mr-2 object-cover">
                            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="window.location.href='/student024/Shop/backend/login/logout.php';">Logout</button>
                        <?php } else { // if not logged in show login and register buttons ?>
                            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="window.location.href='/student024/Shop/backend/login/login.php'">Login</button>
                            <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="window.location.href='/student024/Shop/backend/views/register.php'">Register</button>
                        <?php } ?>
                    </div>
                </nav>
            </div>
            <style>
                header {
                    border-bottom: 2px solid #ccc;
                }

            </style>

        </header>
        




