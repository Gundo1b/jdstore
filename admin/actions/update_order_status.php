<?php
require_once '../../config/db.php';

// In a real app, you'd check for admin login status here
// session_start();
// if(!isset($_SESSION['admin_id'])) { die('Unauthorized'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'], $_POST['order_status'])) {
        $order_id = intval($_POST['order_id']);
        $order_status = $_POST['order_status'];

        // Optional: Validate that the status is one of the allowed values
        $allowed_statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
        if (!in_array($order_status, $allowed_statuses)) {
            die('Invalid status value.');
        }

        // Prepare and execute the update statement
        $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->bind_param("si", $order_status, $order_id);

        if ($stmt->execute()) {
            // Redirect back to the details page with a success message
            header("Location: ../order_details.php?id=" . $order_id . "&status=updated");
            exit();
        } else {
            die("Error: Could not update order status. " . $stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();

// Redirect back if accessed directly or without POST data
header("Location: ../manage_orders.php");
exit();
?>
