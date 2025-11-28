<?php
session_start();
// It's not ideal to have DB logic in the header, but for this simple app, it's acceptable.
// For a larger app, this data would be loaded by a controller.
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDStore - Your one-stop shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Discount Banner -->
    <div style="background-color: #ff6b6b; color: white; text-align: center; padding: 10px; font-weight: bold;">
        🎉 Special Discount: Get 20% off on all books! Use code: BOOK20 🎉
    </div>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="index.php" class="brand">JDStore</a>
                <!-- <button class="nav-toggle" aria-label="toggle navigation">
                    <span class="hamburger"></span>
                </button> -->
                <div class="nav-links-container">
                    <ul class="nav-links">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span></li>
                            <li><a href="account.php">My Account</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <!-- <li><a href="login.php">Login</a></li> -->
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="header-actions">
                    <!-- Cart Icon -->
                    <a href="cart.php" class="cart-icon">
                        &#128722; Cart
                        (<span><?php
                            $cart_count = 0;
                            if (isset($_SESSION['cart'])) {
                                $cart_count = array_sum($_SESSION['cart']);
                            }
                            echo $cart_count;
                        ?></span>)
                    </a>

                    <!-- Search Bar -->
                    <div class="search-container">
                        <form action="search.php" method="GET">
                            <input type="text" placeholder="Search products..." name="query" required>
                            <button type="submit">Search</button>
                        </form>
                    </div>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- Quick Login Link -->
                    <a href="login.php" class="btn btn-primary" style="padding: 5px 10px;">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <main class="container">
<style>.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0;}</style>
<script src="assets/js/main.js"></script>
