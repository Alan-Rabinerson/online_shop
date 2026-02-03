<?php 
    require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php';
    require $_SERVER['DOCUMENT_ROOT'].'';
?>
<main>
    <form action="/student024/Shop/backend/db/payment_methods/db_method_insert.php" method="POST">
        <label for="method_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Method Name</label>
        <input type="text" name="method_name" id="method_name" class='form-control'  required>
        <label for="number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Number</label>
        <input type="text" name="number" id="number" class='form-control'  required>
        <label for="expiration_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Expiration Date</label>
        <input type="text" name="expiration_date" id="expiration_date" class='form-control'  required>
        <label for="security_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Security Code</label>
        <input type="text" name="security_code" id="security_code" class='form-control'  required>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save New Payment Method</button>
    </form>
</main>
<?php require $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php'; ?>