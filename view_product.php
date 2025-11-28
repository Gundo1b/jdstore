<?php
include 'config/db.php';
include 'templates/header.php';

$product = null;
$product_images = [];
if (isset($_GET['id'])) {
    $product_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    $stmt = $conn->prepare("SELECT p.*, p.image, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    // Fetch all images for this product
    $images_stmt = $conn->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id");
    $images_stmt->bind_param("i", $product_id);
    $images_stmt->execute();
    $images_result = $images_stmt->get_result();
    while ($img = $images_result->fetch_assoc()) {
        $product_images[] = $img['image'];
    }
    $images_stmt->close();
}
?>

<div class="main-layout">
    <?php include 'templates/sidebar.php'; ?>
    <div class="main-content">
        <?php if ($product): ?>
            <div style="display: flex; gap: 30px; margin-top: 20px;">
                <div style="flex: 1; position: relative;">
                    <div class="product-image-carousel" style="position: relative; max-width: 100%; margin: 0 auto;">
                        <?php if (!empty($product_images)): ?>
                            <?php foreach ($product_images as $index => $image): ?>
                                <img src="assets/images/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image <?php echo $index === 0 ? 'active' : ''; ?>" style="max-width: 100%; height: auto; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <img src="assets/images/default.jpg" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100%; height: auto; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <?php endif; ?>

                        <?php if (count($product_images) > 1): ?>
                            <button class="image-nav prev" onclick="changeImage(-1)" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px; cursor: pointer; border-radius: 50%;">&larr;</button>
                            <button class="image-nav next" onclick="changeImage(1)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: white; border: none; padding: 10px; cursor: pointer; border-radius: 50%;">&rarr;</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="flex: 1;">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <?php
                    $price = (float)($product['price'] ?? 0);
                    $discount = (float)($product['sale_discount'] ?? 0);
                    if ($discount > 0) {
                        $discounted = $price - ($price * ($discount / 100));
                        ?>
                        <p class="price" style="font-size: 2rem; color: #007bff; font-weight: bold;">
                            <span style="text-decoration:line-through; color:#888; margin-right:8px;">R<?php echo number_format($price, 2); ?></span>
                            <span style="background:#dc3545; color:#fff; padding:6px 10px; border-radius:4px; font-weight:700;">R<?php echo number_format($discounted, 2); ?></span>
                        </p>
                        <?php
                    } else {
                        ?>
                        <p class="price" style="font-size: 2rem; color: #007bff; font-weight: bold;">R<?php echo number_format($price, 2); ?></p>
                        <?php
                    }
                    ?>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <?php if (!empty($product['description'])): ?>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($product['isbn'])): ?>
                        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($product['isbn']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($product['brand'])): ?>
                        <p><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($product['author'])): ?>
                        <p><strong>Author:</strong> <?php echo htmlspecialchars($product['author']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($product['publisher'])): ?>
                        <p><strong>Publisher:</strong> <?php echo htmlspecialchars($product['publisher']); ?></p>
                    <?php endif; ?>

                    <form action="actions/cart_actions.php?action=add" method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control" style="width: 80px;">
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div style="color: red; border: 1px solid red; padding: 10px; margin-top: 20px;">
                Product not found.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<script>
let currentImageIndex = 0;
const images = document.querySelectorAll('.product-image');

function changeImage(direction) {
    if (images.length === 0) return;

    // Hide current image
    images[currentImageIndex].style.display = 'none';

    // Calculate new index
    currentImageIndex += direction;
    if (currentImageIndex < 0) {
        currentImageIndex = images.length - 1;
    } else if (currentImageIndex >= images.length) {
        currentImageIndex = 0;
    }

    // Show new image
    images[currentImageIndex].style.display = 'block';
}
</script>
