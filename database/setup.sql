-- Louis Vuitton E-Commerce Database Setup
-- Run this file to create database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS lv_ecommerce;
USE lv_ecommerce;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

-- Users table - stores customer and admin info
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- stored as hash
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products - inventory items
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    category ENUM('Clothing', 'Accessories', 'Cosmetics') NOT NULL,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart - user shopping carts
-- TODO: maybe add a "saved for later" feature?
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
-- Note: might want to add order_items table later for detailed line items
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Placed',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing
-- Passwords are hashed with bcrypt - use password_hash() in PHP
INSERT INTO users (full_name, email, password, role) VALUES
('Admin User', 'admin@lv.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Doe', 'customer@lv.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Clothing products
INSERT INTO products (name, price, description, category, image_path) VALUES
('Silk Evening Dress', 2850.00, 'Exquisite black silk evening dress with gold embroidery. Perfect for sophisticated events and galas. Made from premium Italian silk with a timeless silhouette that flatters every figure.', 'Clothing', 'uploads/dress.jpg'),
('Leather Jacket', 3200.00, 'Premium leather jacket crafted from the finest Italian leather. Features gold-tone hardware and signature LV monogram lining. A timeless piece that combines luxury with everyday wearability.', 'Clothing', 'uploads/jacket.jpg'),
('Cashmere Sweater', 1450.00, 'Ultra-soft cashmere sweater in classic black. Lightweight yet warm, featuring subtle gold LV logo embroidery. Perfect for layering or wearing alone for effortless elegance.', 'Clothing', 'uploads/sweater.jpg'),
('Tailored Trousers', 980.00, 'Perfectly tailored wool trousers with a modern slim fit. Features gold button details and impeccable construction. Versatile enough for business or evening occasions.', 'Clothing', 'uploads/trousers.jpg');

-- Accessories
INSERT INTO products (name, price, description, category, image_path) VALUES
('Monogram Handbag', 4500.00, 'Iconic LV monogram canvas handbag with leather trim and gold hardware. Spacious interior with multiple compartments. A statement piece that never goes out of style.', 'Accessories', 'uploads/handbag.jpg'),
('Leather Belt', 650.00, 'Reversible leather belt featuring the signature LV buckle in polished gold. Made from premium calfskin leather. Can be worn with both casual and formal attire.', 'Accessories', 'uploads/belt.jpg'),
('Designer Sunglasses', 890.00, 'Oversized sunglasses with LV monogram temples and gold accents. UV protection lenses in gradient tint. Comes with luxury case and cleaning cloth.', 'Accessories', 'uploads/sunglasses.jpg');

-- Cosmetics
INSERT INTO products (name, price, description, category, image_path) VALUES
('Luxury Lipstick', 85.00, 'High-pigment lipstick in classic red shade. Long-lasting formula with moisturizing properties. Housed in a gold-plated case with LV monogram detailing.', 'Cosmetics', 'uploads/lipstick.jpg'),
('Signature Perfume', 350.00, 'Exclusive eau de parfum with notes of jasmine, vanilla, and sandalwood. Comes in an elegant gold-accented bottle. A timeless fragrance that embodies luxury and sophistication.', 'Cosmetics', 'uploads/perfume.jpg'),
('Premium Face Cream', 420.00, 'Anti-aging face cream with gold-infused formula. Contains hyaluronic acid and vitamin E for ultimate hydration. Luxurious texture that absorbs quickly.', 'Cosmetics', 'uploads/facecream.jpg');

-- verify setup
SELECT 'Setup complete!' AS message;
SELECT 'Products created:' AS info, COUNT(*) AS count FROM products;
SELECT 'Users created:' AS info, COUNT(*) AS count FROM users;
