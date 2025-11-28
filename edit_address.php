<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's current address
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address_result = $stmt->get_result();
$address = $address_result->fetch_assoc();
$stmt->close();

// If no address, initialize an empty array to avoid errors in the form
if (!$address) {
    $address = [
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => '',
        'phone_number' => ''
    ];
}
?>

<div class="main-content">
    <h1>Edit Shipping Address</h1>

    <form action="actions/auth_actions.php?action=update_address" method="POST">
        <div class="form-group">
            <label for="address1">Address Line 1</label>
            <input type="text" id="address1" name="address_line1" class="form-control" value="<?php echo htmlspecialchars($address['address_line1']); ?>" required>
        </div>
        <div class="form-group">
            <label for="address2">Address Line 2 (Optional)</label>
            <input type="text" id="address2" name="address_line2" class="form-control" value="<?php echo htmlspecialchars($address['address_line2']); ?>">
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($address['city']); ?>" required>
        </div>
        <div class="form-group">
            <label for="state">State / Province</label>
            <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($address['state']); ?>" required>
        </div>
        <div class="form-group">
            <label for="zip">ZIP / Postal Code</label>
            <input type="text" id="zip" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($address['zip_code']); ?>" required>
        </div>
        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" id="country" name="country" class="form-control" value="<?php echo htmlspecialchars($address['country']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($address['phone_number']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Save Address</button>
        <a href="account.php" class="btn">Cancel</a>
    </form>
</div>

<?php
require_once 'templates/footer.php';
?>
