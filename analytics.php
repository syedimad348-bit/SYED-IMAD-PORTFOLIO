<?php
require '../db.php';
if (!isset($_SESSION['user'])) { header('Location: ../login.php'); exit; }

$sales = $conn->query("SELECT DATE(OrderDate) AS dt, IFNULL(SUM(TotalAmount),0) AS total FROM Orders GROUP BY DATE(OrderDate) ORDER BY DATE(OrderDate) ASC");
$items = $conn->query("SELECT m.ItemName, IFNULL(SUM(oi.Quantity),0) AS qty, IFNULL(SUM(oi.Subtotal),0) AS revenue FROM OrderItems oi JOIN Menu m ON oi.MenuID=m.MenuID GROUP BY m.MenuID ORDER BY revenue DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Analytics</title><link rel="stylesheet" href="../assets/css/style.css"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head>
<body>
<?php include '../components/topbar.php'; ?>
<div class="layout">
<?php include '../components/sidebar.php'; ?>
<main class="main">
  <div class="container">
    <div class="card"><div class="card-title">Sales Over Time</div><canvas id="repSales"></canvas></div>

    <div class="card"><div class="card-title">Items Revenue</div><canvas id="repItems"></canvas>
      <table><thead><tr><th>Item</th><th>Qty</th><th>Revenue (PKR)</th></tr></thead><tbody>
        <?php while($it=$items->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($it['ItemName']); ?></td>
            <td><?php echo $it['qty']; ?></td>
            <td><?php echo number_format($it['revenue'],2); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody></table>
    </div>
  </div>
</main>
</div>

<script>
const repLabels = [<?php while($r=$sales->fetch_assoc()){ echo "'".$r['dt']."',"; } ?>];
const repData   = [<?php $sales2=$conn->query("SELECT DATE(OrderDate) AS dt, IFNULL(SUM(TotalAmount),0) AS total FROM Orders GROUP BY DATE(OrderDate) ORDER BY DATE(OrderDate) ASC"); while($r=$sales2->fetch_assoc()){ echo $r['total'].','; } ?>];
new Chart(document.getElementById('repSales').getContext('2d'), { type:'line', data:{ labels:repLabels, datasets:[{label:'Sales PKR', data:repData, tension:0.2, backgroundColor:'rgba(123,47,247,0.08)', borderColor:'rgba(123,47,247,0.9)'}] }, options:{responsive:true} });

const itemLabels = [<?php $itq=$conn->query("SELECT m.ItemName, SUM(oi.Quantity) AS qty FROM OrderItems oi JOIN Menu m ON oi.MenuID=m.MenuID GROUP BY m.MenuID ORDER BY qty DESC"); while($b=$itq->fetch_assoc()){ echo "'".addslashes($b['ItemName'])."',"; } ?>];
const itemData   = [<?php $itq2=$conn->query("SELECT m.ItemName, SUM(oi.Quantity) AS qty FROM OrderItems oi JOIN Menu m ON oi.MenuID=m.MenuID GROUP BY m.MenuID ORDER BY qty DESC"); while($b=$itq2->fetch_assoc()){ echo $b['qty'].','; } ?>];
new Chart(document.getElementById('repItems').getContext('2d'), { type:'bar', data:{ labels:itemLabels, datasets:[{label:'Qty Sold', data:itemData, backgroundColor:'rgba(107,42,214,0.85)'}] }, options:{responsive:true} });
</script>
</body>
</html>
