<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: shop.php');
    exit();
}

$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if (!isLoggedIn()) {
    // redirect to login with return
    header('Location: login.php');
    exit();
}

if ($productId > 0) {
    addToCart($productId, $quantity);
}

header('Location: cart.php');
exit();
?>
