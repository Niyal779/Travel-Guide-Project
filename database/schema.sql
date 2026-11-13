-- Fresh-install schema for Travel Guide.
-- Import this into an empty MySQL/MariaDB database named `travel_guide`.

CREATE DATABASE IF NOT EXISTS travel_guide
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE travel_guide;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'scout', 'user') NOT NULL DEFAULT 'user',
    is_verified TINYINT(1) NOT NULL DEFAULT 0,
    profile_picture VARCHAR(255) DEFAULT NULL,
    remember_token CHAR(64) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_remember_token (remember_token),
    KEY idx_users_role_verified (role, is_verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    scout_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    short_history TEXT NOT NULL,
    country VARCHAR(120) NOT NULL,
    genre VARCHAR(60) NOT NULL,
    cost_level ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    travel_medium_info VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_posts_status_created (status, created_at),
    KEY idx_posts_scout_status (scout_id, status),
    KEY idx_posts_country (country),
    CONSTRAINT fk_posts_scout FOREIGN KEY (scout_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_requests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    scout_id INT UNSIGNED NOT NULL,
    post_data LONGTEXT NOT NULL,
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    PRIMARY KEY (id),
    KEY idx_post_requests_status_date (status, requested_at),
    KEY idx_post_requests_scout (scout_id),
    CONSTRAINT fk_post_requests_scout FOREIGN KEY (scout_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wishlist (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    post_id INT UNSIGNED NOT NULL,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_wishlist_user_post (user_id, post_id),
    KEY idx_wishlist_post (post_id),
    CONSTRAINT fk_wishlist_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wishlist_post FOREIGN KEY (post_id) REFERENCES posts (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    content VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_comments_post_date (post_id, created_at),
    KEY idx_comments_user (user_id),
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cost_estimates (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id INT UNSIGNED NOT NULL,
    base_cost DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    PRIMARY KEY (id),
    UNIQUE KEY uq_cost_estimates_post (post_id),
    CONSTRAINT fk_cost_estimates_post FOREIGN KEY (post_id) REFERENCES posts (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
