<?php
// json/add_student.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Send POST from the form.";
    exit;
}

$student_id = trim($_POST['student_id'] ?? '');
$name = trim($_POST['name'] ?? '');
$group = trim($_POST['group'] ?? '');

if ($student_id === '' || $name === '' || $group === '') {
    die("Please fill all fields.");
}

$file = __DIR__ . '/students.json';

// load existing
$students = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    $students = json_decode($content, true);
    if (!is_array($students)) $students = [];
}

// optional: avoid duplicate student_id
foreach ($students as $s) {
    if (($s['student_id'] ?? '') === $student_id) {
        die("Student ID already exists.");
    }
}

$students[] = [
    'student_id' => $student_id,
    'name' => $name,
    'group' => $group
];

// save
if (file_put_contents($file, json_encode($students, JSON_PRETTY_PRINT)) === false) {
    die("Failed to save.");
}

echo "✅ Student added successfully. <a href='add_student_form.html'>Add another</a>";
