<?php
require_once 'templates/header.php';

// Ensure an order ID is provided
if (!isset($_GET['order_id'])) {
    echo "<div class='main-content'><h1>Error</h1><p>No order specified.</p></div>";
    require_once 'templates/footer.php';
    exit();
}

$order_id = intval($_GET['order_id']);

?>

<div class="main-content">
    <h1>Thank You for Your Order!</h1>
    <p>Your order has been placed successfully.</p>
    <p>Your Order ID is: <strong><?php echo $order_id; ?></strong></p>
    <p>We will process it shortly. You can check the status of your order in your account dashboard. for more info about your order call us</p>
    <p><a href="index.php" class="btn btn-primary">Continue Shopping</a></p>
</div>

<?php
require_once 'templates/footer.php';
?>
