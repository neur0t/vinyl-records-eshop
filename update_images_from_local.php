<?php
/**
 * update_images_from_local.php
 *
 * Scans the local `assets/images/` folder for image files and attempts to
 * match them to products by slugified name. Run in the browser to preview
 * suggested mappings. To apply changes, open with `?run=1`.
 *
 * This script does NOT download images; it simply updates the `products.image_url`
 * column to point to the chosen local file path (relative to the webroot).
 */

require_once 'config.php';

function slugify($s) {
    $s = mb_strtolower($s, 'UTF-8');
    $s = preg_replace('/[^a-z0-9\s-α-ωάέίόύήώϊϋΆΈΊΌΎΉΏΑ-ΩΆΈΊΌΎΉΏ]/u', '', $s);
    $s = preg_replace('/[\s]+/u', '-', $s);
    $s = trim($s, '-');
    return $s;
}

$imagesDir = __DIR__ . '/assets/images';
$files = [];
foreach (glob($imagesDir . '/*.{svg,png,jpg,jpeg,gif}', GLOB_BRACE) as $f) {
    $files[] = basename($f);
}

$products = [];
$res = $conn->query("SELECT id, name FROM products");
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}

$candidates = [];
foreach ($products as $p) {
    $pSlug = slugify($p['name']);
    $best = null;
    foreach ($files as $file) {
        $fname = pathinfo($file, PATHINFO_FILENAME);
        $fSlug = slugify($fname);
        if ($fSlug === $pSlug) {
            $best = $file; break;
        }
    }
    if (!$best) {
        // fallback: partial match by checking if all words in product slug appear in filename
        $parts = explode('-', $pSlug);
        foreach ($files as $file) {
            $fname = pathinfo($file, PATHINFO_FILENAME);
            $fSlug = slugify($fname);
            $matchCount = 0;
            foreach ($parts as $part) { if ($part && strpos($fSlug, $part) !== false) $matchCount++; }
            if ($matchCount >= max(1, floor(count($parts)/2))) {
                $best = $file; break;
            }
        }
    }
    $candidates[$p['id']] = ['product' => $p, 'file' => $best];
}

echo "<h2>Local image -> product mapping preview</h2>";
echo "<p>Found " . count($files) . " image files and " . count($products) . " products.</p>";
echo "<table border=1 cellpadding=6 cellspacing=0 style='border-collapse:collapse'><tr><th>Product</th><th>Suggested file</th><th>Action</th></tr>";
foreach ($candidates as $pid => $info) {
    $prod = htmlspecialchars($info['product']['name']);
    $file = $info['file'] ? htmlspecialchars($info['file']) : '<em>no match</em>';
    $path = $info['file'] ? 'assets/images/' . $info['file'] : '';
    $action = $info['file'] ? 'Will set to: <code>' . $path . '</code>' : '';
    echo "<tr><td>$prod</td><td>$file</td><td>$action</td></tr>";
}
echo "</table>";

if (isset($_GET['run']) && $_GET['run'] == '1') {
    $applied = 0;
    foreach ($candidates as $pid => $info) {
        if (!$info['file']) continue;
        $path = 'assets/images/' . $info['file'];
        $pidInt = intval($pid);
        $stmt = $conn->prepare('UPDATE products SET image_url = ? WHERE id = ?');
        $stmt->bind_param('si', $path, $pidInt);
        if ($stmt->execute()) {
            $applied += $stmt->affected_rows;
        }
        $stmt->close();
    }
    echo "<p><strong>Applied changes.</strong> Rows affected: " . $applied . "</p>";
    echo "<p><a href='shop.php'>View shop</a></p>";
}

$conn->close();

?>
