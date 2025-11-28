<?php
session_start();
require_once '../config/db.php';

// Security: User must be logged in and cart must not be empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $cart = $_SESSION['cart'];
    $total_amount = 0;
    
    // --- Server-side validation of total price ---
    // Fetch all product prices from the database in one query
    $product_ids = array_keys($cart);
    $ids_string = implode(',', array_map('intval', $product_ids));
    $sql = "SELECT id, price FROM products WHERE id IN ($ids_string)";
    $result = $conn->query($sql);
    
    $db_prices = [];
    while ($row = $result->fetch_assoc()) {
        $db_prices[$row['id']] = $row['price'];
    }

    // Recalculate total on the server to prevent manipulation
    foreach ($cart as $product_id => $quantity) {
        if (isset($db_prices[$product_id])) {
            $total_amount += $db_prices[$product_id] * $quantity;
        } else {
            // Product in cart doesn't exist in DB, major error
            die("An error occurred with your cart. Please try again.");
        }
    }
    // --- End validation ---

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Step 1: Insert into `orders` table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $total_amount);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the ID of the new order
        $stmt->close();

        // Step 2: Insert into `order_items` table
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        
        foreach ($cart as $product_id => $quantity) {
            $price_per_item = $db_prices[$product_id];
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price_per_item);
            $stmt->execute();
        }
        $stmt->close();

        // If all queries were successful, commit the transaction
        $conn->commit();

        // Step 3: Clear the shopping cart
        $_SESSION['cart'] = [];

        // Step 4: Redirect to a success page
        header("Location: ../order_success.php?order_id=" . $order_id);
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // Something went wrong, rollback the transaction
        die("There was an error placing your order. Please try again. " . $exception->getMessage());
    }

    $conn->close();

} else {
    // Redirect if accessed directly
    header("Location: ../index.php");
    exit();
}
?>
