-- ============================================================
-- DATABASE: Crochet Haven (WITHOUT PRODUCTS TABLE)
-- ============================================================

DROP DATABASE IF EXISTS crochet_haven;

CREATE DATABASE crochet_haven
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE crochet_haven;

-- ============================================================
-- USERS TABLE
-- ============================================================

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,

    address TEXT,
    city VARCHAR(100),
    pincode VARCHAR(10),
     is_admin TINYINT(1) DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_phone ON users(phone);
     
-- orders table

DROP TABLE IF EXISTS orders;
 CREATE TABLE orders ( id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL, 
  payment_method VARCHAR(50) NOT NULL, 
  full_name VARCHAR(255) NOT NULL,
   phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL, 
    address TEXT NOT NULL, 
   city VARCHAR(100) NOT NULL,
    pincode VARCHAR(10) NOT NULL, 
    status VARCHAR(50) DEFAULT 'Pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
     CREATE INDEX idx_orders_user_id ON orders(user_id);
     CREATE INDEX idx_orders_status ON orders(status);

-- ============================================================
-- ORDER ITEMS TABLE
-- ============================================================

DROP TABLE IF EXISTS order_items;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,

    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_order_items_order_id ON order_items(order_id);

-- ============================================================
-- CUSTOM ORDERS TABLE (user_id nullable)
-- ============================================================

DROP TABLE IF EXISTS custom_orders;

CREATE TABLE custom_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,

    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,

    product_type VARCHAR(100) NOT NULL,
    color VARCHAR(100),
    size VARCHAR(100),

    description TEXT NOT NULL,
    reference_image VARCHAR(500),
    estimated_price DECIMAL(10,2),

    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- CONTACT MESSAGES TABLE
-- ============================================================

DROP TABLE IF EXISTS contact_messages;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,

    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- REVIEWS TABLE (NO FK to products table)
-- ============================================================

DROP TABLE IF EXISTS reviews;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,

    rating TINYINT NOT NULL,
    review_text TEXT NOT NULL,
    photos JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_review (product_id, user_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_reviews_product_id ON reviews(product_id);
CREATE INDEX idx_reviews_user_id ON reviews(user_id);

-- ============================================================
-- CARTS TABLE
-- ============================================================

DROP TABLE IF EXISTS carts;

CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_cart (user_id, product_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- WISHLISTS TABLE
-- ============================================================

DROP TABLE IF EXISTS wishlists;

CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_wishlist (user_id, product_id),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Insert normal user
INSERT INTO users (full_name, email, phone, password, is_admin)
VALUES (
    'Test User',
    'test@example.com',
    '9876543210',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    0
);

-- Insert admin user
INSERT INTO users (full_name, email, phone, password, is_admin)
VALUES (
    'Admin User',
    'admin@crochethaven.com',
    '9999999999',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1
);

