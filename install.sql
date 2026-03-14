CREATE DATABASE IF NOT EXISTS courier_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE courier_crm;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(30) UNIQUE NULL,
    email VARCHAR(150) UNIQUE NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('recruiter','admin') NOT NULL DEFAULT 'recruiter',
    accepted_terms_at DATETIME NULL,
    accepted_privacy_at DATETIME NULL,
    last_seen_news_id INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS couriers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recruiter_id INT UNSIGNED NOT NULL,
    last_name VARCHAR(120) NOT NULL,
    first_name VARCHAR(120) NOT NULL,
    city VARCHAR(120) NOT NULL,
    invite_date DATE NOT NULL,
    orders_count INT UNSIGNED NOT NULL DEFAULT 0,
    reward DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('Приглашен в хаб','Принят','Отклонен') NOT NULL DEFAULT 'Приглашен в хаб',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_couriers_recruiter FOREIGN KEY (recruiter_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    author_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_news_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS password_reset_codes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    code_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payout_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recruiter_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    requisites TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    admin_comment TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME NULL,
    CONSTRAINT fk_payout_recruiter FOREIGN KEY (recruiter_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Администратор по умолчанию (пароль: admin12345)
INSERT INTO users (name, phone, email, password, role)
VALUES ('Администратор', NULL, 'admin@example.com', '$2y$12$kezuM6nLMvhGlxnl9p9vE.K12BIQ.a6cRPqss96qvWZrOzUBOx/Vm', 'admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);
