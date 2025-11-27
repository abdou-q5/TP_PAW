<?php
include 'config.php';

$action = $_GET['action'] ?? '';

if($action=='export'){
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Name','Email','Username']); // رؤوس الأعمدة

    $stmt = $conn->query("SELECT name,email,username FROM users WHERE role='student'");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
if($action=='import' && isset($_FILES['file'])){
    $file = $_FILES['file']['tmp_name'];
    if(($handle = fopen($file, 'r')) !== false){
        $header = fgetcsv($handle); // تخطي الصف الأول (رؤوس الأعمدة)
        while(($data = fgetcsv($handle, 1000, ',')) !== false){
            $stmt = $conn->prepare("INSERT INTO users (name,email,username,password,role) VALUES (?,?,?,?,?)");
            $stmt->execute([$data[0], $data[1], $data[2], password_hash('default123', PASSWORD_DEFAULT), 'student']);
        }
        fclose($handle);
        echo "Import successful";
    }
}

