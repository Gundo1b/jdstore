<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';
?>

<style>
/* Container Styling */
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.admin-table thead {
    background: #0d6efd;
    color: white;
}

.admin-table th, .admin-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e5e5e5;
    font-size: 14px;
}

.admin-table tbody tr:hover {
    background: #f8f9fa;
}

.thumb {
    width: 70px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #ccc;
}

.add-btn {
    background: #198754 !important;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
}

.stock-badge {
    padding: 5px 10px;
    border-radius: 20px;
    color: #fff;
    background: #0d6efd;
    font-size: 12px;
}

.admin-actions a {
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 5px;
    font-size: 12px;
    margin-right: 6px;
}

.admin-actions .edit {
    background: #ffc107;
    color: #000;
}

.admin-actions .delete {
    background: #dc3545;
    color: #fff;
}
</style>

<h1>Manage Products</h1>
<p><a href="add_product.php" class="btn btn-primary add-btn">Add New Product</a></p>

<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>ISBN</th>
            <th>Brand</th>
            <th>Author</th>
            <th>Publisher</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Discount (%)</th>
            <th>Profit</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $image_sql = "SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1";
                $image_stmt = $conn->prepare($image_sql);
                $image_stmt->bind_param("i", $row['id']);
                $image_stmt->execute();
                $image_result = $image_stmt->get_result();
                $image_row = $image_result->fetch_assoc();
                $image_stmt->close();

                $image_path = $image_row && file_exists('../assets/images/' . $image_row['image'])
                    ? '../assets/images/' . htmlspecialchars($image_row['image'])
                    : 'https://via.placeholder.com/80x60?text=No+Image';
                ?>
                <tr>
                    <td style="position: relative;">
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="thumb">
                        <?php if (!empty($row['sale_discount']) && $row['sale_discount'] > 0): ?>
                            <span style="position: absolute; top: 5px; right: 5px; background: #dc3545; color: white; padding: 4px 8px; border-radius: 50%; font-size: 12px; font-weight: bold; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">-<?php echo htmlspecialchars($row['sale_discount']); ?>%</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['isbn']); ?></td>
                    <td><?php echo htmlspecialchars($row['brand']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['publisher']); ?></td>
                    <td>R<?php echo number_format((float)($row['cost'] ?? 0), 2); ?></td>
                    <td>R<?php echo number_format((float)$row['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['sale_discount'] ?? 0); ?>%</td>
                    <td>R<?php echo number_format((float)($row['profit'] ?? 0), 2); ?></td>
                    <td><span class="stock-badge"><?php echo htmlspecialchars($row['stock_quantity'] ?? $row['stock'] ?? 0); ?></span></td>
                    <td class="admin-actions">
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit">Edit</a>
                        <a href="actions/product_actions.php?action=delete&id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='13'>No products found.</td></tr>";
        }
        $conn->close();
        ?>
    </tbody>
</table>

<?php
require_once 'templates/admin_footer.php';
?>
