<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="./css/hello.css">

   <style>
      .placed-orders .title {
         text-align: center;
         margin-bottom: 20px;
         text-transform: uppercase;
         color: black;
         font-size: 40px;
      }

      .placed-orders table {
         width: 100%;
         border-collapse: collapse;
      }

      .placed-orders th, .placed-orders td {
         border: 2px solid rgb(9, 218, 255);
         padding: 10px;
         text-align: left;
      }

      .placed-orders .empty {
         text-align: center;
      }
   </style>
</head>
<body>
   
<?php include 'index_header.php'; ?>
<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <table>
      <tr>
         <th>Order Date</th>
         <th>Order Id</th>
         <th>Name</th>
         <th>Mobile Number</th>
         <th>Email Id</th>
         <th>Address</th>
         <th>Payment Method</th>
         <th>Your Orders</th>
         <th>Total Price</th>
         <th>Payment Status</th>
         <th>Action</th>
      </tr>

      <?php
        $select_book = mysqli_query($conn, "SELECT * FROM confirm_order WHERE user_id = '$user_id' ORDER BY order_date DESC") or die('query failed');
        if(mysqli_num_rows($select_book) > 0){
            while($fetch_book = mysqli_fetch_assoc($select_book)){
      ?>
      <tr>
         <td><?php echo $fetch_book['order_date']; ?></td>
         <td>#<?php echo $fetch_book['order_id']; ?></td>
         <td><?php echo $fetch_book['name']; ?></td>
         <td><?php echo $fetch_book['number']; ?></td>
         <td><?php echo $fetch_book['email']; ?></td>
         <td><?php echo $fetch_book['address']; ?></td>
         <td><?php echo $fetch_book['payment_method']; ?></td>
         <td><?php echo $fetch_book['total_books']; ?></td>
         <td>₹<?php echo $fetch_book['total_price']; ?>/-</td>
         <td style="color:<?php echo ($fetch_book['payment_status'] == 'pending') ? 'orange' : 'green'; ?>;"><?php echo $fetch_book['payment_status']; ?></td>
         <td><a href="invoice.php?order_id=<?php echo $fetch_book['order_id']; ?>" target="_blank">Print Receipt</a></td>
      </tr>
      <?php
         }
      } else {
         echo '<tr><td colspan="11" class="empty">You have not placed any order yet!!!!</td></tr>';
      }
      ?>
   </table>
</section>

<?php include 'index_footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>