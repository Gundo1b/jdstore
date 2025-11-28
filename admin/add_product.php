<?php
require_once '../config/db.php';
require_once 'templates/admin_header.php';
?>

<h1 style="margin-top:0; color:#333; font-size:1.6rem;">Add New Product</h1>

<form action="actions/product_actions.php?action=add" method="POST" enctype="multipart/form-data" style="background:#fff; padding:20px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.05); max-width:900px;">

    <div class="form-group" style="margin-bottom:15px;">
        <label for="name" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Product Name</label>
        <input type="text" id="name" name="name" required class="form-control" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
    </div>

    <div class="form-group" style="margin-bottom:15px;">
        <label for="description" style="display:block; margin-bottom:6px; font-weight:600; color:#333;">Description</label>
        <textarea id="description" name="description" required class="form-control" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; min-height:120px;"></textarea>
    </div>

    <div class="form-group" style="margin-bottom:15px; display:flex; gap:12px; flex-wrap:wrap;">

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Cost Price</label>
            <input type="number" id="cost" name="cost" step="0.01" required class="form-control"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Selling Price</label>
            <input type="number" id="price" name="price" step="0.01" required class="form-control"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Sale Discount (%)</label>
            <input type="number" id="sale_discount" name="sale_discount" step="0.01" min="0" max="100" class="form-control"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="0">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Profit</label>
            <input type="number" id="profit" name="profit" step="0.01" readonly
                   style="width:100%; padding:10px; border:1px solid #e9ecef; background:#f8f9fa; border-radius:4px;">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Profit Margin (%)</label>
            <input type="text" id="margin" readonly
                   style="width:100%; padding:10px; border:1px solid #e9ecef; background:#f8f9fa; border-radius:4px;">
        </div>

    </div>

    <div class="form-group" style="margin-bottom:15px;">
        <label style="font-weight:600; margin-bottom:6px; display:block;">Category</label>
        <select id="category_id" name="category_id" required class="form-control"
                style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
            <option value="">-- Select a Category --</option>

            <?php
            $sql = "SELECT id, name FROM categories ORDER BY name ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<option value="'. $row['id'] .'">'. htmlspecialchars($row['name']) .'</option>';
                }
            } else {
                echo '<option disabled>No categories found.</option>';
            }
            ?>
        </select>
    </div>

    <div class="form-group" style="margin-bottom:15px;">
        <label style="font-weight:600; margin-bottom:6px; display:block;">ISBN (Optional)</label>
        <input type="text" id="isbn" name="isbn" class="form-control"
               style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
    </div>

    <div class="form-group" style="margin-bottom:15px; display:flex; gap:12px; flex-wrap:wrap;">

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Brand (Optional)</label>
            <input type="text" id="brand" name="brand" class="form-control"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Author (Optional)</label>
            <input type="text" id="author" name="author" class="form-control"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
        </div>

        <div style="flex:1; min-width:180px;">
            <label style="font-weight:600; margin-bottom:6px; display:block;">Publisher (Optional)</label>
            <input type="text" id="publisher" name="publisher" class="form-control"
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;" placeholder="Optional">
        </div>

    </div>

    <div class="form-group" style="margin-bottom:15px;">
        <label style="font-weight:600; margin-bottom:6px; display:block;">Product Images</label>
        <input type="file" name="images[]" accept="image/*" required>
        <input type="file" name="images[]" accept="image/*">
        <input type="file" name="images[]" accept="image/*">
        <input type="file" name="images[]" accept="image/*">
        <input type="file" name="images[]" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary"
            style="padding:10px 18px; background:#007bff; color:#fff; border:none; border-radius:4px;">
        Add Product
    </button>
</form>


<!-- AUTO CALC SCRIPT -->
<script>
function updateProfit() {
    let cost = parseFloat(document.getElementById("cost").value) || 0;
    let price = parseFloat(document.getElementById("price").value) || 0;
    let sale_discount = parseFloat(document.getElementById("sale_discount").value) || 0;

    // Apply discount to price to get selling price
    let discounted_price = price - (price * (sale_discount / 100));

    // Profit
    let profit = discounted_price - cost;
    document.getElementById("profit").value = profit.toFixed(2);

    // Profit Margin (%)
    let margin = (discounted_price > 0) ? ((profit / discounted_price) * 100) : 0;
    document.getElementById("margin").value = margin.toFixed(2) + "%";
}

document.getElementById("cost").addEventListener("input", updateProfit);
document.getElementById("price").addEventListener("input", updateProfit);
document.getElementById("sale_discount").addEventListener("input", updateProfit);
</script>


<?php
require_once 'templates/admin_footer.php';
?>
