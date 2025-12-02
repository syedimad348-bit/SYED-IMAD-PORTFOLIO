<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<style>
/* Overlay sidebar */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: -250px; /* hidden by default */
    width: 250px;
    height: 100%;
    background: rgba(0,0,0,0.95);
    backdrop-filter: blur(12px);
    box-shadow: 2px 0 25px rgba(248,211,79,0.5);
    border-radius: 0 20px 20px 0;
    padding: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 20px;
    transition: left 0.3s ease;
}

/* When active (open) */
.sidebar-overlay.active {
    left: 0;
}

/* Toggle button (icon only) */
.sidebar-toggle {
    position: fixed;
    top: 20px;
    left: 20px;
    width: 40px;
    height: 40px;
    background: #f8d34f;
    color: black;
    font-size: 24px;
    border-radius: 50%;
    text-align: center;
    line-height: 40px;
    cursor: pointer;
    box-shadow: 0 0 10px #f8d34f, 0 0 15px rgba(248,211,79,0.5);
    z-index: 10000;
}

/* Header */
.sidebar-overlay .sidebar-header h2 {
    font-size: 18px;
    color: #f8d34f;
    margin: 0;
}

.sidebar-overlay .sidebar-header p {
    font-size: 12px;
    color: #ccc;
}

/* Links */
.sidebar-overlay a {
    display: block;
    color: white;
    font-size: 16px;
    text-decoration: none;
    padding: 10px 8px;
    border-radius: 10px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.sidebar-overlay a:hover {
    color: #f8d34f;
    box-shadow: 0 0 12px #f8d34f, 0 0 20px rgba(248,211,79,0.5);
}

.sidebar-overlay a.active {
    background: rgba(248,211,79,0.2);
    color: black;
    box-shadow: 0 0 10px #f8d34f;
}

/* Logout */
.sidebar-overlay .logout {
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.2);
    font-size: 14px;
    color: #f8d34f;
    cursor: pointer;
    transition: 0.3s ease;
}

.sidebar-overlay .logout:hover {
    color: #ffe278;
    text-shadow: 0 0 10px #f8d34f;
}
</style>

<!-- Toggle button -->
<div class="sidebar-toggle" onclick="toggleSidebar()">☰</div>

<!-- Sidebar -->
<div class="sidebar-overlay" id="sidebar">
    <div class="sidebar-header">
        <h2>Syed Imad POS</h2>
        <p>Logged in: <?php echo $_SESSION['user'] ?? 'Guest'; ?></p>
    </div>

    <a href="index.php" class="active">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="menu.php">Menu</a>
    <a href="orders.php">Orders</a>
    <a href="reports.php">Reports</a>

    <div class="logout" onclick="location.href='logout.php'">Logout</div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
</script>
