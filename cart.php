<?php
require_once 'functions.php';
include 'header.php';

if (!isLoggedIn()) {
		echo '<p>Παρακαλώ <a href="login.php">συνδεθείτε</a> για να δείτε το καλάθι σας.</p>';
		include 'footer.php';
		exit();
}

// Handle quantity update/remove via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST['update']) && isset($_POST['cart_item_id']) && isset($_POST['quantity'])) {
				updateCartQuantity(intval($_POST['cart_item_id']), intval($_POST['quantity']));
		}
		if (isset($_POST['remove']) && isset($_POST['cart_item_id'])) {
				removeFromCart(intval($_POST['cart_item_id']));
		}
		header('Location: cart.php');
		exit();
}

$items = getCartItems();
?>

<h2>Το Καλάθι μου</h2>

<?php if (empty($items)): ?>
	<p>Το καλάθι είναι άδειο.</p>
<?php else: ?>
	<table class="cart-table">
		<thead><tr><th>Προϊόν</th><th>Τιμή</th><th>Ποσότητα</th><th>Σύνολο</th><th></th></tr></thead>
		<tbody>
		<?php foreach ($items as $it): ?>
			<tr>
				<td>
					<img src="<?php echo htmlspecialchars($it['image_url']); ?>" alt="<?php echo htmlspecialchars($it['name']); ?>" style="height:50px;">
					<?php echo htmlspecialchars($it['name']); ?>
				</td>
				<td><?php echo formatPrice($it['price']); ?></td>
				<td>
					<form method="post" style="display:inline-block;">
						<input type="hidden" name="cart_item_id" value="<?php echo $it['id']; ?>">
						<input type="number" name="quantity" value="<?php echo $it['quantity']; ?>" min="1" style="width:60px;">
						<button name="update" type="submit">Αποθήκευση</button>
					</form>
				</td>
				<td><?php echo formatPrice($it['price'] * $it['quantity']); ?></td>
				<td>
					<form method="post">
						<input type="hidden" name="cart_item_id" value="<?php echo $it['id']; ?>">
						<button name="remove" type="submit">Αφαίρεση</button>
					</form>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<p class="cart-total">Σύνολο: <?php echo formatPrice(getCartTotal()); ?></p>
	<a class="checkout-button" href="checkout.php">Ολοκλήρωση αγοράς</a>
<?php endif; ?>

<?php include 'footer.php'; ?>
