<?php
require_once 'functions.php';

// If already logged in, redirect
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $userId = registerUser($username, $email, $password);
    if ($userId) {
        $_SESSION['user_id'] = $userId;
        redirect('index.php');
    } else {
        $error = 'Registration failed — username or email may already exist, or fields are invalid.';
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<main class="auth">
  <h2>Εγγραφή</h2>
  <?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>
  <form method="post" action="register.php">
    <label>Username</label>
    <input name="username" required>

    <label>Email</label>
    <input name="email" type="email" required>

    <label>Password</label>
    <input name="password" type="password" required>

    <button type="submit">Register</button>
  </form>
  <p>Έχετε ήδη λογαριασμό? <a href="login.php">Σύνδεση</a></p>
</main>
</body>
</html>
