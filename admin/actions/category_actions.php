<?php
require_once '../../config/db.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];

        // Check if category already exists
        $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            header("Location: ../manage_categories.php?error=Category+already+exists");
            exit();
        }

        // Insert new category
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            header("Location: ../manage_categories.php?status=cat_added");
        } else {
            header("Location: ../manage_categories.php?error=Failed+to+add+category");
        }
        $stmt->close();

    } elseif ($action == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Before deleting, check if any products use this category
        // Note: The ON DELETE RESTRICT in the DB schema will prevent this automatically,
        // but we add a user-friendly check here.
        $stmt = $conn->prepare("SELECT id FROM products WHERE category_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            header("Location: ../manage_categories.php?error=Cannot+delete+category+as+it+is+in+use+by+products.");
            exit();
        }
        $stmt->close();

        // Delete the category
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: ../manage_categories.php?status=cat_deleted");
        } else {
            header("Location: ../manage_categories.php?error=Failed+to+delete+category");
        }
        $stmt->close();
    }
}

$conn->close();
?>