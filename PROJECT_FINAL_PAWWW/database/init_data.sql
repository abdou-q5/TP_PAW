USE attendance_db;

-- إضافة مدير واحد
INSERT INTO users (name, email, username, password, role)
VALUES ('Admin Algiers', 'admin@univ.dz', 'admin', 
        '$2y$10$e0NRQW2YlYv0eVnK7tx2Ie2Oe5Q8b2eL9o.lQW9QZcW0fLpEJjFqe', 'admin');
-- كلمة المرور: admin123 (مشفرة بـ password_hash)

-- إضافة أساتذة
INSERT INTO users (name, email, username, password, role)
VALUES 
('Prof. Ali', 'ali@univ.dz', 'profali', '$2y$10$9Jt1gTh2sfP9bDqUoZ5VwO8Q6h9sJc2V7lPZj3YzQ2f7x8f3gX2mG', 'professor'),
('Prof. Sara', 'sara@univ.dz', 'profsara', '$2y$10$9Jt1gTh2sfP9bDqUoZ5VwO8Q6h9sJc2V7lPZj3YzQ2f7x8f3gX2mG', 'professor');

-- إضافة طلاب
INSERT INTO users (name, email, username, password, role)
VALUES 
('Ahmed B.', 'ahmed@student.univ.dz', 'ahmedb', '$2y$10$Z8W1aU9v7k2HnR1x0fVxPeOq6aYb6xB2qfJj2b3e6mK0aP1wY6rQ', 'student'),
('Fatima L.', 'fatima@student.univ.dz', 'fatimal', '$2y$10$Z8W1aU9v7k2HnR1x0fVxPeOq6aYb6xB2qfJj2b3e6mK0aP1wY6rQ', 'student'),
('Youssef M.', 'youssef@student.univ.dz', 'youssefm', '$2y$10$Z8W1aU9v7k2HnR1x0fVxPeOq6aYb6xB2qfJj2b3e6mK0aP1wY6rQ', 'student');

-- إضافة مواد
INSERT INTO courses (name) VALUES ('Mathematics'), ('Physics'), ('Computer Science');

-- إضافة مجموعات
INSERT INTO groups_table (name) VALUES ('Groupe A'), ('Groupe B');

-- إضافة جلسات حضور (Attendance Sessions)
INSERT INTO attendance_sessions (course_id, session_date)
VALUES 
(1, '2025-11-25'),
(2, '2025-11-26'),
(3, '2025-11-27');
