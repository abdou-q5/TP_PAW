<?php
// json/take_attendance.php
$students_file = __DIR__ . '/students.json';
if (!file_exists($students_file)) {
    die("students.json not found. Add students first.");
}

$students = json_decode(file_get_contents($students_file), true);
if (!is_array($students)) $students = [];

$today = date('Y-m-d');
$attendance_file = __DIR__ . "/attendance_{$today}.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (file_exists($attendance_file)) {
        die("Attendance for today has already been taken.");
    }

    $attendance = [];
    foreach ($students as $s) {
        $id = $s['student_id'];
        $status = $_POST["status_$id"] ?? 'absent';
        $attendance[] = ['student_id' => $id, 'status' => $status];
    }
    file_put_contents($attendance_file, json_encode($attendance, JSON_PRETTY_PRINT));
    echo "✅ Attendance saved to " . basename($attendance_file);
    exit;
}

// show form
echo "<h2>Take Attendance — $today</h2>";
echo "<form method='post'>";
foreach ($students as $s) {
    $id = htmlspecialchars($s['student_id']);
    $name = htmlspecialchars($s['name']);
    echo "<div>{$id} — {$name} : 
        <select name='status_{$id}'>
          <option value='present'>Present</option>
          <option value='absent' selected>Absent</option>
        </select>
    </div>";
}
echo "<button type='submit'>Save Attendance</button></form>";
