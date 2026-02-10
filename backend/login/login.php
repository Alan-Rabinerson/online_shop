<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php'; 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/functions/write_log.php';

    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    $errores = ['usuario' => '', 'login' => '', 'contrasena' => ''];
    if (isset($_POST['submit'])) {
        // Ensure session started (avoid calling session_abort which may not exist on some PHP versions)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // sanitize inputs
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if (isset($conn) && $conn) {
            $username_esc = mysqli_real_escape_string($conn, $username);
            $password_esc = mysqli_real_escape_string($conn, $password);
        } else {
            $username_esc = $username;
            $password_esc = $password;
        }

        // Table name starts with digits — quote it with backticks
        $sql = "SELECT * FROM `024_customers` WHERE username = '$username_esc' AND `password` = '$password_esc'";
        $result = mysqli_query($conn, $sql);

        $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/', $username)) {
            $errores['usuario'] = "Por favor, inserte un nombre de usuario válido.";
        } else {
            $errores['usuario'] = '';
        }

        if (!preg_match('/^[a-zA-Z0-9_]{6,18}$/', $password)) {
            $errores['contrasena'] = "Por favor, inserte una contraseña válida.";
        } else {
            $errores['contrasena'] = '';
        }

        if (count($user) > 0 && count($user) === 1) {
            // Credenciales válidas, redirigir al backend
            $_SESSION['loggedin'] = true;
            $_SESSION['customer_id'] = $user[0]['customer_id'];
            $_SESSION['username'] = $user[0]['username'];
            $_SESSION['email'] = $user[0]['email'];
            
            if ($user[0]['type'] == 'admin') {
                $_SESSION['role'] = 'admin';
                setcookie('user', json_encode($user[0]), time() + (3600), "/"); // guardamos la cookie por 1 horas (28000 segundos)
                header("Location: /student024/Shop/backend/index.php");
                write_log("User number" . $user[0]['customer_id']. " " . $user[0]['username']." logged in", "connection_log.txt");
                exit;
            } else {
                $_SESSION['role'] = 'user';
                setcookie('user', json_encode($user[0]), time() + (3600), "/"); // guardamos la cookie por 1 horas (28000 segundos)
                write_log("User number" . $user[0]['customer_id']. " " . $user[0]['username']." logged in", "connection_log.txt");
                header("Location: /student024/Shop/index.html");
            }
            
        } else {
            // Credenciales inválidas, mostrar mensaje de error
            $errores['login'] = 'Usuario o contraseña inválidos. Por favor, inténtelo de nuevo.';
            write_log("Failed login attempt for username " . $username, "connection_log.txt");
            exit;
        }
    }
?>
<main>
    <h1>Login</h1>
    <form  action="/student024/Shop/backend/login/login.php" method="POST">
        <div>
            <label for="username">Username:</label>
            <input class="border border-gray-300 p-2 rounded" type="text" id="username" name="username" required>
            <?php echo '<div class="text-red-500">'.$errores['usuario'].'</div>'; ?>

        </div>
        <div>
            <label for="password">Password:</label>
            <input class="border border-gray-300 p-2 rounded" type="password" id="password" name="password" required>
            <?php echo '<div class="text-red-500">'.$errores['contrasena'].'</div>'; ?>
        </div>
        <?php echo '<div class="text-red-500">'.$errores['login'].'</div>'; ?>
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" type="submit" name="submit" >Login</button>
    </form>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php'; ?>
