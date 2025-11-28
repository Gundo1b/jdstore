<?php session_start();
// In a real system, robust admin authentication would be here.
// For now, we assume direct access to admin pages is intended or managed externally.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JDStore Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
/* Admin-specific styles */
.admin-actions a {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    color: #fff;
    background: #6c757d;
    font-size: 0.9rem;
}
.admin-actions a.edit {
    background: #007bff;
}
.admin-actions a.delete {
    background: #dc3545;
}
.add-btn {
    margin-bottom: 20px;
}
.admin-table thead th {
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.03em;
}
.admin-table tbody tr:hover {
    background: #f8f9fa;
}
.thumb {
    max-width: 80px;
    max-height: 60px;
    
    object-fit: cover;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.price-cell { color: #007bff; font-weight: 600; }
.cost-cell { color: #28a745; }
.profit-cell { color: #17a2b8; font-weight: 600; }
.stock-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    background: #e9ecef;
    color: #333;
    font-size: 0.9rem;
}
@media (max-width: 900px) {
    .admin-table thead { display: none; }
    .admin-table, .admin-table tbody, .admin-table tr, .admin-table td { display: block; width: 100%; }
    .admin-table tr { margin-bottom: 1rem; border-bottom: 1px solid #eee; }
    .admin-table td { text-align: right; padding-left: 50%; position: relative; }
    .admin-table td::before { content: attr(data-label); position: absolute; left: 12px; width: calc(50% - 24px); text-align: left; font-weight: 600; }
}

        /* Admin specific styles for the simplified header */
        .admin-navbar {
            background-color: #222; /* Darker background for admin */
            color: #fff;
            padding: 1rem 0;
            margin-bottom: 20px;
        }
        .admin-navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-navbar .brand {
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }
        .admin-nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        .admin-nav-links li {
            margin-left: 20px;
        }
        .admin-nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            padding: 5px 10px;
            display: block;
            border-bottom: 2px solid transparent; /* Highlight active link */
        }
        .admin-nav-links a:hover, .admin-nav-links a.active {
            border-bottom: 2px solid #007bff;
        }
    </style>
</head>
<body>
    <header>
        <nav class="admin-navbar">
            <div class="container">
                <a href="index.php" class="brand">JDStore Admin</a>
                <ul class="admin-nav-links">
                    <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Manage Products</a></li>
                    <li><a href="manage_categories.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_categories.php') ? 'active' : ''; ?>">Manage Categories</a></li>
                    <li><a href="manage_users.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : ''; ?>">Manage Users</a></li>
                    <li><a href="manage_orders.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage_orders.php') ? 'active' : ''; ?>">Manage Orders</a></li>
                    <li><a href="../index.php">View Store</a></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container">
