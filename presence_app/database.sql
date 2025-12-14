-- database.sql
CREATE DATABASE IF NOT EXISTS presence_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE presence_db;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    session_index TINYINT NOT NULL, -- 1..6 (ou plus)
    presence TINYINT(1) DEFAULT 0, -- 0 absent, 1 present
    participation TINYINT(1) DEFAULT 0, -- 0 non, 1 oui
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_student_session (student_id, session_index),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert demo students (optionnel)
INSERT INTO students (student_id, last_name, first_name, email) VALUES
('2023001','Omar','Mohamed','omar@example.com'),
('2023002','Ali','Djamal','ali@example.com'),
('2023003','Lina','Kamal','lina@example.com');
