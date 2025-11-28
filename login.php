<?php
require_once 'templates/header.php';

// If user is already logged in, redirect them to the homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<div class="main-content">
    <h1>Login to your Account</h1>

    <?php 
    if (isset($_SESSION['error'])) {
        echo '<div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px;">' . htmlspecialchars($_SESSION['error']) . '</div>';
        // Unset the error message after displaying it
        unset($_SESSION['error']);
    }
    ?>

    <?php if(isset($_GET['error'])): ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <form action="actions/auth_actions.php?action=login" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p style="margin-top: 20px;">Don't have an account? <a href="register.php">Register here</a>.</p>
</div>

<?php
require_once 'templates/footer.php';
?>