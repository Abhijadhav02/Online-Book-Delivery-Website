<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `book_info` WHERE bid = '$delete_id'") or die('query failed');
   header('location:total_books.php');
}


if (isset($_POST['update_product'])) {

   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_title = $_POST['update_title'];
   $update_description = $_POST['update_description'];
   $update_price = $_POST['update_price'];
   $discount = $_POST['discount'];
   $discountprice = $_POST['discountprice'];
   $update_stock = $_POST['update_stock']; // New input for stock quantity
   $update_category = $_POST['update_category'];

   mysqli_query($conn, "UPDATE `book_info` SET name = '$update_name', title='$update_title', description ='$update_description', price = '$update_price', discount = '$discount', discountprice = '$discountprice', stock = '$update_stock', category='$update_category' WHERE bid = '$update_p_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = './added_books/' . $update_image;
   $update_old_image = $_POST['update_old_image'];

   if (!empty($update_image)) {
      if ($update_image_size > 2000000) {
         $message[] = 'image file size is too large';
      } else {
         mysqli_query($conn, "UPDATE `book_info` SET image = '$update_image' WHERE bid = '$update_p_id'") or die('query failed');
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('uploaded_img/' . $update_old_image);
      }
   }

   header('location:total_books.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="./css/register.css">
   <title>Manage Books</title>
</head>

<body>
   <?php
   include './admin_header.php'
   ?>
   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
        <div class="message"><span>' . $message . '</span><i onclick="this.parentElement.remove();">Close</i>
        </div>
        ';
      }
   }
   ?>
   <a class="update_btn" style="position: fixed ; z-index:100;" href="add_books.php">Add More Books</a>

   <section class="edit-product-form">
      <?php
      if (isset($_GET['update'])) {
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `book_info` WHERE bid = '$update_id'") or die('query failed');
         if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
      ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['bid']; ?>">
                  <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                  <img src="./added_books/<?php echo $fetch_update['image']; ?>" alt="">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter Book Name">
                  <input type="text" name="update_title" value="<?php echo $fetch_update['title']; ?>" class="box" required placeholder="Enter Author Name">
                  <input type="number" name="update_price" id="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Enter Book Price">
                  <input type="number" name="discount" id="discount" value="<?php echo $fetch_update['discount']; ?>"   min="0" class="box" required placeholder="Enter Book discount">
                 
                  <input type="text" name="discountprice" id="discountprice"  value="<?php echo $fetch_update['discountprice']; ?>" min="0" class="box" required placeholder="Book discount Price">
                  <!-- Add input for stock quantity -->
                  <input type="number" name="update_stock" value="<?php echo $fetch_update['stock']; ?>" min="0" class="box" required placeholder="Enter Stock Quantity">
                  <select name="update_category" class="text_field">
                     <option value="Adventure" <?php if ($fetch_update['category'] == 'Adventure') echo 'selected'; ?>>Adventure</option>
                     <option value="Magic" <?php if ($fetch_update['category'] == 'Magic') echo 'selected'; ?>>Magic</option>
                     <option value="Knowledge" <?php if ($fetch_update['category'] == 'Knowledge') echo 'selected'; ?>>Knowledge</option>
                  </select>
                  <input type="text" name="update_description" value="<?php echo $fetch_update['description']; ?>" class="box" required placeholder="Enter Book Description">
                  <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                  <input type="submit" value="Update" name="update_product" class="delete_btn">
                  <input type="reset" value="Cancel" id="close-update" class="update_btn">
               </form>
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
      <?php
            }
         }
      } else {
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
      ?>
      

   </section>

   <section class="show-products">
      <div class="box-container">

         <?php
         $select_book = mysqli_query($conn, "SELECT * FROM `book_info` ORDER BY date DESC") or die('query failed');
         if (mysqli_num_rows($select_book) > 0) {
            while ($fetch_book = mysqli_fetch_assoc($select_book)) {
         ?>
               <div class="box">
                  <img class="books_images" src="added_books/<?php echo $fetch_book['image']; ?>" alt="">
                  <div class="name">Author: <?php echo $fetch_book['title']; ?></div>
                  <div class="name">Name: <?php echo $fetch_book['name']; ?></div>
                  <div class="price">book Price: ₹ <?php echo $fetch_book['price']; ?>/-</div>
                  <div class="price">discount: <?php echo $fetch_book['discount']; ?>%</div>
                  <div class="price">selling Price: ₹ <?php echo $fetch_book['discountprice']; ?>/-</div>
                  <div class="stock">Stock: <?php echo $fetch_book['stock']; ?></div>
                  <a href="total_books.php?update=<?php echo $fetch_book['bid']; ?>" class="update_btn">Update</a>
                  <a href="total_books.php?delete=<?php echo $fetch_book['bid']; ?>" class="delete_btn" onclick="return confirm('Delete this product?');">Delete</a>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
         ?>
      </div>
   </section>

   <script src="./js/admin.js"></script>
</body>

</html>



<!-- this is ok code -->