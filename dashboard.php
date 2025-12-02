<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Restaurant Dashboard</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* GLOBAL */
body {
  margin: 0;
  font-family: "Poppins", sans-serif;
  background: #0a0a0a;
  color: #fff;
  overflow-x: hidden;
  position: relative;
}

/* CANVAS BACKGROUND */
#bgCanvas {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

/* SIDEBAR */
.sidebar {
  width: 230px;
  height: 100vh;
  background: #000;
  border-right: 2px solid #b8860b;
  position: fixed;
  top: 0;
  left: 0;
  padding-top: 30px;
}

.sidebar a {
  display: block;
  padding: 15px 25px;
  color: #d4af37;
  font-size: 17px;
  text-decoration: none;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  transition: 0.3s;
}

.sidebar a:hover {
  background: #1a1a1a;
  color: #fff;
}

/* HEADER */
.header {
  margin-left: 230px;
  height: 70px;
  background: #000;
  border-bottom: 2px solid #b8860b;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 25px;
  font-size: 22px;
  font-weight: 600;
  color: #d4af37;
  position: relative;
  z-index: 2;
}

.logout-btn {
  color: #d4af37;
  text-decoration: none;
  font-size: 17px;
  padding: 8px 15px;
  border: 1px solid #d4af37;
  border-radius: 6px;
  transition: .3s;
}

.logout-btn:hover {
  background: #d4af37;
  color: #000;
}

/* MAIN CONTENT */
.container {
  margin-left: 230px;
  padding: 25px;
  position: relative;
  z-index: 2;
}

.card {
  background: #111;
  padding: 20px;
  border-radius: 15px;
  margin-bottom: 25px;
  border: 1px solid #b8860b;
  box-shadow: 0 0 12px rgba(255,215,0,0.1);
}

.chart-title {
  font-size: 20px;
  margin-bottom: 10px;
  color: #d4af37;
  display: flex;
  align-items: center;
  gap: 10px;
}

canvas {
  max-height: 260px !important; 
}
</style>
</head>
<body>

<canvas id="bgCanvas"></canvas>

<!-- SIDEBAR -->
<div class="sidebar">
  <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
  <a href="orders.php"><i class="fa-solid fa-cart-shopping"></i> Orders</a>
  <a href="menu.php"><i class="fa-solid fa-burger"></i> Menu</a>
  <a href="reports.php"><i class="fa-solid fa-file-lines"></i> Reports</a>
  <a href="customers.php"><i class="fa-solid fa-users"></i> Customers</a>
</div>

<!-- HEADER -->
<div class="header">
  Restaurant Dashboard
  <a href="logout.php" class="logout-btn">Logout</a>
</div>

<!-- MAIN -->
<div class="container">

  <div class="card">
    <div class="chart-title"><i class="fa-solid fa-chart-line"></i> Daily Sales Trend</div>
    <canvas id="chart1"></canvas>
  </div>

  <div class="card">
    <div class="chart-title"><i class="fa-solid fa-burger"></i> Top Selling Items</div>
    <canvas id="chart2"></canvas>
  </div>

  <div class="card">
    <div class="chart-title"><i class="fa-solid fa-chart-pie"></i> Category Share</div>
    <canvas id="chart3"></canvas>
  </div>

  <div class="card">
    <div class="chart-title"><i class="fa-solid fa-money-bill"></i> Revenue Comparison</div>
    <canvas id="chart4"></canvas>
  </div>

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

// ====== Charts ======
const gold = 'rgba(212,175,55, 0.8)';
const goldSoft = 'rgba(212,175,55, 0.4)';
const white = 'rgba(255,255,255,0.8)';
const red = 'rgba(255,120,120,0.8)';

/* 1 — DAILY SALES */
new Chart(chart1, {
  type: 'line',
  data: {
    labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    datasets: [{
      label: 'Sales',
      data: [1200,1700,1400,2000,2500,2800,2300],
      borderColor: gold,
      backgroundColor: goldSoft,
      tension: 0.3,
      borderWidth: 2
    }]
  },
  options: { responsive: true }
});

/* 2 — TOP SELLING */
new Chart(chart2, {
  type: 'bar',
  data: {
    labels: ['Pizza','Burger','Wrap','Fries','Biryani'],
    datasets: [{
      label: 'Sold',
      data: [85,120,50,150,90],
      backgroundColor: gold
    }]
  }
});

/* 3 — CATEGORY SHARE */
new Chart(chart3, {
  type: 'pie',
  data: {
    labels: ['Fast Food','Drinks','Desserts'],
    datasets: [{
      data: [45,30,25],
      backgroundColor: [gold, white, red]
    }]
  }
});

/* 4 — REVENUE COMPARISON */
new Chart(chart4, {
  type: 'bar',
  data: {
    labels: ['Week 1','Week 2','Week 3','Week 4'],
    datasets: [{
      label: 'Revenue',
      data: [15000,18000,17000,22500],
      backgroundColor: gold
    }]
  }
});
</script>

</body>
</html>
