<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Discount Calculator</title>
</head>
<body>

<h2>Discount Calculator</h2>

<label for="price">Price:</label>
<input type="number" id="update_price" name="update_price" min="0" step="0.01" required><br><br>

<label for="discount">Discount (%):</label>
<input type="number" id="discount" name="discount" min="0" max="100" step="1" required><br><br>

<label for="discountedPrice">Discounted Price:</label>
<input type="text" id="discountprice" name="discountprice" readonly><br><br>

<script>
document.getElementById("discount").addEventListener("input", function() {
    var price = parseFloat(document.getElementById("update_price").value);
    var discount = parseFloat(document.getElementById("discount").value);
    
    if (!isNaN(price) && !isNaN(discount)) {
        var discountedPrice = price - (price * (discount / 100));
        document.getElementById("discountprice").value = discountedPrice.toFixed(2);
    } else {
        document.getElementById("discountprice").value = '';
    }
});
</script>

</body>
</html>
