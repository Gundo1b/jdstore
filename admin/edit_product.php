<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = $_GET['id'];

// Fetch product data
$product_sql = "SELECT * FROM products WHERE id = ?";
$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product = $product_result->fetch_assoc();
$product_stmt->close();

if (!$product) {
    header("Location: index.php");
    exit();
}

// Fetch categories
$categories_sql = "SELECT * FROM categories ORDER BY name";
$categories_result = $conn->query($categories_sql);

// Fetch existing images
$images_sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY id";
$images_stmt = $conn->prepare($images_sql);
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();
$existing_images = [];
while ($img = $images_result->fetch_assoc()) {
    $existing_images[] = $img;
}
$images_stmt->close();
?>

<h1 style="margin-top:0; color:#333; font-size:1.6rem;">Edit Product</h1>

<form action="actions/product_actions.php?action=edit&id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data" style="background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.05); max-width:900px;">
    <div class="form-group" style="margin-bottom:15px;">
        <label for="name" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Product Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
    </div>
    <div class="form-group" style="margin-bottom:15px;">
        <label for="description" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Description</label>
        <textarea id="description" name="description" class="form-control" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; min-height:120px;"><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>
    <div class="form-group" style="margin-bottom:15px; display:flex; gap:12px; flex-wrap:wrap;">
        <div style="flex:1; min-width:180px;">
            <label for="price" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Price</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
        </div>
        <div style="flex:1; min-width:180px;">
            <label for="stock_quantity" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Stock Quantity</label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" min="0" value="<?php echo htmlspecialchars($product['stock_quantity'] ?? 0); ?>" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
        </div>
    </div>
    <div class="form-group" style="margin-bottom:15px;">
        <label for="category_id" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Category</label>
        <select id="category_id" name="category_id" class="form-control" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group" style="margin-bottom:15px;">
        <label for="isbn" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">ISBN (for Books)</label>
        <input type="text" id="isbn" name="isbn" class="form-control" value="<?php echo htmlspecialchars($product['isbn']); ?>" placeholder="Optional, for books only" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;">
    </div>

    <div class="form-group" style="margin-bottom:15px; display:flex; gap:12px; flex-wrap:wrap;">

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Brand (Optional)</label>
            <input type="text" id="brand" name="brand" class="form-control" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Author (Optional)</label>
            <input type="text" id="author" name="author" class="form-control" value="<?php echo htmlspecialchars($product['author'] ?? ''); ?>"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Publisher (Optional)</label>
            <input type="text" id="publisher" name="publisher" class="form-control" value="<?php echo htmlspecialchars($product['publisher'] ?? ''); ?>"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Sale Discount (%)</label>
            <input type="number" id="sale_discount" name="sale_discount" class="form-control" value="<?php echo htmlspecialchars($product['sale_discount'] ?? 0); ?>" step="0.01" min="0" max="100"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="0">
        </div>

    </div>

    <div class="form-group" style="margin-bottom:15px;">
        <label style="display:block; margin-bottom:8px; font-weight:600; color:#333;">Current Images</label>
        <div id="current-images">
            <?php foreach ($existing_images as $img): ?>
                <div class="image-item" style="display: inline-block; margin: 10px; position: relative; text-align:center;">
                    <img src="../assets/images/<?php echo htmlspecialchars($img['image']); ?>" alt="Product Image" style="max-width: 100px; max-height: 100px; border-radius:4px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
                    <br>
                    <label style="font-size:0.9rem; display:block; margin-top:6px;"><input type="checkbox" name="delete_images[]" value="<?php echo $img['id']; ?>"> Delete</label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group" style="margin-bottom:15px;">
        <label for="images" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Add New Images (up to 5 additional)</label>
        <input type="file" id="image1" name="images[]" class="form-control" accept="image/*" style="display:block; margin-bottom:8px;">
        <input type="file" id="image2" name="images[]" class="form-control" accept="image/*" style="display:block; margin-bottom:8px;">
        <input type="file" id="image3" name="images[]" class="form-control" accept="image/*" style="display:block; margin-bottom:8px;">
        <input type="file" id="image4" name="images[]" class="form-control" accept="image/*" style="display:block; margin-bottom:8px;">
        <input type="file" id="image5" name="images[]" class="form-control" accept="image/*" style="display:block; margin-bottom:8px;">
    </div>

    <button type="submit" class="btn btn-primary" style="padding:10px 18px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">Update Product</button>
    <a href="index.php" class="btn btn-danger" style="display:inline-block; margin-left:10px; padding:10px 18px; background:#dc3545; color:#fff; text-decoration:none; border-radius:4px;">Cancel</a>
</form>

<?php
require_once 'templates/admin_footer.php';
?>
