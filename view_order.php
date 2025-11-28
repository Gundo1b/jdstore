<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if an order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='main-content'><h1>Invalid Order ID</h1></div>";
    require_once 'templates/footer.php';
    exit();
}

$order_id = intval($_GET['id']);

// --- Fetch Order and Address details ---
// IMPORTANT: Add `o.user_id = ?` to the WHERE clause to ensure users can only see their own orders.
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id, o.total_amount, o.order_status, o.order_date,
        a.address_line1, a.address_line2, a.city, a.state, a.zip_code, a.country, a.phone_number
    FROM orders o
    LEFT JOIN addresses a ON o.user_id = a.user_id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<div class='main-content'><h1>Order Not Found</h1><p>Either the order does not exist or you do not have permission to view it.</p></div>";
    require_once 'templates/footer.php';
    exit();
}

// --- Fetch Order Items ---
$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price_per_item, p.name AS product_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
?>

<div class="main-content">
    <h1>Details for Order #<?php echo $order['order_id']; ?></h1>
    <p><strong>Status:</strong> <span style="font-weight: bold; color: #007bff;"><?php echo htmlspecialchars($order['order_status']); ?></span></p>

    <div style="display: flex; gap: 40px;">
        <!-- Shipping Info -->
        <div style="flex: 1;">
            <h2>Shipped To</h2>
            <p>
                <?php echo htmlspecialchars($order['address_line1']); ?><br>
                <?php if (!empty($order['address_line2'])) echo htmlspecialchars($order['address_line2']) . '<br>'; ?>
                <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['zip_code']); ?><br>
                <?php echo htmlspecialchars($order['country']); ?>
            </p>
        </div>

        <!-- Items in Order -->
        <div style="flex: 2;">
            <h2>Items in this Order</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price per Item</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>R<?php echo number_format($item['price_per_item'], 2); ?></td>
                        <td>R<?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;">
                        <td colspan="3" style="text-align: right;">Grand Total:</td>
                        <td>R<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <a href="account.php" class="btn">Back to My Account</a>
    </div>
</div>

<?php
$stmt->close();
require_once 'templates/footer.php';
?>
