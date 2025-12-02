<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';
if(isset($_POST['submit'])){
    $customer_id = $_POST['customer'];
    $items = $_POST['menu'];
    $quantities = $_POST['quantity'];

    $conn->query("INSERT INTO Orders (CustomerID) VALUES ($customer_id)");
    $order_id = $conn->insert_id;
    $total = 0;

    for($i=0;$i<count($items);$i++){
        $menu_id=$items[$i];
        $qty=$quantities[$i];
        if($qty>0){
            $price = $conn->query("SELECT Price FROM Menu WHERE MenuID=$menu_id")->fetch_assoc()['Price'];
            $subtotal = $price*$qty;
            $total += $subtotal;
            $conn->query("INSERT INTO OrderItems (OrderID, MenuID, Quantity, Subtotal) VALUES ($order_id,$menu_id,$qty,$subtotal)");
        }
    }
    $conn->query("UPDATE Orders SET TotalAmount=$total WHERE OrderID=$order_id");
    $success = "Order placed! Total: PKR $total";
}
$customers = $conn->query("SELECT * FROM Customers ORDER BY Name");
$menu = $conn->query("SELECT * FROM Menu");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>New Order</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
<h2>New Order</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<form method="POST">
<label>Customer:</label><br>
<select name="customer" required>
<option value="">Select Customer</option>
<?php while($c=$customers->fetch_assoc()){ echo "<option value='{$c['CustomerID']}'>{$c['Name']}</option>"; } ?>
</select><br><br>

<label>Menu Items:</label><br>
<?php while($m=$menu->fetch_assoc()){ ?>
<div>
<input type="checkbox" name="menu[]" value="<?php echo $m['MenuID']; ?>">
<?php echo $m['ItemName'] . " (PKR " . $m['Price'] . ")"; ?>
Qty: <input type="number" name="quantity[]" value="0" min="0" style="width:50px;">
</div>
<?php } ?>
<br>
<input type="submit" name="submit" value="Place Order">
</form>
</div>
</body>
</html>
