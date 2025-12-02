<?php
// header.php - include after session check in pages
?>
<header class="topbar">
  <div class="top-left">
    <h2 class="brand">Syed Imad POS</h2>
  </div>
  <div class="top-right">
    <span class="user">Logged in: <?php echo htmlspecialchars($_SESSION['user'] ?? ''); ?></span>
    <a class="btn-logout" href="logout.php">Logout</a>
  </div>
</header>
