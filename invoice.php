<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
$dompdf = new Dompdf;
include 'config.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $get_order = mysqli_query($conn, "SELECT * FROM `confirm_order` WHERE order_id = '$order_id'") or die('query failed');
    if (mysqli_num_rows($get_order) > 0) {
        $fetch_order = mysqli_fetch_assoc($get_order);
    }
    $get_order = mysqli_query($conn, "SELECT * FROM `orders` WHERE id = '$order_id'") or die('query failed');
    if (mysqli_num_rows($get_order) > 0) {
        $fetch_details = mysqli_fetch_assoc($get_order);
    }
}

$html = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Invoice</title>
<style>
.invoice {
    font-family: Arial, sans-serif;
    width: 100%;
    max-width: 800px;
    margin: auto;
    padding: 20px;
    border: 1px solid #ccc;
}

.invoice-header {
    text-align: center;
    margin-bottom: 20px;
}

.invoice-header h1 {
    color: #333;
    margin: 0;
}

.invoice-details {
    margin-bottom: 20px;
}

.invoice-details p {
    margin: 5px 0;
}

.invoice-items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.invoice-items th,
.invoice-items td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

.invoice-total {
    margin-top: 20px;
}

.invoice-total th,
.invoice-total td {
    border: none;
    padding: 10px;
}

.invoice-footer {
    text-align: right;
}

.invoice-footer p {
    margin: 5px 0;
}

</style>
</head>
<body>
<div class="invoice">
    <div class="invoice-header">
        <h1>Invoice</h1>
    </div>
    <div class="invoice-details">
        <p><strong>Invoice Date:</strong> ' . $fetch_order['date'] . '</p>
        <p><strong>Order ID:</strong> ' . $fetch_order['order_id'] . '</p>
        <p><strong>Order Date:</strong> ' . $fetch_order['order_date'] . '</p>
        <p><strong>Payment Method:</strong> ' . $fetch_order['payment_method'] . '</p>
    </div>
    <hr />
    <div class="buyer-details">
        <p><strong>Bill To:</strong></p>
        <p><strong>Name:</strong> ' . $fetch_order['name'] . '</p>
        <p><strong>Address:</strong> ' . $fetch_details['address'] . '</p>
        <p><strong>City:</strong> ' . $fetch_details['city'] . '</p>
        <p><strong>State:</strong> ' . $fetch_details['state'] . '</p>
        <p><strong>Country:</strong> ' . $fetch_details['country'] . '</p>
        <p><strong>Pincode:</strong> ' . $fetch_details['pincode'] . '</p>
    </div>
    <hr />
    <table class="invoice-items">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Book Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

$select_book = mysqli_query($conn, "SELECT * FROM `orders` WHERE id = '$order_id'") or die('query failed');
$s = 1;
$total_price = 0;
if (mysqli_num_rows($select_book) > 0) {
    while ($fetch_book = mysqli_fetch_assoc($select_book)) {
        $total_price += $fetch_book['sub_total'];
        $html .= '<tr>
                <td>' . $s . '</td>
                <td>' . $fetch_book['book'] . '</td>
                <td>' . $fetch_book['quantity'] . '</td>
                <td>' . $fetch_book['unit_price'] . '</td>
                <td>' . $fetch_book['sub_total'] . '</td>
            </tr>';
        $s++;
    }
}

$html .= '</tbody>
    </table>
    <div class="invoice-total">
        <table>
            <tr>
                <th colspan="4">NET TOTAL</th>
                <td>' . $total_price . '</td>
            </tr>
        </table>
    </div>
    <div class="invoice-footer">
        <p><strong>Thank you for your purchase!</strong></p>
    </div>
</div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('invoice', array('Attachment' => 0));
?>