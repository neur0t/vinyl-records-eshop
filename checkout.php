<?php
require_once 'functions.php';
include 'header.php';

if (!isLoggedIn()) {
  echo '<p>Παρακαλώ <a href="login.php">συνδεθείτε</a> για να ολοκληρώσετε την αγορά.</p>';
  include 'footer.php';
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cardNumber = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
  $cardLast4 = substr($cardNumber, -4);
  $expiry = $_POST['expiry'] ?? '';
  $cvv = $_POST['cvv'] ?? '';
  $shippingAddress = $_POST['shipping_address'] ?? '';

  // In real app: validate card and process payment via gateway
  $orderId = createOrder($shippingAddress, 'card');
  if ($orderId) {
    // Insert a simple payment record
    $stmt = $conn->prepare("INSERT INTO payments (order_id, card_holder_name, card_last_four, amount, status) VALUES (?, ?, ?, ?, 'completed')");
    if ($stmt) {
      $user = getCurrentUser();
      $name = $user['first_name'] . ' ' . $user['last_name'];
      $amount = getCartTotal();
      $stmt->bind_param('issd', $orderId, $name, $cardLast4, $amount);
      $stmt->execute();
      $stmt->close();
    }

    echo '<p>Ευχαριστούμε! Η παραγγελία σας δημιουργήθηκε (ID: ' . intval($orderId) . ').</p>';
    echo '<p><a href="index.php">Πίσω στην αρχική</a></p>';
    include 'footer.php';
    exit();
  } else {
    echo '<p>Σφάλμα κατά τη δημιουργία της παραγγελίας. Προσπαθήστε ξανά.</p>';
  }
}

?>

<h2>Στοιχεία Πληρωμής</h2>

<form method="post">
  <label>Αριθμός Κάρτας</label>
  <input type="text" name="card_number" required>

  <label>Ημερομηνία Λήξης (MM/YY)</label>
  <input type="text" name="expiry" required>

  <label>CVV</label>
  <input type="text" name="cvv" required>

  <label>Διεύθυνση Αποστολής</label>
  <textarea name="shipping_address" required></textarea>

  <button type="submit">Πληρωμή (εικονική)</button>
</form>

<?php include 'footer.php'; ?>
