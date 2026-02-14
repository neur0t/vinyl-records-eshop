<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Vinyl Records Store</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>

<header>
  <h1>🎶 Vinyl Records Store</h1>
  <nav>
    <a href="index.php">Αρχική</a>
    <a href="shop.php">Κατάστημα</a>
    <a href="cart.php">Καλάθι</a>
    <?php if (isLoggedIn()):
        $u = getCurrentUser();
    ?>
      <span style="color:#fff;margin-left:10px;">Χαίρετε, <?php echo htmlspecialchars($u['username']); ?></span>
      <a href="logout.php">Αποσύνδεση</a>
    <?php else: ?>
      <a href="login.php">Σύνδεση</a>
    <?php endif; ?>
  </nav>
</header>

<main>
