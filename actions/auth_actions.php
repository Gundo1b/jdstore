<?php
session_start();
require_once '../config/db.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'register') {
        // --- REGISTRATION LOGIC ---
        // Get data from POST request
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $address_line1 = $_POST['address_line1'];
        $address_line2 = $_POST['address_line2'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip_code = $_POST['zip_code'];
        $country = $_POST['country'];
        $phone_number = $_POST['phone_number'];
        
        // Validation: Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            die("Error: Email address already registered. <a href='../login.php'>Login here</a>.");
        }
        $stmt->close();

        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Use a transaction to ensure both user and address are created successfully
        $conn->begin_transaction();

        try {
            // Insert user into the users table
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            $stmt->execute();
            $user_id = $stmt->insert_id; // Get the ID of the new user
            $stmt->close();

            // Insert address into the addresses table
            $stmt = $conn->prepare("INSERT INTO addresses (user_id, address_line1, address_line2, city, state, zip_code, country, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $user_id, $address_line1, $address_line2, $city, $state, $zip_code, $country, $phone_number);
            $stmt->execute();
            $stmt->close();

            // If both queries are successful, commit the transaction
            $conn->commit();

            // Automatically log the user in
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;

            // Redirect to a success page or the home page
            header("Location: ../index.php?status=registered");
            exit();

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback(); // Rollback the transaction on error
            die("Error: Registration failed. Please try again. " . $exception->getMessage());
        }

    } elseif ($action == 'login') {
        // --- LOGIN LOGIC ---
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Find user by email
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $name, $hashed_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                
                // Redirect to the user's dashboard or homepage
                header("Location: ../index.php");
                exit();
            } else {
                // Password is not valid
                header("Location: ../login.php?error=Invalid email or password");
                exit();
            }
        } else {
            // No user found with that email
            header("Location: ../login.php?error=Invalid email or password");
            exit();
        }
        $stmt->close();

    } elseif ($action == 'update_address' && isset($_SESSION['user_id'])) {
        // --- UPDATE ADDRESS LOGIC ---
        $user_id = $_SESSION['user_id'];
        
        $address_line1 = $_POST['address_line1'];
        $address_line2 = $_POST['address_line2'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip_code = $_POST['zip_code'];
        $country = $_POST['country'];
        $phone_number = $_POST['phone_number'];

        // Check if an address already exists for this user
        $stmt = $conn->prepare("SELECT id FROM addresses WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Address exists, so UPDATE it
            $stmt->close();
            $stmt = $conn->prepare("UPDATE addresses SET address_line1 = ?, address_line2 = ?, city = ?, state = ?, zip_code = ?, country = ?, phone_number = ? WHERE user_id = ?");
            $stmt->bind_param("sssssssi", $address_line1, $address_line2, $city, $state, $zip_code, $country, $phone_number, $user_id);
        } else {
            // No address exists, so INSERT a new one
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO addresses (user_id, address_line1, address_line2, city, state, zip_code, country, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $user_id, $address_line1, $address_line2, $city, $state, $zip_code, $country, $phone_number);
        }

        if ($stmt->execute()) {
            header("Location: ../account.php?status=address_updated");
        } else {
            die("Error: Could not update address. " . $stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();
?>