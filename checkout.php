<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// --- Security: User must be logged in ---
if (!isset($_SESSION['user_id'])) {
    // Store the intended destination and redirect to login
    $_SESSION['redirect_url'] = 'checkout.php';
    header("Location: login.php");
    exit();
}

// --- Security: Cart cannot be empty ---
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$cart_items = [];
$total_price = 0;

// Fetch user's address
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
$address = $address_result->fetch_assoc();
$stmt->close();


// Fetch product details for items in cart
$product_ids = array_keys($cart);
$ids_string = implode(',', $product_ids);
$sql = "SELECT id, name, price FROM products WHERE id IN ($ids_string)";
$product_result = $conn->query($sql);

if ($product_result && $product_result->num_rows > 0) {
    while ($row = $product_result->fetch_assoc()) {
        $quantity = $cart[$row['id']];
        $subtotal = $quantity * $row['price'];
        $row['quantity'] = $quantity;
        $cart_items[] = $row;
        $total_price += $subtotal;
    }
} else {
    // Something is wrong, cart has items but we can't find them in DB. Clear cart.
    $_SESSION['cart'] = [];
    header("Location: cart.php?error=invalid_items");
    exit();
}

?>

<div class="main-content">
    <h1>Checkout</h1>
    <div style="display: flex; gap: 40px;">
        <!-- Shipping Information -->
        <div style="flex: 1;">
            <h2>Shipping Address</h2>
            <?php if ($address): ?>
                <p>
                    <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong><br>
                    <?php echo htmlspecialchars($address['address_line1']); ?><br>
                    <?php if (!empty($address['address_line2'])) echo htmlspecialchars($address['address_line2']) . '<br>'; ?>
                    <?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> <?php echo htmlspecialchars($address['zip_code']); ?><br>
                    <?php echo htmlspecialchars($address['country']); ?><br>
                    Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
                </p>
                <p><a href="edit_address.php">(Edit Address)</a></p>
            <?php else: ?>
                <p style="color: red;">No address found. Please <a href="edit_address.php">add an address</a>.</p>
            <?php endif; ?>
        </div>

        <!-- Order Summary -->
        <div style="flex: 2;">
            <h2>Order Summary</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>R<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;">
                        <td colspan="2" style="text-align: right;">Grand Total:</td>
                        <td>R<?php echo number_format($total_price, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <form action="/actions/order_actions.php" method="POST" style="margin-top: 20px; text-align: right;">
                <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">
                <?php if ($address): // Only show button if an address exists ?>
                    <button type="submit" class="btn btn-primary">Place Order</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
