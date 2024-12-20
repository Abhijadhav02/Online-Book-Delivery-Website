<?php
include 'config.php';
error_reporting(0);
session_start();

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart'])) {
    if (!isset($user_id)) {
        $message[] = 'Please Login to get your books';
    } else {
        $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
        $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
        $book_image = mysqli_real_escape_string($conn, $_POST['book_image']);
        $book_price = mysqli_real_escape_string($conn, $_POST['book_price']);
        $discount = mysqli_real_escape_string($conn, $_POST['discount']);
        $discountprice = mysqli_real_escape_string($conn, $_POST['discountprice']);
        $book_quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
        
        // Check stock availability
        $stock_check = mysqli_query($conn, "SELECT stock FROM book_info WHERE bid = '$book_id'");
        if ($stock_row = mysqli_fetch_assoc($stock_check)) {
            if ($stock_row['stock'] < $book_quantity) {
                $message[] = 'Sorry, not enough stock available.';
            } else {
                // Continue with adding to cart and deduct stock
                $total_price = number_format($book_price * $book_quantity, 2);
                $select_book = $conn->query("SELECT * FROM cart WHERE name= '$book_name' AND user_id='$user_id' ") or die('query failed');

                if (mysqli_num_rows($select_book) > 0) {
                    $message[] = 'This Book is already in your cart';
                } else {
                    $conn->query("INSERT INTO cart (`book_id`,`user_id`,`name`, `price`, `image`, `quantity` ,`total`) VALUES('$book_id','$user_id','$book_name','$book_price','$book_image','$book_quantity', '$total_price')") or die('Add to cart Query failed');
                    // Update stock
                    $new_stock = $stock_row['stock'] - $book_quantity;
                    $conn->query("UPDATE book_info SET stock = '$new_stock' WHERE bid = '$book_id'");
                    $message[] = 'Book Added To Cart Successfully';
                }
            }
        } else {
            $message[] = 'Book not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index_book.css">
    <title>Selected Products</title>

    <style>
        .message {
            position: sticky;
            top: 0;
            margin: 0 auto;
            width: 61%;
            background-color: #fff;
            padding: 6px 9px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            gap: 0px;
            border: 2px solid rgb(68, 203, 236);
            border-top-right-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .message span {
            font-size: 22px;
            color: rgb(240, 18, 18);
            font-weight: 400;
        }

        .message i {
            cursor: pointer;
            color: rgb(3, 227, 235);
            font-size: 15px;
        }

        .out-of-stock {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            text-align: center;
            background-color: rgba(255, 0, 0, 0.8);
            color: #fff;
            font-size: 18px;
            padding: 5px 0;
        }

        .row_box {
            position: relative;
        }
    </style>
</head>

<body>
    <?php
    include 'index_header.php';
    ?>
    <?php
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '
            <div class="message" id="messages"><span>' . $msg . '</span>
            </div>
            ';
        }
    }
    ?>
    <div class="details">
        <?php
        if (isset($_GET['details'])) {
            $get_id = $_GET['details'];
            $get_book = mysqli_query($conn, "SELECT * FROM `book_info` WHERE bid = '$get_id'") or die('query failed');
            if (mysqli_num_rows($get_book) > 0) {
                while ($fetch_book = mysqli_fetch_assoc($get_book)) {
                    ?>
                    <div class="row_box">
                        <?php if ($fetch_book['stock'] == 0): ?>
                            <div class="out-of-stock">Out of Stock</div>
                        <?php endif; ?>
                        <form style="display: flex ;" action="" method="POST">
                            <div class="col_box">
                                <img src="./added_books/<?php echo $fetch_book['image']; ?>" alt="<?php echo $fetch_book['name']; ?>">
                            </div>
                            <div class="col_box">
                                <h4>Author: <?php echo $fetch_book['title']; ?></h4>
                                <h1>Name: <?php echo $fetch_book['name']; ?></h1>
                                <h3>Price: ₹ <?php echo $fetch_book['price']; ?>/-</h3>
                                <h3>discount: ₹ <?php echo $fetch_book['discount']; ?>%</h3>
                                <h3>Selling Price: ₹ <?php echo $fetch_book['discountprice']; ?>/-</h3>
                                <h3>Stock: <?php echo $fetch_book['stock']; ?> available</h3>
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" value="1" min="1" max="100" id="quantity">
                                <div class="buttons">
                                    <input class="hidden_input" type="hidden" name="book_name" value="<?php echo $fetch_book['name'] ?>">
                                    <input class="hidden_input" type="hidden" name="book_id" value="<?php echo $fetch_book['bid'] ?>">
                                    <input class="hidden_input" type="hidden" name="book_image" value="<?php echo $fetch_book['image'] ?>">
                                    <input class="hidden_input" type="hidden" name="book_price" value="<?php echo $fetch_book['price'] ?>">
                                    <input type="submit" name="add_to_cart" value="Add To Cart" class="btn">
                                    <button name="add_to_cart"><img style="height: 40px;" src="./images/cart1.png" alt="Add to cart"></button>
                                </div>
                                <h3>Book Details</h3>
                                <p><?php echo $fetch_book['description']; ?></p>
                            </div>
                        </form>
                    </div>
        <?php
                }
            }
        } else {
            echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
        }
        ?>
    </div>
    <script src="./js/admin.js"></script>
    <script>
        setTimeout(() => {
            const box = document.getElementById('messages');
            // 👇️ hides element (still takes up space on page)
            box.style.display = 'none';
        }, 5000);
    </script>
</body>

</html>





<!--   ----   this is ok code   -->
<!--   ----   this is ok code   -->




