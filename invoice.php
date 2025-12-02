<?php
require 'db.php';
if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$order_id = intval($_GET['order_id']);
$order = $conn->query("SELECT o.OrderID,o.OrderDate,o.TotalAmount,c.Name,c.Phone,c.Email
                       FROM Orders o
                       LEFT JOIN Customers c ON o.CustomerID=c.CustomerID
                       WHERE o.OrderID=$order_id")->fetch_assoc();

$items = $conn->query("SELECT m.ItemName, oi.Quantity, oi.Subtotal
                       FROM OrderItems oi
                       JOIN Menu m ON oi.MenuID=m.MenuID
                       WHERE oi.OrderID=$order_id");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice #<?php echo $order['OrderID']; ?></title>
<style>
body { font-family:'Poppins',sans-serif; background:#f5f5f5; color:#000; padding:20px; }
.invoice-box { max-width:800px; margin:auto; background:white; padding:30px; border-radius:15px; box-shadow:0 0 20px rgba(0,0,0,0.15); }
h1,h2,h3,h4,h5,h6 { margin:0; }
.invoice-box table { width:100%; border-collapse:collapse; margin-top:20px; }
.invoice-box table th, .invoice-box table td { padding:12px; border-bottom:1px solid #ddd; text-align:left; }
.invoice-box table th { background:#f8d34f; color:#000; }
.total { text-align:right; font-weight:bold; font-size:18px; margin-top:15px; }
.btn { padding:10px 18px; background:#f8d34f; color:black; border:none; border-radius:8px; font-weight:bold; cursor:pointer; margin-top:20px; }
.btn:hover { background:#ffe278; }
@media print {
    .btn { display:none; }
    body { background:#fff; padding:0; }
}
</style>
</head>
<body>

<div class="invoice-box">
    <h2>Invoice #<?php echo $order['OrderID']; ?></h2>
    <p><strong>Date:</strong> <?php echo $order['OrderDate']; ?></p>
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['Name']); ?> | <?php echo htmlspecialchars($order['Phone']); ?></p>
    <?php if($order['Email']): ?><p><strong>Email:</strong> <?php echo htmlspecialchars($order['Email']); ?></p><?php endif; ?>

    <table>
        <thead>
            <tr><th>Item</th><th>Quantity</th><th>Subtotal (PKR)</th></tr>
        </thead>
        <tbody>
            <?php while($it=$items->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($it['ItemName']); ?></td>
                <td><?php echo $it['Quantity']; ?></td>
                <td><?php echo number_format($it['Subtotal'],2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total">Total: PKR <?php echo number_format($order['TotalAmount'],2); ?></div>

    <button class="btn" onclick="window.print();"><i class="fa-solid fa-print"></i> Print Invoice</button>
</div>

</body>
</html>
