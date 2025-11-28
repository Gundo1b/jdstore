<div class="product-card">
    <?php
    // Fetch the first image for this product
    $image_sql = "SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC LIMIT 1";
    $image_stmt = $conn->prepare($image_sql);
    $image_stmt->bind_param("i", $row['id']);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();
    $image_row = $image_result->fetch_assoc();
    $image_stmt->close();

    $image_path = $image_row && file_exists('assets/images/' . $image_row['image']) ? 'assets/images/' . htmlspecialchars($image_row['image']) : 'https://via.placeholder.com/250x200?text=No+Image';
    ?>
    <div style="position: relative; display: inline-block; width: 100%;">
        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <?php if (!empty($row['sale_discount']) && $row['sale_discount'] > 0): ?>
            <span style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; padding: 6px 10px; border-radius: 50%; font-size: 14px; font-weight: bold; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">-<?php echo htmlspecialchars($row['sale_discount']); ?>%</span>
        <?php endif; ?>
    </div>
    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
    <?php
    $price = (float)($row['price'] ?? 0);
    $discount = (float)($row['sale_discount'] ?? 0);
    if ($discount > 0) {
        $discounted = $price - ($price * ($discount / 100));
        ?>
        <p class="price">
            <span style="text-decoration:line-through; color:#888; margin-right:8px;">R<?php echo number_format($price, 2); ?></span>
            <span style="background:#dc3545; color:#fff; padding:6px 10px; border-radius:4px; font-weight:700;">R<?php echo number_format($discounted, 2); ?></span>
        </p>
        <?php
    } else {
        ?>
        <p class="price">R<?php echo number_format($price, 2); ?></p>
        <?php
    }
    ?>
    <p><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></p>
    <!--<?php if (!empty($row['description'])): ?>-->
    <!--    <p><?php echo htmlspecialchars($row['description']); ?></p>-->
    <!--<?php endif; ?>-->
    <?php if (!empty($row['isbn'])): ?>
        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($row['isbn']); ?></p>
    <?php endif; ?>
    
    <form action="actions/cart_actions.php?action=add" method="POST">
        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
        <div class="form-group" style="display: inline-flex; align-items: center; gap: 10px;">
            <label for="quantity-<?php echo $row['id']; ?>" class="sr-only">Quantity</label>
            <input type="number" id="quantity-<?php echo $row['id']; ?>" name="quantity" value="1" min="1" style="width: 60px;" class="form-control">
            <button type="submit" class="btn btn-primary">Add to Cart</button>
        </div>
    </form>
    <a href="view_product.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary mt-2">View Product</a>
</div>
