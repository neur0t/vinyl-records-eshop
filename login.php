<?php
require_once 'functions.php';

// If already logged in, redirect
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['identifier'] ?? '';
    $password = $_POST['password'] ?? '';

    $userId = authenticateUser($identifier, $password);
    if ($userId) {
        $_SESSION['user_id'] = $userId;
        redirect('index.php');
    } else {
        $error = 'Login failed — invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<main class="auth">
  <h2>Σύνδεση</h2>
  <?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="post" action="login.php">
    <label>Username or Email</label>
    <input name="identifier" required>

    <label>Password</label>
    <input name="password" type="password" required>

    <button type="submit">Login</button>
  </form>
  <p>Δεν έχετε λογαριασμό? <a href="register.php">Εγγραφή</a></p>
</main>
</body>
</html>
