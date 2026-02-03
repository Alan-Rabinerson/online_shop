<?php include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/header.php'; 
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/config/db_connect_switch.php';
    // Fetch all reviews ordered by creation date
    $sql = "SELECT * FROM 024_reviews ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<main class="w-screen text-center flex flex-col items-center">
    <h1 class="text-2xl font-bold mb-4">Reviews Management</h1>
    <?php
        ;
    ?>
    <div class="overflow-x-auto w-11/12">
        <form action="/student024/Shop/backend/db/reviews/update_review_status.php" method="POST" class="mb-4">
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="boton-rojo w-fit">Approve All Reviews</button>
        </form>
        <table class="table-auto w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Customer ID</th>
                    <th class="border border-gray-300 px-4 py-2">Product ID</th>
                    <th class="border border-gray-300 px-4 py-2">Order Number</th>
                    <th class="border border-gray-300 px-4 py-2">Review Content</th>
                    <th class="border border-gray-300 px-4 py-2">Review Rating</th>
                    <th class="border border-gray-300 px-4 py-2">Approved</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review){?>
                    <tr>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($review['customer_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($review['product_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($review['order_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($review['review_content'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($review['review_rating'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($review['status'], ENT_QUOTES, 'UTF-8'); 

                        if ($review['status'] == 'PENDING') {
                        ?>
                            
                            <form action="/student024/Shop/backend/db/reviews/update_review_status.php" method="POST">
                                <input type="hidden" name="status" value="APPROVED">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($review['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($review['customer_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="order_number" value="<?php echo htmlspecialchars($review['order_number'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button class="boton-rojo bg-green-500" type="submit">Approve</button>
                            </form>
                            <form action="/student024/Shop/backend/db/reviews/update_review_status.php" method="POST">
                                <input type="hidden" name="status" value="REJECTED">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($review['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($review['customer_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="order_number" value="<?php echo htmlspecialchars($review['order_number'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button class="boton-rojo" type="submit">Reject</button>
                            </form>
                        </td>
                        <?php } ?>
                    </tr><?php
                } ?>
            </tbody>
        </table>
    </div>
</main>
<?php
    include $_SERVER['DOCUMENT_ROOT'].'/student024/Shop/backend/includes/footer.php';
?>