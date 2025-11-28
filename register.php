<?php
require_once 'templates/header.php';

// If user is already logged in, redirect them to the homepage
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<div class="main-content">
    <h1>Create an Account</h1>
    <p>Sign up to be able to purchase items and save your delivery address.</p>

    <form action="actions/auth_actions.php?action=register" method="POST">
        <h2>Personal Information</h2>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" minlength="8" required>
        </div>
        
        <h2>Delivery Address</h2>
        <div class="form-group">
            <label for="address1">Address Line 1</label>
            <input type="text" id="address1" name="address_line1" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="address2">Address Line 2 (Optional)</label>
            <input type="text" id="address2" name="address_line2" class="form-control">
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="state">State / Province</label>
            <input type="text" id="state" name="state" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="zip">ZIP / Postal Code</label>
            <input type="text" id="zip" name="zip_code" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" id="country" name="country" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone_number" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p style="margin-top: 20px;">Already have an account? <a href="login.php">Login here</a>.</p>
</div>

<?php
require_once 'templates/footer.php';
?>