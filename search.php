<?php
require_once 'config/db.php';
require_once 'templates/header.php';

// Check if a search query is provided
if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo "<div class='main-layout'><div class='main-content'><h1>Invalid Search</h1><p>Please enter a search term.</p></div></div>";
    require_once 'templates/footer.php';
    exit();
}

$search_query = trim($_GET['query']);
$search_term = "%" . $search_query . "%";
?>

<div class="main-layout">
    <?php include 'templates/sidebar.php'; ?>
    <div class="main-content">
        <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>

        <div class="product-grid">
            <?php
            // Fetch products that match the search query in name or description
            $stmt = $conn->prepare("SELECT p.*, c.name AS category_name 
                                   FROM products p 
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.name LIKE ? OR p.description LIKE ? 
                                   ORDER BY p.created_at DESC");
            $stmt->bind_param("ss", $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    include 'templates/product_card.php';
                }
            } else {
                echo "<p>No products found matching your search term.</p>";
            }
            $stmt->close();
            ?>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>