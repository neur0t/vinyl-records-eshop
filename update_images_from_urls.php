<?php
/**
 * update_images_from_urls.php
 *
 * Usage:
 * - Edit the $mapping array below and supply for each product name a publicly
 *   accessible image URL (JPG/PNG).
 * - Run in browser: http://localhost/vinyl-records-eshop/update_images_from_urls.php
 *
 * NOTE: This script only updates the `products.image_url` field to point to the
 * provided URL. It does NOT download or embed copyrighted images. You must
 * ensure you have the right to use any image you point to.
 */

require_once 'config.php';

// Try to load mapping from image_url_map.json if present
$mapping = [];
$mapFile = __DIR__ . '/image_url_map.json';
if (file_exists($mapFile)) {
    $data = json_decode(file_get_contents($mapFile), true);
    if (is_array($data)) $mapping = $data;
}

// If the user submitted a form with mappings, parse them
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mappings'])) {
    // Expect lines of the form: Product Name|https://... or JSON
    $text = trim($_POST['mappings']);
    // Try JSON first
    $maybe = json_decode($text, true);
    if (is_array($maybe)) {
        $mapping = $maybe;
    } else {
        $lines = preg_split('/\r?\n/', $text);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $parts = explode('|', $line, 2);
            if (count($parts) === 2) {
                $mapping[trim($parts[0])] = trim($parts[1]);
            }
        }
    }
}

// Show form if not applying
if (empty($mapping) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<h2>Update product images from external URLs</h2>';
    echo '<p>Paste lines like: <code>Product Name|https://example.com/cover.svg</code></p>';
    echo '<form method="post">';
    echo '<textarea name="mappings" rows="10" cols="80" placeholder="Pink Floyd - The Wall|https://...\nBlack Sabbath - Paranoid|https://..."></textarea><br>';
    echo '<button type="submit">Preview and apply</button>';
    echo '</form>';
    if (file_exists($mapFile)) echo '<p>Loaded mappings from <code>image_url_map.json</code>.</p>';
    $conn->close();
    exit();
}

$updated = 0;
$errors = [];
echo '<h2>Applying mappings</h2>';
foreach ($mapping as $productName => $url) {
    $p = $conn->real_escape_string($productName);
    $u = $conn->real_escape_string($url);
    $sql = "UPDATE products SET image_url = '$u' WHERE name = '$p'";
    if ($conn->query($sql)) {
        $affected = $conn->affected_rows;
        $updated += $affected;
        echo "Updated: " . htmlspecialchars($productName) . " -> " . htmlspecialchars($url) . " (rows: $affected)<br>";
    } else {
        $errors[] = "Error updating $productName: " . $conn->error;
    }
}

if (!empty($errors)) {
    echo '<h3>Errors</h3>';
    foreach ($errors as $e) echo htmlspecialchars($e) . '<br>';
}

echo "<hr>Done. Total rows affected: $updated";
$conn->close();

?>
