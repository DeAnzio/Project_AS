-- Buat database
CREATE DATABASE IF NOT EXISTS project_akhir;
USE project_akhir;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'manager') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel logs
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert data contoh dengan password yang benar
-- Password: admin123 (hashed dengan bcrypt)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcx36', 'admin'),
('manager1', 'manager@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcx36', 'manager'),
('user1', 'user@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36DRcx36', 'user');

-- Add columns for external OAuth providers (Auth0)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS auth0_id VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS oauth_provider VARCHAR(50) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS oauth_token TEXT DEFAULT NULL;