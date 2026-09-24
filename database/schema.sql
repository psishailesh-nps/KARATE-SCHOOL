CREATE DATABASE IF NOT EXISTS jetpur_karate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jetpur_karate;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) NOT NULL UNIQUE,
 name VARCHAR(150) NOT NULL,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('Super Admin','Academy Admin','Coach','Accountant','Student / Parent') NOT NULL DEFAULT 'Academy Admin',
 status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
INSERT INTO users(username,name,password_hash,role,status)
VALUES('admin','System Administrator','$2y$12$XHaZ.uXp0xUi3DwJIjwOSeHPmFoDB0jbDorZebGD46Bm4n2hYtXfa','Super Admin','Active')
ON DUPLICATE KEY UPDATE username=username;

CREATE TABLE IF NOT EXISTS erp_store (
 data_key VARCHAR(100) PRIMARY KEY,
 data_value LONGTEXT NOT NULL,
 updated_by INT UNSIGNED NULL,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_store_user FOREIGN KEY(updated_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS audit_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NULL,
 action VARCHAR(50) NOT NULL,
 entity VARCHAR(100) NOT NULL,
 entity_id VARCHAR(100) NULL,
 ip_address VARCHAR(45) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_audit_user(user_id),
 INDEX idx_audit_created(created_at),
 CONSTRAINT fk_audit_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS batches (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 code VARCHAR(30) NULL UNIQUE,
 days VARCHAR(100) NULL,
 start_time TIME NULL,
 end_time TIME NULL,
 coach_id INT UNSIGNED NULL,
 capacity INT NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Active',
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS coaches (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 coach_code VARCHAR(30) NOT NULL UNIQUE,
 name VARCHAR(150) NOT NULL,
 mobile VARCHAR(20) NULL,
 email VARCHAR(150) NULL,
 specialization VARCHAR(150) NULL,
 experience_years DECIMAL(5,1) NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Active',
 joining_date DATE NULL,
 address TEXT NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 student_code VARCHAR(30) NOT NULL UNIQUE,
 first_name VARCHAR(80) NOT NULL,
 last_name VARCHAR(80) NULL,
 gender VARCHAR(20) NULL,
 date_of_birth DATE NULL,
 mobile VARCHAR(20) NULL,
 email VARCHAR(150) NULL,
 admission_date DATE NULL,
 belt VARCHAR(50) NOT NULL DEFAULT 'White Belt',
 batch_id INT UNSIGNED NULL,
 coach_id INT UNSIGNED NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'Active',
 address TEXT NULL,
 emergency_contact_name VARCHAR(150) NULL,
 emergency_contact_mobile VARCHAR(20) NULL,
 notes TEXT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_students_batch(batch_id),
 INDEX idx_students_coach(coach_id),
 INDEX idx_students_mobile(mobile)
);

CREATE TABLE IF NOT EXISTS app_meta (
 meta_key VARCHAR(100) PRIMARY KEY,
 meta_value TEXT NULL,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);