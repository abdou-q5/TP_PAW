CREATE DATABASE IF NOT EXISTS attendance_db;
USE attendance_db;

-- جدول المستخدمين
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','professor','admin') NOT NULL
);

-- جدول المواد
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- جدول مجموعات الطلاب
CREATE TABLE groups_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- جدول الجلسات
CREATE TABLE attendance_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    session_date DATE NOT NULL,
    FOREIGN KEY(course_id) REFERENCES courses(id)
);

-- جدول الحضور
CREATE TABLE attendance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('present','absent') NOT NULL,
    FOREIGN KEY(session_id) REFERENCES attendance_sessions(id),
    FOREIGN KEY(student_id) REFERENCES users(id)
);

-- جدول المبررات
CREATE TABLE justifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    session_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    FOREIGN KEY(student_id) REFERENCES users(id),
    FOREIGN KEY(session_id) REFERENCES attendance_sessions(id)
);
