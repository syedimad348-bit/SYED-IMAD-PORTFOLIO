<?php
require 'db.php';
if(session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

if(isset($_POST['add'])) {
  $n = $conn->real_escape_string($_POST['name']);
  $p = floatval($_POST['price']);
  $conn->query("INSERT INTO menu (ItemName,Price) VALUES ('$n',$p)");
  $msg = "Menu item added successfully";
}

$items = $conn->query("SELECT * FROM menu ORDER BY MenuID ASC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Menu - Syed Imad POS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&family=Roboto:wght@400;500&display=swap');

body {
    margin:0;
    font-family:'Roboto',sans-serif;
    background:#0a0a0a;
    color:white;
    animation: fadePage 0.8s ease-in-out;
}
@keyframes fadePage {
    from { opacity:0; transform:translateY(15px); }
    to   { opacity:1; transform:translateY(0); }
}

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
.layout { display:flex; }
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
    left:0;
    top:0;
    height:100%;
    width:0;
    background:#f8d34f;
    border-radius:10px 0 0 10px;
    transition:.3s ease;
}
.sidebar a:hover::before { width:6px; }

/* MAIN AREA */
main.main { flex:1; padding:30px; font-family:'Poppins'; }

/* CARDS */
.card {
    background: rgba(255,255,255,0.05);
    backdrop-filter:blur(12px);
    padding:25px;
    border-radius:18px;
    margin-bottom:30px;
    border:1px solid #f8d34f22;
    box-shadow:0 6px 25px rgba(0,0,0,0.5);
    transition:.3s;
    position:relative;
    overflow:hidden;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow:0 10px 35px rgba(248,211,79,0.5);
    border-color:#f8d34f55;
}
.card::after {
    content:"";
    position:absolute;
    top:0;
    left:-150%;
    width:80%;
    height:100%;
    background:linear-gradient(120deg, transparent, rgba(248,211,79,0.25), transparent);
    transform:skewX(-20deg);
    transition:.6s;
}
.card:hover::after { left:150%; }

.card-title {
    font-size:22px;
    font-weight:700;
    color:#f8d34f;
    margin-bottom:20px;
    text-shadow:0 0 10px rgba(248,211,79,0.4);
}

/* FORM */
.grid-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}
.grid-form input {
    padding:12px 15px;
    border-radius:12px;
    border:none;
    background: rgba(255,255,255,0.05);
    color:white;
    font-size:16px;
    box-shadow: inset 0 0 5px rgba(248,211,79,0.3);
    transition: 0.3s ease;
}
.grid-form input:focus {
    outline:none;
    box-shadow: 0 0 12px #f8d34f, inset 0 0 5px rgba(248,211,79,0.5);
}
.grid-form button {
    grid-column: span 1;
    padding:12px 20px;
    border-radius:12px;
    border:none;
    background:#f8d34f;
    color:black;
    font-weight:bold;
    font-size:16px;
    cursor:pointer;
    transition:0.3s ease;
}
.grid-form button:hover {
    background:#ffe278;
    box-shadow: 0 6px 20px rgba(248,211,79,0.5);
}

/* NOTICE */
.notice {
    background: rgba(248,211,79,0.2);
    color: #f8d34f;
    padding:10px 15px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(248,211,79,0.3);
    font-weight:bold;
}

/* TABLE */
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
    font-weight:bold;
}
table tr:nth-child(even) { background: rgba(255,255,255,0.05); }
table tr:hover { background: rgba(248,211,79,0.1); cursor:pointer; }

/* SCROLL */
.table-box { overflow-x:auto; }
</style>
</head>
<body>

<?php include 'header.php'; ?>
<div class="layout">
<?php include 'sidebar.php'; ?>

<main class="main">

<div class="card">
    <div class="card-title"><i class="fa-solid fa-plus"></i> Add Menu Item</div>
    <?php if(isset($msg)) echo "<div class='notice'>$msg</div>"; ?>
    <form method="POST" class="grid-form">
        <input name="name" placeholder="Item name" required>
        <input name="price" placeholder="Price (PKR)" required>
        <button name="add"><i class="fa-solid fa-floppy-disk"></i> Add Item</button>
    </form>
</div>

<div class="card table-box">
    <div class="card-title"><i class="fa-solid fa-list"></i> Menu Items</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Price (PKR)</th>
            </tr>
        </thead>
        <tbody>
        <?php while($it=$items->fetch_assoc()): ?>
            <tr>
                <td><?php echo $it['MenuID']; ?></td>
                <td><?php echo htmlspecialchars($it['ItemName']); ?></td>
                <td><?php echo number_format($it['Price'],2); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</main>
</div>

</body>
</html>
