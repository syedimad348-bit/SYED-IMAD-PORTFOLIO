<?php
require 'db.php';
if(session_status()==PHP_SESSION_NONE){ session_start(); }
if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

if(isset($_POST['add'])) {
  $n = $conn->real_escape_string($_POST['name']);
  $ph = $conn->real_escape_string($_POST['phone']);
  $em = $conn->real_escape_string($_POST['email']);
  $conn->query("INSERT INTO customers (Name,Phone,Email) VALUES ('$n','$ph','$em')");
  $msg = "Customer added successfully";
}

$rows = $conn->query("SELECT * FROM customers ORDER BY CreatedAt DESC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Customers - Syed Imad POS</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Roboto:wght@400;500&display=swap');

body {
    margin: 0;
    font-family: "Roboto", sans-serif;
    background: #0d0d0d;
    color: white;
}

/* HEADER */
header {
    width: 100%;
    background: #000;
    padding: 18px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f8d34f;
    box-shadow: 0 0 22px rgba(248,211,79,0.35);
}
header .logo {
    font-size: 26px;
    color: #f8d34f;
    font-weight: 700;
    font-family: "Poppins";
    text-shadow: 0 0 10px rgba(248,211,79,0.4);
}
header button {
    padding: 10px 18px;
    background: #f8d34f;
    color: #000;
    font-weight: bold;
    border-radius: 10px;
    cursor: pointer;
    transition: .2s;
}
header button:hover { transform: scale(1.07); }

/* SIDEBAR */
.layout { display: flex; min-height: 100vh; }
.sidebar {
    width: 230px;
    background: #0b0b0b;
    padding: 20px 10px;
    border-right: 1px solid #f8d34f33;
}
.sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    margin-bottom: 10px;
    border-radius: 10px;
    color: #f8d34f;
    font-family: "Poppins";
    font-size: 15px;
    transition: .2s;
}
.sidebar a:hover {
    background: rgba(248,211,79,0.15);
    box-shadow: 0 0 10px rgba(248,211,79,0.4);
}

/* MAIN */
main.main { flex: 1; padding: 30px; font-family: "Poppins"; }

/* CARDS */
.card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(12px);
    padding: 25px;
    border-radius: 18px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.5);
    margin-bottom: 30px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover { transform: translateY(-5px); box-shadow: 0 10px 35px rgba(248,211,79,0.5); }
.card-title {
    font-size: 22px;
    font-weight: bold;
    color: #f8d34f;
    margin-bottom: 20px;
    text-shadow: 0 0 10px #f8d34f, 0 0 20px rgba(248,211,79,0.3);
}

/* FORM */
.grid-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}
.grid-form input {
    padding: 12px 15px;
    border-radius: 12px;
    border: none;
    background: rgba(255,255,255,0.05);
    color: white;
    font-size: 16px;
    box-shadow: inset 0 0 5px rgba(248,211,79,0.3);
    transition: 0.3s ease;
}
.grid-form input:focus {
    outline: none;
    box-shadow: 0 0 10px #f8d34f, inset 0 0 5px rgba(248,211,79,0.5);
}
.grid-form button {
    grid-column: span 1;
    padding: 12px 20px;
    border-radius: 12px;
    border: none;
    background: #f8d34f;
    color: black;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s ease;
}
.grid-form button:hover {
    background: #ffe278;
    box-shadow: 0 6px 20px rgba(248,211,79,0.5);
}

/* NOTICE */
.notice {
    background: rgba(248,211,79,0.2);
    color: #f8d34f;
    padding: 10px 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(248,211,79,0.3);
    font-weight: bold;
}

/* TABLE */
.table-box { overflow-x:auto; }
table {
    width: 100%;
    border-collapse: collapse;
    color: white;
    font-size: 15px;
}
table th, table td { padding: 12px; text-align: left; }
table th { background: rgba(248,211,79,0.2); color: #f8d34f; font-weight: bold; }
table tr:nth-child(even) { background: rgba(255,255,255,0.05); }
table tr:hover { background: rgba(248,211,79,0.1); }

</style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo">Syed Imad POS</div>
    <div>
        <button onclick="location.href='dashboard.php'"><i class="fa-solid fa-chart-line"></i> Graph Dashboard</button>
        <button style="margin-left:10px;" onclick="location.href='logout.php'"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </div>
</header>

<div class="layout">

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="index.php"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="orders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
    <a href="menu.php"><i class="fa-solid fa-burger"></i> Menu</a>
    <a href="customers.php"><i class="fa-solid fa-users"></i> Customers</a>
    <a href="report.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
</div>

<!-- MAIN CONTENT -->
<main class="main">
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-user-plus"></i> Add Customer</div>
        <?php if(isset($msg)) echo "<div class='notice'>$msg</div>"; ?>
        <form method="POST" class="grid-form">
            <input name="name" placeholder="Name" required>
            <input name="phone" placeholder="Phone">
            <input name="email" placeholder="Email" type="email">
            <button name="add"><i class="fa-solid fa-plus"></i> Add Customer</button>
        </form>
    </div>

    <div class="card table-box">
        <div class="card-title"><i class="fa-solid fa-users"></i> All Customers</div>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Created</th>
                </tr>
            </thead>
            <tbody>
            <?php while($r=$rows->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $r['CustomerID']; ?></td>
                    <td><?php echo htmlspecialchars($r['Name']); ?></td>
                    <td><?php echo htmlspecialchars($r['Phone']); ?></td>
                    <td><?php echo htmlspecialchars($r['Email']); ?></td>
                    <td><?php echo $r['CreatedAt']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
</div>

</body>
</html>
