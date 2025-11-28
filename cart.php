<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// The cart is an array of [product_id => quantity]
$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total_price = 0;

if (!empty($cart)) {
    // Get product IDs from cart to fetch from DB
    $product_ids = array_keys($cart);
    $ids_string = implode(',', $product_ids);

    // Fetch product details for items in the cart
    $sql = "SELECT id, name, price, image FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $quantity = $cart[$row['id']];
            $subtotal = $quantity * $row['price'];
            $row['quantity'] = $quantity;
            $row['subtotal'] = $subtotal;
            $cart_items[] = $row;
            $total_price += $subtotal;
        }
    }
}
?>

<div class="main-content">
    <h1>Your Shopping Cart</h1>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="index.php">Continue shopping</a>.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: auto; margin-right: 10px;">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td>R<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form action="actions/cart_actions.php?action=update" method="POST" style="display: inline-flex; align-items: center;">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 70px;">
                                <button type="submit" class="btn" style="margin-left: 10px;">Update</button>
                            </form>
                        </td>
                        <td>R<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td>
                            <a href="actions/cart_actions.php?action=remove&product_id=<?php echo $item['id']; ?>" style="color: red;">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total:</td>
                    <td colspan="2" style="font-weight: bold;">R<?php echo number_format($total_price, 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <div style="margin-top: 20px; display: flex; justify-content: space-between;">
            <a href="actions/cart_actions.php?action=clear" class="btn btn-danger">Clear Cart</a>
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>

    <?php endif; ?>
</div>

<?php
require_once 'templates/footer.php';
?>
