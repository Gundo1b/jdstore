<?php
require_once 'config/db.php';
require_once 'templates/header.php';
?>

<div class="main-layout">
    <?php include 'templates/sidebar.php'; ?>
    <div class="main-content">
        <!-- <h1>Welcome to JDStore</h1>
        <p>Browse our collection of books and stationery.</p> -->

        <!-- Categories -->
        <div class="categories-breadcrumb" style="margin-bottom: 20px;">
            <strong>Categories:</strong>
            <?php
            $cat_sql = "SELECT id, name FROM categories ORDER BY name";
            $cat_result = $conn->query($cat_sql);
            if ($cat_result->num_rows > 0) {
                $categories = [];
                while($cat_row = $cat_result->fetch_assoc()) {
                    $categories[] = '<a href="category.php?id=' . $cat_row['id'] . '" style="text-decoration: none; color: #007bff;">' . htmlspecialchars($cat_row['name']) . '</a>';
                }
                echo implode(' / ', $categories);
            }
            ?>
        </div>

        <h2>Our Products</h2>

        <div class="product-grid">
            <?php
            $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    include 'templates/product_card.php';
                }
            } else {
                echo "<p>No products found. Please check back later!</p>";
            }
            // Note: The connection is closed in the header/footer includes, so we might re-evaluate closing it here. For now, let's keep it consistent.
            // $conn->close();
            ?>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>