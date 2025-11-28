<?php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                // Store an error message in the session
                $_SESSION['error'] = "You need to be logged in to add products to the cart.";
                // Redirect to login page
                header('Location: ../login.php');
                exit();
            }

            // Add an item to the cart
            if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);

                if ($quantity > 0) {
                    // If product is already in cart, update quantity
                    if (isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id] += $quantity;
                    } else {
                        // Otherwise, add new product
                        $_SESSION['cart'][$product_id] = $quantity;
                    }
                }
            }
            // Redirect back to the previous page or a specific page
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '../index.php');
            exit();

        case 'update':
            // Update item quantity in the cart
            if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                $product_id = intval($_POST['product_id']);
                $quantity = intval($_POST['quantity']);

                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id] = $quantity;
                } else {
                    // Remove item if quantity is 0 or less
                    unset($_SESSION['cart'][$product_id]);
                }
            }
            header('Location: ../cart.php');
            exit();

        case 'remove':
            // Remove an item from the cart
            if (isset($_GET['product_id'])) {
                $product_id = intval($_GET['product_id']);
                unset($_SESSION['cart'][$product_id]);
            }
            header('Location: ../cart.php');
            exit();

        case 'clear':
            // Clear the entire cart
            $_SESSION['cart'] = [];
            header('Location: ../cart.php');
            exit();
    }
}
?>