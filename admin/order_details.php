<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';

// Check if an order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h1>Invalid Order ID</h1>";
    require_once 'templates/admin_footer.php';
    exit();
}

$order_id = intval($_GET['id']);

// --- Fetch Order, User, and Address details ---
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id, o.total_amount, o.order_status, o.order_date,
        u.id AS user_id, u.name, u.email,
        a.address_line1, a.address_line2, a.city, a.state, a.zip_code, a.country, a.phone_number
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN addresses a ON u.id = a.user_id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<h1>Order Not Found</h1>";
    require_once 'templates/admin_footer.php';
    exit();
}

// --- Fetch Order Items ---
$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price_per_item, p.name AS product_name, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
?>

    <h1>Order Details #<?php echo $order['order_id']; ?></h1>

    <div style="display: flex; gap: 40px;">
        <!-- Customer & Order Info -->
        <div style="flex: 1;">
            <h2>Customer Information</h2>
            <p>
                <strong>Name:</strong> <?php echo htmlspecialchars($order['name']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
                <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone_number']); ?>
            </p>
            
            <h2>Shipping Address</h2>
            <p>
                <?php echo htmlspecialchars($order['address_line1']); ?><br>
                <?php if (!empty($order['address_line2'])) echo htmlspecialchars($order['address_line2']) . '<br>'; ?>
                <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?><br>
                <?php echo htmlspecialchars($order['country']); ?>
            </p>

            <h2>Order Status</h2>
            <p><strong>Current Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>

            <?php if(isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
                <p style="color: green;">Order status updated successfully!</p>
            <?php endif; ?>

            <form action="actions/update_order_status.php" method="POST">
                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                <div class="form-group">
                    <label for="order_status"><strong>Update Status</strong></label>
                    <select name="order_status" id="order_status" class="form-control">
                        <option value="Pending" <?php if($order['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Shipped" <?php if($order['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                        <option value="Delivered" <?php if($order['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                        <option value="Cancelled" <?php if($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Status</button>
            </form>
        </div>

        <!-- Items in Order -->
        <div style="flex: 2;">
            <h2>Items in this Order</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price per Item</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: auto;"></td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>R<?php echo number_format($item['price_per_item'], 2); ?></td>
                        <td>R<?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;">
                        <td colspan="4" style="text-align: right;">Grand Total:</td>
                        <td>R<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="manage_orders.php" class="btn">Back to All Orders</a>
    </div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/admin_footer.php';
?>
