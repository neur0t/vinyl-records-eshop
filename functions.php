<?php
// Common Functions File
require_once 'config.php';

/**
 * Get all categories from database
 */
function getCategories() {
    global $conn;
    $sql = "SELECT * FROM categories ORDER BY name";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get all products with optional category filter
 */
function getProducts($categoryId = null) {
    global $conn;
    
    if ($categoryId) {
        $categoryId = intval($categoryId);
        $sql = "SELECT * FROM products WHERE category_id = $categoryId ORDER BY name";
    } else {
        $sql = "SELECT * FROM products ORDER BY name";
    }
    
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get single product by ID
 */
function getProductById($productId) {
    global $conn;
    $productId = intval($productId);
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = $productId";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

/**
 * Get category by ID
 */
function getCategoryById($categoryId) {
    global $conn;
    $categoryId = intval($categoryId);
    $sql = "SELECT * FROM categories WHERE id = $categoryId";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user info
 */
function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = intval($_SESSION['user_id']);
    $sql = "SELECT * FROM users WHERE id = $userId";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

/**
 * Add product to cart
 */
function addToCart($productId, $quantity = 1) {
    global $conn;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = intval($_SESSION['user_id']);
    $productId = intval($productId);
    $quantity = intval($quantity);
    
    // Check if product exists
    $product = getProductById($productId);
    if (!$product) {
        return false;
    }
    
    // Check if item already in cart
    $sql = "SELECT id FROM cart_items WHERE user_id = $userId AND product_id = $productId";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Update quantity
        $sql = "UPDATE cart_items SET quantity = quantity + $quantity WHERE user_id = $userId AND product_id = $productId";
    } else {
        // Insert new item
        $sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ($userId, $productId, $quantity)";
    }
    
    return $conn->query($sql);
}

/**
 * Get cart items for user
 */
function getCartItems() {
    global $conn;
    
    if (!isLoggedIn()) {
        return [];
    }
    
    $userId = intval($_SESSION['user_id']);
    $sql = "SELECT ci.id, ci.quantity, p.* FROM cart_items ci 
            JOIN products p ON ci.product_id = p.id 
            WHERE ci.user_id = $userId 
            ORDER BY ci.added_at DESC";
    
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get cart total
 */
function getCartTotal() {
    global $conn;
    
    if (!isLoggedIn()) {
        return 0;
    }
    
    $userId = intval($_SESSION['user_id']);
    $sql = "SELECT SUM(ci.quantity * p.price) as total FROM cart_items ci 
            JOIN products p ON ci.product_id = p.id 
            WHERE ci.user_id = $userId";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Remove item from cart
 */
function removeFromCart($cartItemId) {
    global $conn;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $cartItemId = intval($cartItemId);
    $userId = intval($_SESSION['user_id']);
    
    $sql = "DELETE FROM cart_items WHERE id = $cartItemId AND user_id = $userId";
    return $conn->query($sql);
}

/**
 * Update cart item quantity
 */
function updateCartQuantity($cartItemId, $quantity) {
    global $conn;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $cartItemId = intval($cartItemId);
    $quantity = intval($quantity);
    $userId = intval($_SESSION['user_id']);
    
    if ($quantity <= 0) {
        return removeFromCart($cartItemId);
    }
    
    $sql = "UPDATE cart_items SET quantity = $quantity WHERE id = $cartItemId AND user_id = $userId";
    return $conn->query($sql);
}

/**
 * Clear cart for user
 */
function clearCart() {
    global $conn;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = intval($_SESSION['user_id']);
    $sql = "DELETE FROM cart_items WHERE user_id = $userId";
    return $conn->query($sql);
}

/**
 * Create order from cart
 */
function createOrder($shippingAddress, $paymentMethod) {
    global $conn;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = intval($_SESSION['user_id']);
    $totalAmount = getCartTotal();
    $shippingAddress = $conn->real_escape_string($shippingAddress);
    $paymentMethod = $conn->real_escape_string($paymentMethod);
    
    // Create order
    $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) 
            VALUES ($userId, $totalAmount, '$shippingAddress', '$paymentMethod')";
    
    if ($conn->query($sql)) {
        $orderId = $conn->insert_id;
        
        // Get cart items and add to order_items
        $cartItems = getCartItems();
        foreach ($cartItems as $item) {
            $productId = intval($item['id']);
            $quantity = intval($item['quantity']);
            $unitPrice = floatval($item['price']);
            
            $sql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price) 
                    VALUES ($orderId, $productId, $quantity, $unitPrice)";
            $conn->query($sql);
        }
        
        // Clear cart
        clearCart();
        
        return $orderId;
    }
    
    return false;
}

/**
 * Get user orders
 */
function getUserOrders($userId = null) {
    global $conn;
    
    if (!$userId && isLoggedIn()) {
        $userId = intval($_SESSION['user_id']);
    }
    
    if (!$userId) {
        return [];
    }
    
    $userId = intval($userId);
    $sql = "SELECT * FROM orders WHERE user_id = $userId ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get order details
 */
function getOrderDetails($orderId) {
    global $conn;
    
    $orderId = intval($orderId);
    $sql = "SELECT o.*, u.username, u.email FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.id = $orderId";
    
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

/**
 * Get order items
 */
function getOrderItems($orderId) {
    global $conn;
    
    $orderId = intval($orderId);
    $sql = "SELECT oi.*, p.name, p.image_url FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = $orderId";
    
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Escape string for database
 */
function escape($string) {
    global $conn;
    return $conn->real_escape_string($string);
}

/**
 * Format price
 */
function formatPrice($price) {
    return number_format($price, 2, ',', '.') . '€';
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Register a new user
 * Returns new user id on success, false on failure
 */
function registerUser($username, $email, $password) {
    global $conn;

    $username = trim($username);
    $email = trim($email);

    if (empty($username) || empty($email) || empty($password)) {
        return false;
    }

    // Check if username or email already exists (prepared)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return false;
    }
    $stmt->close();

    $hash = hashPassword($password);
    $role = ROLE_USER;

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    if (!$stmt) return false;
    $stmt->bind_param('ssss', $username, $email, $hash, $role);
    $ok = $stmt->execute();
    if ($ok) {
        $newId = $conn->insert_id;
        $stmt->close();
        return $newId;
    }
    $stmt->close();
    return false;
}

/**
 * Authenticate user by username or email and password
 * Returns user id on success, false on failure
 */
function authenticateUser($identifier, $password) {
    global $conn;

    $identifier = trim($identifier);
    if (empty($identifier) || empty($password)) return false;

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? OR email = ? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $stmt->bind_result($id, $hash);
    if ($stmt->fetch()) {
        $stmt->close();
        if (verifyPassword($password, $hash)) {
            return intval($id);
        }
        return false;
    }
    $stmt->close();
    return false;
}

/**
 * Redirect to page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}
?>
