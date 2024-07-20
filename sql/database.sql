CREATE DATABASE IF NOT EXISTS php_mvc;

USE php_mvc;

CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE(email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sessions (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Check if the index exists before creating it
SET @indexName := (SELECT COUNT(1)
                FROM INFORMATION_SCHEMA.STATISTICS
                WHERE table_schema = 'php_mvc_test'
                AND table_name = 'users'
                AND index_name = 'idx_users_email');

SET @sql := IF(@indexName = 0,
            'CREATE INDEX idx_users_email ON users(email)',
            'SELECT "Index already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;