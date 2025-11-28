<?php
require_once '../../config/db.php';

if (isset($_GET['action']) && $_GET['action'] == 'edit') {
    // Check if product ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: ../index.php");
        exit();
    }

    $product_id = $_GET['id'];

    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'] ?? 0;
        $category_id = $_POST['category_id'];
        $isbn = !empty($_POST['isbn']) ? $_POST['isbn'] : NULL;
        $brand = !empty($_POST['brand']) ? $_POST['brand'] : NULL;
        $author = !empty($_POST['author']) ? $_POST['author'] : NULL;
        $publisher = !empty($_POST['publisher']) ? $_POST['publisher'] : NULL;
        $sale_discount = !empty($_POST['sale_discount']) ? $_POST['sale_discount'] : 0;

        // Update product details
        $update_sql = "UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?, isbn = ?, brand = ?, author = ?, publisher = ?, sale_discount = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssdissssdii", $name, $description, $price, $stock_quantity, $category_id, $isbn, $brand, $author, $publisher, $sale_discount, $product_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Handle image deletions
        if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $image_id) {
                // Get image filename before deleting
                $img_sql = "SELECT image FROM product_images WHERE id = ? AND product_id = ?";
                $img_stmt = $conn->prepare($img_sql);
                $img_stmt->bind_param("ii", $image_id, $product_id);
                $img_stmt->execute();
                $img_result = $img_stmt->get_result();
                $img_row = $img_result->fetch_assoc();
                $img_stmt->close();

                if ($img_row) {
                    // Delete file from server
                    $file_path = "../../assets/images/" . $img_row['image'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }

                    // Delete from database
                    $del_sql = "DELETE FROM product_images WHERE id = ? AND product_id = ?";
                    $del_stmt = $conn->prepare($del_sql);
                    $del_stmt->bind_param("ii", $image_id, $product_id);
                    $del_stmt->execute();
                    $del_stmt->close();
                }
            }
        }

        // Handle new image uploads
        $target_dir = "../../assets/images/";
        $uploaded_images = [];

        // Loop through each uploaded image
        foreach ($_FILES["images"]["name"] as $key => $image_name) {
            // Skip if no file uploaded for this input
            if (empty($_FILES["images"]["tmp_name"][$key])) {
                continue;
            }

            // Create a unique name for the image to prevent overwriting existing files
            $unique_image_name = uniqid() . '-' . basename($image_name);
            $target_file = $target_dir . $unique_image_name;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["images"]["tmp_name"][$key]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                continue; // Skip invalid images
            }

            // Check file size (e.g., 5MB limit)
            if ($_FILES["images"]["size"][$key] > 5000000) {
                continue; // Skip large files
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                continue; // Skip invalid formats
            }

            // Upload file
            if (move_uploaded_file($_FILES["images"]["tmp_name"][$key], $target_file)) {
                $uploaded_images[] = $unique_image_name;
            }
        }

        // Insert new images into database
        if (!empty($uploaded_images)) {
            $image_stmt = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
            $image_stmt->bind_param("is", $product_id, $image_name);

            foreach ($uploaded_images as $image_name) {
                $image_stmt->execute();
            }
            $image_stmt->close();
        }

        // Redirect back to admin index on success
        header("Location: ../index.php?status=updated");
        exit();
    }
} elseif (isset($_GET['action']) && $_GET['action'] == 'delete') {
    // Check if product ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: ../index.php");
        exit();
    }

    $product_id = $_GET['id'];

    // Delete order items first to avoid foreign key constraint
    $del_order_items_sql = "DELETE FROM order_items WHERE product_id = ?";
    $del_order_items_stmt = $conn->prepare($del_order_items_sql);
    $del_order_items_stmt->bind_param("i", $product_id);
    $del_order_items_stmt->execute();
    $del_order_items_stmt->close();

    // Fetch all images for this product
    $images_sql = "SELECT image FROM product_images WHERE product_id = ?";
    $images_stmt = $conn->prepare($images_sql);
    $images_stmt->bind_param("i", $product_id);
    $images_stmt->execute();
    $images_result = $images_stmt->get_result();

    // Delete image files from server
    while ($img = $images_result->fetch_assoc()) {
        $file_path = "../../assets/images/" . $img['image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $images_stmt->close();

    // Delete images from database
    $del_images_sql = "DELETE FROM product_images WHERE product_id = ?";
    $del_images_stmt = $conn->prepare($del_images_sql);
    $del_images_stmt->bind_param("i", $product_id);
    $del_images_stmt->execute();
    $del_images_stmt->close();

    // Delete product from database
    $del_product_sql = "DELETE FROM products WHERE id = ?";
    $del_product_stmt = $conn->prepare($del_product_sql);
    $del_product_stmt->bind_param("i", $product_id);
    $del_product_stmt->execute();
    $del_product_stmt->close();

    // Redirect back to admin index on success
    header("Location: ../index.php?status=deleted");
    exit();
} elseif (isset($_GET['action']) && $_GET['action'] == 'add') {
    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $cost = $_POST['cost']; // Added this line
        $category_id = $_POST['category_id'];
        $isbn = !empty($_POST['isbn']) ? $_POST['isbn'] : NULL;
        $brand = !empty($_POST['brand']) ? $_POST['brand'] : NULL;
        $author = !empty($_POST['author']) ? $_POST['author'] : NULL;
        $publisher = !empty($_POST['publisher']) ? $_POST['publisher'] : NULL;
        $sale_discount = !empty($_POST['sale_discount']) ? $_POST['sale_discount'] : 0;
        $stock_quantity = $_POST['stock_quantity'] ?? 0; // Modified this line

        // --- Multiple Image Upload Handling ---
        $target_dir = "../../assets/images/";
        $uploaded_images = [];
        $upload_errors = [];

        // Loop through each uploaded image
        foreach ($_FILES["images"]["name"] as $key => $image_name) {
            // Skip if no file uploaded for this input
            if (empty($_FILES["images"]["tmp_name"][$key])) {
                continue;
            }

            // Create a unique name for the image to prevent overwriting existing files
            $unique_image_name = uniqid() . '-' . basename($image_name);
            $target_file = $target_dir . $unique_image_name;
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["images"]["tmp_name"][$key]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                $upload_errors[] = "File '$image_name' is not an image.";
                $uploadOk = 0;
            }

            // Check file size (e.g., 5MB limit)
            if ($_FILES["images"]["size"][$key] > 5000000) {
                $upload_errors[] = "File '$image_name' is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                $upload_errors[] = "File '$image_name' has an invalid format. Only JPG, JPEG, PNG & GIF are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                // Continue to next image
                continue;
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["images"]["tmp_name"][$key], $target_file)) {
                    $uploaded_images[] = $unique_image_name;
                } else {
                    $upload_errors[] = "There was an error uploading '$image_name'.";
                }
            }
        }

        // Check if at least one image was uploaded
        if (empty($uploaded_images)) {
            echo "Error: At least one image must be uploaded.";
            if (!empty($upload_errors)) {
                echo "<br>Upload errors: " . implode("<br>", $upload_errors);
            }
            exit();
        }

        // Insert product into database first
        $stmt = $conn->prepare("INSERT INTO products (name, description, cost, price, category_id, isbn, brand, author, publisher, sale_discount, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddissssdi", $name, $description, $cost, $price, $category_id, $isbn, $brand, $author, $publisher, $sale_discount, $stock_quantity);

        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            $stmt->close();

            // Now insert each image into product_images table
            $image_stmt = $conn->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
            $image_stmt->bind_param("is", $product_id, $image_name);

            foreach ($uploaded_images as $image_name) {
                $image_stmt->execute();
            }
            $image_stmt->close();

            // Redirect back to admin index on success
            header("Location: ../index.php?status=success");
            exit();
        } else {
            echo "Error: " . $stmt->error;
            $stmt->close();
        }
        $conn->close();
    }
} else {
    // Redirect if accessed directly without an action
    header("Location: ../index.php");
    exit();
}
?>