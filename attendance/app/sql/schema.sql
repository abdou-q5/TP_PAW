-- 
-- =========================
-- 1. Création de la base
-- =========================
CREATE DATABASE IF NOT EXISTS attendance_db 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE attendance_db;

-- =========================
-- 2. Tables
-- =========================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    role ENUM('student', 'professor', 'admin') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE student_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    level VARCHAR(10) NOT NULL,
    year INT NOT NULL
);

CREATE TABLE students (
    id INT PRIMARY KEY,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    group_id INT NULL,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE SET NULL
);

CREATE TABLE professors (
    id INT PRIMARY KEY,
    department VARCHAR(100) NULL,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE admins (
    id INT PRIMARY KEY,
    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    semester TINYINT NOT NULL,
    year INT NOT NULL
);

CREATE TABLE course_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    group_id INT NOT NULL,
    professor_id INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES professors(id) ON DELETE CASCADE,
    UNIQUE (course_id, group_id)
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_group_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_group_id) REFERENCES course_groups(id) ON DELETE CASCADE,
    UNIQUE (student_id, course_group_id)
);

CREATE TABLE attendance_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_group_id INT NOT NULL,
    session_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_group_id) REFERENCES course_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES professors(id) ON DELETE CASCADE
);

CREATE TABLE attendance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('present','absent','late','excused') NOT NULL DEFAULT 'absent',
    participation_score TINYINT NULL,
    notes TEXT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES attendance_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE (session_id, student_id)
);

CREATE TABLE justification_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attendance_record_id INT NOT NULL,
    student_id INT NOT NULL,
    reason TEXT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    review_comment TEXT NULL,
    FOREIGN KEY (attendance_record_id) REFERENCES attendance_records(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- =========================
-- 3. Données de test (sans hash pour l'instant)
-- =========================

INSERT INTO student_groups (name, level, year)
VALUES ('L3-INFO-G1', 'L3', 2025);

-- on met un mot de passe temporaire 'temp' (on corrigera ensuite en PHP)
INSERT INTO users (username, password_hash, first_name, last_name, email, role)
VALUES 
('prof1', 'TEMP', 'Ali', 'Professeur', 'ali.prof@univ.dz', 'professor'),
('admin1', 'TEMP', 'Admin', 'System', 'admin@univ.dz', 'admin'),
('s1', 'TEMP', 'Mohamed', 'Etudiant1', 'mohamed1@univ.dz', 'student'),
('s2', 'TEMP', 'Sara', 'Etudiant2', 'sara2@univ.dz', 'student');

INSERT INTO professors (id, department)
SELECT id, 'Informatique' FROM users WHERE username = 'prof1';

INSERT INTO admins (id)
SELECT id FROM users WHERE username = 'admin1';

INSERT INTO students (id, matricule, group_id)
SELECT id, 'MAT001', (SELECT id FROM student_groups WHERE name='L3-INFO-G1')
FROM users WHERE username='s1';

INSERT INTO students (id, matricule, group_id)
SELECT id, 'MAT002', (SELECT id FROM student_groups WHERE name='L3-INFO-G1')
FROM users WHERE username='s2';

INSERT INTO courses (code, name, semester, year)
VALUES ('WEB-ADV', 'Advanced Web Programming', 1, 2025);

INSERT INTO course_groups (course_id, group_id, professor_id)
VALUES (
  (SELECT id FROM courses WHERE code='WEB-ADV'),
  (SELECT id FROM student_groups WHERE name='L3-INFO-G1'),
  (SELECT p.id 
     FROM professors p 
     JOIN users u ON u.id = p.id 
    WHERE u.username='prof1')
);

INSERT INTO enrollments (student_id, course_group_id)
SELECT s.id, cg.id
FROM students s
JOIN users u ON u.id = s.id
JOIN course_groups cg ON cg.course_id = (SELECT id FROM courses WHERE code='WEB-ADV')
WHERE u.username IN ('s1', 's2');