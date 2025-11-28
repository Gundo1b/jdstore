<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// Check if a category ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='main-layout'><div class='main-content'><h1>Invalid Category</h1><p>No category was selected.</p></div></div>";
    require_once 'templates/footer.php';
    exit();
}

$category_id = intval($_GET['id']);

// Fetch category details
$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->bind_result($category_name);
$stmt->fetch();
$stmt->close();

if (!$category_name) {
    echo "<div class='main-layout'><div class='main-content'><h1>Category Not Found</h1><p>The selected category does not exist.</p></div></div>";
    require_once 'templates/footer.php';
    exit();
}
?>

<div class="main-layout">
    <?php include 'templates/sidebar.php'; ?>
    <div class="main-content">
        <!-- Categories -->
        <div class="categories-breadcrumb" style="margin-bottom: 20px;">
            <strong>Categories:</strong>
            <?php
            $cat_sql = "SELECT id, name FROM categories ORDER BY name";
            $cat_result = $conn->query($cat_sql);
            if ($cat_result->num_rows > 0) {
                $categories = [];
                while($cat_row = $cat_result->fetch_assoc()) {
                    $active_class = ($cat_row['id'] == $category_id) ? 'active' : '';
                    $categories[] = '<a href="category.php?id=' . $cat_row['id'] . '" class="' . $active_class . '" style="text-decoration: none; color: #007bff;">' . htmlspecialchars($cat_row['name']) . '</a>';
                }
                echo implode(' / ', $categories);
            }
            ?>
        </div>

        <h3>Category: <?php echo htmlspecialchars($category_name); ?></h3>

        <div class="product-grid">
            <?php
            // Fetch products in this category
            $stmt = $conn->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY p.created_at DESC");
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    include 'templates/product_card.php';
                }
            } else {
                echo "<p>No products found in this category.</p>";
            }
            $stmt->close();
            ?>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>