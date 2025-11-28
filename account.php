<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// Fetch user's address
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
$address = $address_result->fetch_assoc();
$stmt->close();

?>

<div class="main-content">
    <h1>My Account</h1>
    <p>Welcome, <?php echo htmlspecialchars($user['name']); ?>! Here you can manage your account details and view your orders.</p>

    <div style="display: flex; gap: 40px;">
        <!-- Account Details -->
        <div style="flex: 1;">
            <h2>Account Details</h2>
            <p>
                <strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                <strong>Member Since:</strong> <?php echo date_format(date_create($user['created_at']), 'F j, Y'); ?>
            </p>

            <h2>Shipping Address</h2>
            <?php if ($address): ?>
                <p>
                    <?php echo htmlspecialchars($address['address_line1']); ?><br>
                    <?php if (!empty($address['address_line2'])) echo htmlspecialchars($address['address_line2']) . '<br>'; ?>
                    <?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> <?php echo htmlspecialchars($address['zip_code']); ?><br>
                    <?php echo htmlspecialchars($address['country']); ?><br>
                    <strong>Phone:</strong> <?php echo htmlspecialchars($address['phone_number']); ?>
                </p>
                <a href="edit_address.php" class="btn">Edit Address</a>
            <?php else: ?>
                <p>You have not set a shipping address yet.</p>
                <a href="edit_address.php" class="btn btn-primary">Add Address</a>
            <?php endif; ?>
        </div>

        <!-- Order History -->
        <div style="flex: 2;">
            <h2>My Orders</h2>
            <?php
            // Fetch user's orders
            $order_stmt = $conn->prepare("SELECT id, total_amount, order_status, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC");
            $order_stmt->bind_param("i", $user_id);
            $order_stmt->execute();
            $orders_result = $order_stmt->get_result();
            ?>
            <?php if ($orders_result->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date_format(date_create($order['order_date']), 'Y-m-d'); ?></td>
                                <td>R<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td><a href="view_order.php?id=<?php echo $order['id']; ?>">View Details</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have not placed any orders yet.</p>
            <?php endif; $order_stmt->close(); ?>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>
