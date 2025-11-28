<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';

// A real admin system would have its own login. For now, we assume access.
?>

    <h1>Manage Users</h1>
    <p>This page lists all registered customers.</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registered On</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo date_format(date_create($row['created_at']), 'Y-m-d H:i'); ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4'>No users found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php
$conn->close();
require_once 'templates/admin_footer.php';
?>
