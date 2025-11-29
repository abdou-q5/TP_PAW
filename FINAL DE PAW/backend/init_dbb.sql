CREATE DATABASE IF NOT EXISTS atr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE atr;


CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
role ENUM('admin','professor','student') NOT NULL,
fullname VARCHAR(255)
);


CREATE TABLE courses (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
professor_id INT,
FOREIGN KEY (professor_id) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE enrollments (
id INT AUTO_INCREMENT PRIMARY KEY,
course_id INT NOT NULL,
student_id INT NOT NULL,
UNIQUE(course_id, student_id),
FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE attendance (
id INT AUTO_INCREMENT PRIMARY KEY,
course_id INT NOT NULL,
student_id INT NOT NULL,
date DATE NOT NULL,
presence TINYINT(1) NOT NULL DEFAULT 0,
participation ENUM('none','low','medium','high') DEFAULT 'none',
FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO users (username, password, role, full_name) VALUES
('admin',  '$2y$10$e0NRFJx9yLxURVxq1vP6MOu5FZQ5PlNz1XWd4l1LmxsE8Z0XfUQ1K', 'admin', 'Admin Principal'),
('prof1',  '$2y$10$e0NRFJx9yLxURVxq1vP6MOu5FZQ5PlNz1XWd4l1LmxsE8Z0XfUQ1K', 'professor', 'Professeur Ali'),
('student1','$2y$10$e0NRFJx9yLxURVxq1vP6MOu5FZQ5PlNz1XWd4l1LmxsE8Z0XfUQ1K', 'student', 'Étudiant Ahmed'),
('student2','$2y$10$e0NRFJx9yLxURVxq1vP6MOu5FZQ5PlNz1XWd4l1LmxsE8Z0XfUQ1K', 'student', 'Étudiant Sara');
