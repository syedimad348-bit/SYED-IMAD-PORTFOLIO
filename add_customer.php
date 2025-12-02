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
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $conn->query("INSERT INTO Customers (Name, Phone, Email) VALUES ('$name', '$phone', '$email')");
    $success = "Customer added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Customer</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
<h2>Add Customer</h2>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<form method="POST">
    <input type="text" name="name" placeholder="Name" required><br><br>
    <input type="text" name="phone" placeholder="Phone"><br><br>
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="submit" name="submit" value="Add Customer">
</form>
</div>
</body>
</html>
