<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';
?>

    <h1>Manage Orders</h1>
    <p>This page lists all customer orders.</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total Amount</th>
                <th>Order Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Join with users table to get customer name
            $sql = "SELECT o.id, u.name AS customer_name, o.total_amount, o.order_status, o.order_date 
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    ORDER BY o.order_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td>R<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                        <td><?php echo date_format(date_create($row['order_date']), 'Y-m-d H:i'); ?></td>
                        <td>
                            <a href="order_details.php?id=<?php echo $row['id']; ?>">View Details</a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php
$conn->close();
require_once 'templates/admin_footer.php';
?>