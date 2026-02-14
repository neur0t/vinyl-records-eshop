-- Create Database
CREATE DATABASE IF NOT EXISTS vinyl_records_shop;
USE vinyl_records_shop;

-- Create Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(50),
    postal_code VARCHAR(10),
    country VARCHAR(50),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Shopping Cart Table (Temporary storage for session-based carts)
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Order Items Table (Products in an order)
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Create Payment Information Table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL UNIQUE,
    card_holder_name VARCHAR(100) NOT NULL,
    card_last_four VARCHAR(4) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Create Indexes for better performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_cart_user ON cart_items(user_id);
CREATE INDEX idx_cart_product ON cart_items(product_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_payments_order ON payments(order_id);

-- Insert Sample Categories
INSERT INTO categories (name, description) VALUES
('Rock', 'Κλασικό και σύγχρονο Rock'),
('Jazz', 'Jazz και παραλλαγές'),
('Pop', 'Δημοφιλή Pop καλλιτέχνες'),
('Metal', 'Heavy Metal και παραλλαγές'),
('Electronic', 'Electronic και Dance');

-- Insert Sample Products
INSERT INTO products (category_id, name, description, price, stock, image_url) VALUES
(1, 'Pink Floyd - The Wall', 'Θρυλικό άλμπουμ του 1979', 29.99, 10, 'assets/images/the-wall.svg'),
(1, 'The Beatles - Abbey Road', 'Κλασική δισκογραφία', 25.99, 15, 'assets/images/abbey-road.svg'),
(2, 'Miles Davis - Kind of Blue', 'Κορυφαίο Jazz άλμπουμ', 27.99, 8, 'assets/images/kind-of-blue.svg'),
(3, 'David Bowie - Ziggy Stardust', 'Καμποσέ rock κλασικό', 26.99, 12, 'assets/images/ziggy-stardust.svg'),
(1, 'Led Zeppelin - IV', 'Τεράστιο rock άλμπουμ', 28.99, 9, 'assets/images/led-zeppelin-iv.svg'),
(4, 'Black Sabbath - Paranoid', 'Heavy Metal θρύλος', 24.99, 7, 'assets/images/paranoid.svg'),
(2, 'John Coltrane - A Love Supreme', 'Jazz απλούστερκο έργο', 26.99, 6, 'assets/images/love-supreme.svg'),
(5, 'Daft Punk - Homework', 'Electronic κλασικό', 23.99, 11, 'assets/images/homework.svg');

-- Create Admin User (password: admin123)
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@vinylshop.gr', '$2y$10$YourHashedPasswordHere', 'Admin', 'User', 'admin');
