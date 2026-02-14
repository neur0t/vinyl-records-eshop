<?php
require_once 'functions.php';
include 'header.php';

$categoryId = isset($_GET['category']) ? intval($_GET['category']) : null;
$categories = getCategories();
$products = getProducts($categoryId);
?>

<aside class="categories">
  <h3>Κατηγορίες</h3>
  <ul>
    <li><a href="shop.php">Όλα</a></li>
    <?php foreach ($categories as $cat): ?>
      <li><a href="shop.php?category=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
    <?php endforeach; ?>
  </ul>
</aside>

<section class="products">
  <?php if (empty($products)): ?>
    <p>Δεν βρέθηκαν προϊόντα.</p>
  <?php else: ?>
    <?php foreach ($products as $p): ?>
      <div class="product">
        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
        <h3><?php echo htmlspecialchars($p['name']); ?></h3>
        <p><?php echo htmlspecialchars($p['description']); ?></p>
        <div class="price"><?php echo formatPrice($p['price']); ?></div>

        <form method="post" action="add_to_cart.php">
          <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
          <label>Ποσότητα: <input type="number" name="quantity" value="1" min="1"></label>
          <button type="submit">Προσθήκη στο καλάθι</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
