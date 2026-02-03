<?php 
include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';
require $_SERVER['DOCUMENT_ROOT'].'';
if (isset($_SESSION['customer_id'])) {
    // User is already logged in, redirect to home page
    header('Location: /student024/Shop/backend/index.php');
    exit;
}
if (isset($_POST['register'])){

    // Retrieve and sanitize form inputs
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'customer'; // Default role for new registrations

    $sql = "INSERT INTO `024_customers` (`first_name`, `last_name`, `phone`, `birth_date`, `username`, `email`, `password`, `type`) VALUES ('$first_name', '$last_name', '$phone', '$birthdate', '$username', '$email', '" . password_hash($password, PASSWORD_DEFAULT) . "', '$role')";
    $query = mysqli_query($conn, $sql);
    if ($query) { // Registration successful redirect to login page with success message
        header('Location: /student024/Shop/backend/login/login.php?message='.urlencode("Registration successful. Please log in."));
        exit;
    } else { // Registration failed, redirect back to register with error message
        header('Location: /student024/Shop/backend/views/register.php?error='.urlencode("Registration failed. Please try again or contact the administrator."));
        exit;
    }
}
?>
<main class="bg-azul-oscuro h-100vh text-beige p-6 flex flex-col items-center">
<h1 class="text-2xl font-bold mb-4">Register</h1>
<form action="/student024/Shop/backend/views/register.php" method="POST" class="w-full max-w-md space-y-4" id="registerForm">
    <div class="mb-4" id="firstNameDiv">
        <label for="first_name" class="block mb-2">First Name:</label>
        <input type="text" id="first_name" name="first_name" class="form-control" required>
    </div>

    <div class="mb-4" id="lastNameDiv">
        <label for="last_name" class="block mb-2">Last Name:</label>
        <input type="text" id="last_name" name="last_name" class="form-control" required>
    </div>

    <div class="mb-4" id="usernameDiv">
        <label for="username" class="block mb-2">Username:</label>
        <input type="text" id="username" name="username" class="form-control" required>
    </div>

    <div class="mb-4" id="phoneDiv">
        <label for="phone" class="block mb-2">Phone number:</label>
        <input type="text" id="phone" name="phone" class="form-control" required>
    </div>

    <div class="mb-4" id="birthdateDiv">
        <label for="birthdate" class="block mb-2">Birthdate:</label>
        <input type="date" id="birthdate" name="birthdate" class="form-control" required>
    </div>

    <div class="mb-4" id="emailDiv">
        <label for="email" class="block mb-2">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <div class="mb-4" id="passwordDiv">
        <label for="password" class="block mb-2">Password:</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    <div class="mb-4" id="confirmPasswordDiv">
        <label for="confirm_password" class="block mb-2">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
    </div>

    <input type="hidden" name="register" value="1">
    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Register</button>
</form>
</main>
<script src="/student024/Shop/JavaScript/register.js"></script>

<?php require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';?>