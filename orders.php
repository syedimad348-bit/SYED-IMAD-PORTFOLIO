<?php
require 'db.php';
if(session_status() == PHP_SESSION_NONE){ session_start(); }
if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$msg = '';

if(isset($_POST['place'])) {
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $items = $_POST['menu'] ?? [];
    $qtys = $_POST['qty'] ?? [];

    $result = $conn->query("SELECT CustomerID FROM Customers WHERE Name='$customer_name' LIMIT 1");
    if($result->num_rows > 0) {
        $cid = $result->fetch_assoc()['CustomerID'];
    } else {
        $conn->query("INSERT INTO Customers (Name) VALUES ('$customer_name')");
        $cid = $conn->insert_id;
    }

    $conn->query("INSERT INTO Orders (CustomerID) VALUES ($cid)");
    $oid = $conn->insert_id;
    $total = 0;

    foreach($items as $mid){
        $mid = intval($mid);
        $q = intval($qtys[$mid] ?? 0);
        if($q <= 0) continue;

        $price = $conn->query("SELECT Price FROM Menu WHERE MenuID=$mid")->fetch_assoc()['Price'];
        $sub = $price * $q;
        $total += $sub;

        $conn->query("INSERT INTO OrderItems (OrderID,MenuID,Quantity,Subtotal) VALUES ($oid,$mid,$q,$sub)");
    }

    $conn->query("UPDATE Orders SET TotalAmount=$total WHERE OrderID=$oid");
    $msg = "Order placed successfully (PKR ".number_format($total,2).")";
}

$menu = $conn->query("SELECT * FROM Menu");
$recent = $conn->query("SELECT o.OrderID,o.OrderDate,o.TotalAmount,c.Name 
                        FROM Orders o 
                        LEFT JOIN Customers c ON o.CustomerID=c.CustomerID 
                        ORDER BY o.OrderDate DESC LIMIT 25");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Orders - Syed Imad POS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Roboto:wght@400;500&display=swap');

body {
    margin:0;
    font-family:'Roboto',sans-serif;
    color:white;
    overflow-x:hidden;
    background:#0a0a0a;
    position:relative;
}

/* Canvas Background */
#bgCanvas {
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    z-index:-1;
}

