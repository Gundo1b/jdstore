<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';
?>

    <h1>Manage Categories</h1>

    <div class="container" style="display: flex; gap: 40px;">

        <!-- Add Category Form -->
        <div style="flex: 1;">
            <h2>Add New Category</h2>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'cat_added'): ?>
                <p style="color: green;">Category added successfully!</p>
            <?php endif; ?>
            <?php if(isset($_GET['error'])): ?>
                <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <form action="actions/category_actions.php?action=add" method="POST">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>

        <!-- List Existing Categories -->
        <div style="flex: 2;">
            <h2>Existing Categories</h2>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'cat_deleted'): ?>
                <p style="color: green;">Category deleted successfully!</p>
            <?php endif; ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM categories ORDER BY name ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <a href="actions/category_actions.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure? Deleting a category will also require updating or deleting all products within it.');" style="color: red;">Delete</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='3'>No categories found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

<?php
$conn->close();
require_once 'templates/admin_footer.php';
?>