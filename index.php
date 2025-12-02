<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

require 'db.php';

// SUMMARY CARDS
$total_customers = $conn->query("SELECT COUNT(*) AS c FROM customers")->fetch_assoc()['c'];
$total_orders = $conn->query("SELECT COUNT(*) AS o FROM orders")->fetch_assoc()['o'];
$total_sales = $conn->query("SELECT IFNULL(SUM(TotalAmount),0) AS t FROM orders")->fetch_assoc()['t'];
$total_items_sold = $conn->query("SELECT IFNULL(SUM(Quantity),0) AS q FROM orderitems")->fetch_assoc()['q'];

// RECENT ORDERS
$recent_orders = $conn->query("
    SELECT 
        o.OrderID,
        o.OrderDate,
        c.Name AS CustomerName,
        o.TotalAmount
    FROM orders o
    LEFT JOIN customers c ON o.CustomerID = c.CustomerID
    ORDER BY o.OrderDate DESC
    LIMIT 10
");

// BEST SELLING MENU ITEMS
$best_items = $conn->query("
    SELECT 
        m.ItemName,
        SUM(oi.Quantity) AS total_sold,
        SUM(oi.Subtotal) AS total_earned
    FROM orderitems oi
    JOIN menu m ON oi.MenuID = m.MenuID
    GROUP BY m.MenuID
    ORDER BY total_sold DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Syed Imad POS Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Roboto:wght@400;500&display=swap');

/* BODY & BACKGROUND */
body {
    margin: 0;
    background: #0d0d0d;
    font-family: "Roboto", sans-serif;
    color: white;
    position: relative;
    overflow-x: hidden;
}

/* ANIMATED BACKGROUND */
#bgCanvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
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
    position: relative;
    z-index: 2;
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
header button:hover {
    transform: scale(1.07);
}

/* SIDEBAR */
.sidebar {
    width: 230px;
    background: #0b0b0b;
    min-height: 100vh;
    padding: 20px 10px;
    border-right: 1px solid #f8d34f33;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 2;
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

/* MAIN CONTENT */
.layout { display: flex; }

.main {
    flex: 1;
    padding: 30px;
    margin-left: 230px;
    font-family: "Poppins";
    position: relative;
    z-index: 2;
}

/* CARDS */
.cards {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    padding: 25px;
    border-radius: 18px;
    border: 1px solid #f8d34f22;
    min-width: 240px;
    flex: 1;
    transition: .3s;
}
.card:hover {
    box-shadow: 0 0 18px rgba(248,211,79,0.35);
    transform: translateY(-4px);
}

.card-title {
    font-size: 18px;
    color: #f8d34f;
    margin-bottom: 12px;
}
.card-value {
    font-size: 32px;
    font-weight: bold;
}

/* TABLES */
.table-box {
    margin-top: 25px;
    padding: 20px;
    border-radius: 18px;
    background: rgba(255,255,255,0.05);
    border: 1px solid #f8d34f33;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    padding: 12px;
    background: rgba(248,211,79,0.2);
    color: #f8d34f;
}
td {
    padding: 12px;
}
tr:nth-child(even) { background: rgba(255,255,255,0.05); }
</style>
</head>

<body>

<canvas id="bgCanvas"></canvas>

<!-- HEADER -->
<header>
    <div class="logo">Syed Imad POS</div>
    <div>
        <button onclick="location.href='dashboard.php'">Graph Dashboard</button>
        <button style="margin-left:10px;" onclick="location.href='logout.php'">Logout</button>
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
</div>

<!-- MAIN PAGE -->
<main class="main">

<h1 style="color:#f8d34f;">Dashboard Overview</h1>

<!-- Summary Cards -->
<div class="cards">
    <div class="card">
        <div class="card-title">Total Customers</div>
        <div class="card-value"><?php echo number_format($total_customers); ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Orders</div>
        <div class="card-value"><?php echo number_format($total_orders); ?></div>
    </div>

    <div class="card">
        <div class="card-title">Total Sales (PKR)</div>
        <div class="card-value"><?php echo number_format($total_sales); ?></div>
    </div>

    <div class="card">
        <div class="card-title">Items Sold</div>
        <div class="card-value"><?php echo number_format($total_items_sold); ?></div>
    </div>
</div>

<!-- Latest Orders -->
<div class="table-box">
    <h2 style="color:#f8d34f;">Latest Orders</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total (PKR)</th>
        </tr>
        <?php while($row = $recent_orders->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['OrderID']; ?></td>
            <td><?php echo $row['CustomerName'] ?? "Unknown"; ?></td>
            <td><?php echo $row['OrderDate']; ?></td>
            <td><?php echo number_format($row['TotalAmount']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Top Selling Items -->
<div class="table-box">
    <h2 style="color:#f8d34f;">Top Selling Items</h2>
    <table>
        <tr>
            <th>Item</th>
            <th>Sold</th>
            <th>Revenue (PKR)</th>
        </tr>
        <?php while($item = $best_items->fetch_assoc()): ?>
        <tr>
            <td><?php echo $item['ItemName']; ?></td>
            <td><?php echo $item['total_sold']; ?></td>
            <td><?php echo number_format($item['total_earned']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</main>
</div>

<script>
// ====== Animated Background ======
const canvas = document.getElementById('bgCanvas');
const ctx = canvas.getContext('2d');
let w = canvas.width = window.innerWidth;
let h = canvas.height = window.innerHeight;

let cubes = [];
const colors = ['#b8860b','#ff6b6b','#6bc1ff','#8aff9f','#ff8aff'];

for(let i=0;i<100;i++){
    cubes.push({
        x: Math.random()*w,
        y: Math.random()*h,
        size: Math.random()*20+5,
        speed: Math.random()*1.2+0.2,
        color: colors[Math.floor(Math.random()*colors.length)]
    });
}

window.addEventListener('resize', ()=>{ w=canvas.width=window.innerWidth; h=canvas.height=window.innerHeight; });

let mouse = {x:w/2, y:h/2};
window.addEventListener('mousemove', e=>{ mouse.x=e.clientX; mouse.y=e.clientY; });

function animate(){
    ctx.clearRect(0,0,w,h);
    cubes.forEach(c=>{
        c.x += (mouse.x-w/2)/100*c.speed;
        c.y += (mouse.y-h/2)/100*c.speed;
        if(c.x> w) c.x=0;
        if(c.x<0) c.x=w;
        if(c.y> h) c.y=0;
        if(c.y<0) c.y=h;
        ctx.fillStyle=c.color;
        ctx.fillRect(c.x,c.y,c.size,c.size);
    });
    requestAnimationFrame(animate);
}
animate();
</script>

</body>
</html>