/* HEADER */
header { width:100%; background:#000; padding:18px 30px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #f8d34f; box-shadow:0 0 22px rgba(248,211,79,0.35); z-index:2; position:relative; }
header .logo { font-size:26px; font-weight:700; color:#f8d34f; text-shadow:0 0 10px rgba(248,211,79,0.4); font-family:'Poppins'; }
header button { padding:10px 18px; background:#f8d34f; color:#000; font-weight:bold; border-radius:10px; cursor:pointer; margin-left:10px; transition:.2s; }
header button:hover { transform:scale(1.07); }

/* LAYOUT */
.layout { display:flex; min-height:100vh; position:relative; z-index:1; }

/* SIDEBAR */
.sidebar { width:230px; background:#0b0b0b; padding:20px 10px; border-right:1px solid #f8d34f33; z-index:1; }
.sidebar a { display:flex; align-items:center; gap:12px; padding:12px 16px; margin-bottom:10px; border-radius:10px; color:#f8d34f; font-family:'Poppins'; font-size:15px; transition:.2s; text-decoration:none; }
.sidebar a:hover { background: rgba(248,211,79,0.15); box-shadow:0 0 10px rgba(248,211,79,0.4); }

/* MAIN */
main.main { flex:1; padding:30px; font-family:'Poppins'; position:relative; z-index:2; }

/* CARDS */
.card { background: rgba(255,255,255,0.05); backdrop-filter:blur(12px); padding:25px; border-radius:18px; margin-bottom:25px; border:1px solid #f8d34f22; transition:.3s; }
.card:hover { box-shadow:0 0 18px rgba(248,211,79,0.35); border-color:#f8d34f55; }
.card-title { font-size:22px; font-weight:700; color:#f8d34f; margin-bottom:20px; text-shadow:0 0 10px rgba(248,211,79,0.4); }

/* NOTICE */
.notice { background: rgba(248,211,79,0.2); color:#f8d34f; padding:10px 15px; border-radius:12px; margin-bottom:20px; box-shadow:0 4px 15px rgba(248,211,79,0.3); font-weight:bold; }

/* FORM */
form input { width:100%; padding:12px 15px; margin-bottom:15px; border-radius:12px; border:none; background:rgba(255,255,255,0.05); color:white; font-size:16px; box-shadow: inset 0 0 5px rgba(248,211,79,0.3); transition:0.3s ease; }
form input:focus { outline:none; box-shadow:0 0 10px #f8d34f, inset 0 0 5px rgba(248,211,79,0.5); }

/* MENU GRID */
.menu-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:15px; margin-bottom:20px; }
.menu-item { background: rgba(255,255,255,0.05); padding:12px; border-radius:12px; box-shadow: inset 0 0 5px rgba(248,211,79,0.2); transition:0.3s ease; font-family:'Poppins'; font-weight:500; font-size:16px; display:flex; align-items:center; justify-content:space-between; }
.menu-item:hover { box-shadow: 0 0 12px #f8d34f, 0 0 25px rgba(248,211,79,0.3); transform: translateY(-2px); }
.menu-item input[type="number"] { width:50px; padding:5px; font-size:14px; border-radius:8px; text-align:center; }

/* BUTTON */
.btn-primary { padding:12px 25px; border-radius:12px; border:none; background:#f8d34f; color:black; font-weight:700; font-size:16px; cursor:pointer; transition:.3s; }
.btn-primary:hover { background:#ffe278; box-shadow:0 6px 20px rgba(248,211,79,0.5); }

/* TABLE */
.table-box { overflow-x:auto; }
table { width:100%; border-collapse:collapse; font-size:15px; color:white; font-family:'Roboto'; }
table th, table td { padding:12px; text-align:left; }
table th { background: rgba(248,211,79,0.2); color:#f8d34f; font-weight:700; }
table tr:nth-child(even) { background: rgba(255,255,255,0.05); }
table tr:hover { background: rgba(248,211,79,0.1); }

</style>
</head>
<body>

<canvas id="bgCanvas"></canvas>

<header>
    <div class="logo"><i class="fa-solid fa-cart-shopping"></i> Orders</div>
    <div>
        <button onclick="location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Dashboard</button>
        <button onclick="location.href='logout.php'"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </div>
</header>

<div class="layout">
    <div class="sidebar">
        <a href="index.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="orders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
        <a href="menu.php"><i class="fa-solid fa-burger"></i> Menu</a>
        <a href="customers.php"><i class="fa-solid fa-users"></i> Customers</a>
        <a href="report.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
    </div>

    <main class="main">
        <div class="card">
            <div class="card-title"><i class="fa-solid fa-cart-plus"></i> New Order</div>
            <?php if($msg) echo "<div class='notice'>$msg</div>"; ?>
            <form method="POST">
                <input type="text" name="customer_name" placeholder="Enter Customer Name" required>
                <div class="menu-grid">
                    <?php while($m=$menu->fetch_assoc()): ?>
                        <div class="menu-item">
                            <label>
                                <input type="checkbox" name="menu[]" value="<?php echo $m['MenuID']; ?>"> 
                                <?php echo htmlspecialchars($m['ItemName']); ?> (PKR <?php echo number_format($m['Price'],2); ?>)
                            </label>
                            <input type="number" name="qty[<?php echo $m['MenuID']; ?>]" value="0" min="0" placeholder="Qty">
                        </div>
                    <?php endwhile; ?>
                </div>
                <button name="place" class="btn-primary"><i class="fa-solid fa-check"></i> Place Order</button>
            </form>
        </div>

        <div class="card table-box">
            <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</div>
            <table>
                <thead>
                    <tr><th>ID</th><th>Date</th><th>Customer</th><th>Total (PKR)</th></tr>
                </thead>
                <tbody>
                    <?php while($r=$recent->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $r['OrderID']; ?></td>
                            <td><?php echo $r['OrderDate']; ?></td>
                            <td><?php echo htmlspecialchars($r['Name']); ?></td>
                            <td><?php echo number_format($r['TotalAmount'],2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
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
const colors = ['#f8d34f','#ff6b6b','#6bc1ff','#8aff9f','#ff8aff'];

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
