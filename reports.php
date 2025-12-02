<?php
require 'db.php';
if(session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

// Sales by date
$salesChart = $conn->query("SELECT DATE(OrderDate) AS dt, IFNULL(SUM(TotalAmount),0) AS total 
                            FROM Orders GROUP BY DATE(OrderDate) ORDER BY DATE(OrderDate) ASC");

// Items revenue & qty
$itemsChart = $conn->query("SELECT m.ItemName, IFNULL(SUM(oi.Quantity),0) AS qty, 
                            IFNULL(SUM(oi.Subtotal),0) AS revenue 
                            FROM OrderItems oi 
                            JOIN Menu m ON oi.MenuID=m.MenuID 
                            GROUP BY m.MenuID ORDER BY revenue DESC");

// Prepare arrays for charts
$salesLabelsArr = [];
$salesDataArr = [];
while($row = $salesChart->fetch_assoc()){
    $salesLabelsArr[] = $row['dt'];
    $salesDataArr[] = $row['total'];
}

$itemsChartLabels = [];
$itemsChartData = [];
while($row = $itemsChart->fetch_assoc()){
    $itemsChartLabels[] = $row['ItemName'];
    $itemsChartData[] = $row['qty'];
}

// For table display
$itemsTable = $conn->query("SELECT m.ItemName, IFNULL(SUM(oi.Quantity),0) AS qty, 
                            IFNULL(SUM(oi.Subtotal),0) AS revenue 
                            FROM OrderItems oi 
                            JOIN Menu m ON oi.MenuID=m.MenuID 
                            GROUP BY m.MenuID ORDER BY revenue DESC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reports - Syed Imad POS</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Roboto:wght@400;500&display=swap');

html, body {
    margin:0; padding:0; width:100%; height:100%; overflow:hidden;
    font-family:'Roboto',sans-serif;
    background:#0a0a0a;
    color:white;
}

/* Canvas Background */
#bgCanvas { position:absolute; top:0; left:0; width:100%; height:100%; z-index:0; }

/* HEADER */
header {
    width:100%;
    background:#000;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #f8d34f;
    box-shadow:0 0 25px rgba(248,211,79,0.35);
    position:relative; z-index:2;
}
header .logo {
    font-size:28px;
    font-weight:700;
    color:#f8d34f;
    text-shadow:0 0 12px rgba(248,211,79,0.4);
    font-family:'Poppins';
}
header button {
    padding:10px 18px;
    background:#f8d34f;
    color:#000;
    font-weight:bold;
    border-radius:10px;
    cursor:pointer;
    margin-left:10px;
    border:none;
    transition:.2s;
}
header button:hover { transform:scale(1.07); }

/* SIDEBAR */
.layout { display:flex; position:relative; z-index:2; }
.sidebar {
    width:230px;
    background:#0b0b0b;
    padding:20px 10px;
    border-right:1px solid #f8d34f33;
}
.sidebar a {
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 16px;
    margin-bottom:10px;
    border-radius:10px;
    color:#f8d34f;
    font-family:'Poppins';
    font-size:15px;
    transition:.25s ease;
    position:relative;
    text-decoration:none;
}
.sidebar a:hover {
    background: rgba(248,211,79,0.18);
    box-shadow:0 0 12px rgba(248,211,79,0.35);
}
.sidebar a::before {
    content:"";
    position:absolute;
    left:0; top:0;
    height:100%;
    width:0;
    background:#f8d34f;
    border-radius:10px 0 0 10px;
    transition:.3s ease;
}
.sidebar a:hover::before { width:6px; }

/* MAIN AREA */
main.main {
    flex:1;
    padding:30px;
    font-family:'Poppins';
    position:relative; z-index:2;
}

/* CARDS */
.card {
    background: rgba(255,255,255,0.05);
    backdrop-filter:blur(12px);
    padding:25px;
    border-radius:18px;
    margin-bottom:25px;
    border:1px solid #f8d34f22;
    transition:.3s;
    position:relative;
    overflow:hidden;
}
.card:hover { box-shadow:0 0 20px rgba(248,211,79,0.35); border-color:#f8d34f55; }
.card::after {
    content:"";
    position:absolute;
    top:0; left:-150%;
    width:80%; height:100%;
    background:linear-gradient(120deg, transparent, rgba(248,211,79,0.25), transparent);
    transform:skewX(-20deg);
    transition:.6s;
}
.card:hover::after { left:150%; }

.card-title {
    font-size:22px;
    font-weight:700;
    color:#f8d34f;
    margin-bottom:18px;
    text-shadow:0 0 10px rgba(248,211,79,0.4);
}

/* TABLE */
.table-box { overflow-x:auto; margin-top:15px; }
table {
    width:100%;
    border-collapse:collapse;
    color:white;
    font-size:15px;
}
table th, table td { padding:12px; text-align:left; }
table th {
    background: rgba(248,211,79,0.2);
    color:#f8d34f;
    font-weight:700;
}
table tr:nth-child(even) { background: rgba(255,255,255,0.04); }
table tr:hover { background: rgba(248,211,79,0.12); cursor:pointer; }

/* CANVAS CHART STYLE */
canvas {
    background: rgba(0,0,0,0.35);
    padding:15px;
    border-radius:12px;
    box-shadow: inset 0 0 16px rgba(248,211,79,0.22);
}
</style>
</head>

<body>

<canvas id="bgCanvas"></canvas>

<header>
    <div class="logo">
        <i class="fa-solid fa-chart-line"></i> Reports
    </div>
    <div>
        <button onclick="location.href='dashboard.php'"><i class="fa-solid fa-house"></i> Dashboard</button>
        <button onclick="location.href='logout.php'"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
    </div>
</header>

<div class="layout">

<div class="sidebar">
    <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="orders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
    <a href="menu.php"><i class="fa-solid fa-burger"></i> Menu</a>
    <a href="customers.php"><i class="fa-solid fa-users"></i> Customers</a>
    <a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
</div>

<main class="main">

<div class="card">
    <div class="card-title"><i class="fa-solid fa-chart-line"></i> Sales Over Time</div>
    <canvas id="repSales"></canvas>
</div>

<div class="card">
    <div class="card-title"><i class="fa-solid fa-burger"></i> Items Revenue</div>
    <canvas id="repItems"></canvas>

    <div class="table-box">
        <table>
            <thead>
                <tr><th>Item</th><th>Quantity</th><th>Revenue (PKR)</th></tr>
            </thead>
            <tbody>
            <?php while($it=$itemsTable->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($it['ItemName']); ?></td>
                    <td><?php echo $it['qty']; ?></td>
                    <td><?php echo number_format($it['revenue'],2); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</main>
</div>

<script>
const salesLabels = <?php echo json_encode($salesLabelsArr); ?>;
const salesData = <?php echo json_encode($salesDataArr); ?>;

const itemLabels = <?php echo json_encode($itemsChartLabels); ?>;
const itemData = <?php echo json_encode($itemsChartData); ?>;

new Chart(document.getElementById('repSales'), {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets:[{
            label:"Sales (PKR)",
            data: salesData,
            borderColor:"#f8d34f",
            borderWidth:3,
            pointRadius:5,
            pointBackgroundColor:"#f8d34f",
            pointHoverRadius:8,
            tension:0.25
        }]
    },
    options:{
        responsive:true,
        plugins:{ tooltip:{ callbacks:{ label:(ctx)=>`PKR ${ctx.raw.toLocaleString()}` } } },
        scales:{ y:{ ticks:{ color:"white" } }, x:{ ticks:{ color:"white" } } }
    }
});

new Chart(document.getElementById('repItems'), {
    type:'bar',
    data:{
        labels:itemLabels,
        datasets:[{
            label:"Qty Sold",
            data:itemData,
            backgroundColor:"#f8d34f",
            borderRadius:6
        }]
    },
    options:{
        responsive:true,
        plugins:{ tooltip:{ callbacks:{ label:(ctx)=>`${ctx.raw} sold` } } },
        scales:{ y:{ ticks:{ color:"white" } }, x:{ ticks:{ color:"white" } } }
    }
});

// Background cubes animation
const canvas = document.getElementById('bgCanvas');
const ctx = canvas.getContext('2d');
let w=canvas.width=window.innerWidth;
let h=canvas.height=window.innerHeight;
window.addEventListener('resize',()=>{ w=canvas.width=window.innerWidth; h=canvas.height=window.innerHeight; });
const mouse={x:w/2, y:h/2};
window.addEventListener('mousemove', e=>{ mouse.x=e.clientX; mouse.y=e.clientY; });

const cubes=[];
const cubeCount=120;
const colors=['#f8d34f','#ffe278','#ffcc00','#fff','#ffea7f'];
for(let i=0;i<cubeCount;i++){
    cubes.push({
        x:Math.random()*w,
        y:Math.random()*h,
        size: Math.random()*8+4,
        dx:(Math.random()-0.5)*1.5,
        dy:(Math.random()-0.5)*1.5,
        color: colors[Math.floor(Math.random()*colors.length)],
        angle: Math.random()*360,
        rotationSpeed: (Math.random()-0.5)*0.02
    });
}

function drawCube(c){
    ctx.save();
    ctx.translate(c.x,c.y);
    ctx.rotate(c.angle);
    ctx.fillStyle=c.color;
    ctx.fillRect(-c.size/2,-c.size/2,c.size,c.size);
    ctx.restore();
}

function animate(){
    ctx.clearRect(0,0,w,h);
    const grd=ctx.createLinearGradient(0,0,w,h);
    grd.addColorStop(0,'#111'); grd.addColorStop(0.5,'#0a0a0a'); grd.addColorStop(1,'#111');
    ctx.fillStyle=grd; ctx.fillRect(0,0,w,h);

    for(let i=0;i<cubes.length;i++){
        const c=cubes[i];
        let dx=mouse.x-c.x, dy=mouse.y-c.y, dist=Math.sqrt(dx*dx+dy*dy);
        if(dist<200){ c.x += dx*0.02; c.y += dy*0.02; }
        c.x+=c.dx; c.y+=c.dy; c.angle+=c.rotationSpeed;
        if(c.x<0||c.x>w) c.dx*=-1;
        if(c.y<0||c.y>h) c.dy*=-1;
        drawCube(c);

        for(let j=i+1;j<cubes.length;j++){
            const c2=cubes[j];
            let dxl=c.x-c2.x, dyl=c.y-c2.y, d=Math.sqrt(dxl*dxl+dyl*dyl);
            if(d<120){
                ctx.beginPath();
                ctx.moveTo(c.x,c.y); ctx.lineTo(c2.x,c2.y);
                ctx.strokeStyle=`rgba(248,211,79,${1-d/120})`;
                ctx.lineWidth=1;
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(animate);
}
animate();
</script>

</body>
</html>
