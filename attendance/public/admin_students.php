<?php
require_once '../app/includes/auth.php';
requireRole('admin');
require_once '../app/config/db.php';

$db = getConnection();

if (isset($_GET['export']) && $_GET['export'] == '1') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['matricule','first_name','last_name','group_name']);
    $sql = "
      SELECT s.matricule, u.first_name, u.last_name, g.name AS group_name
      FROM students s
      JOIN users u ON u.id = s.id
      LEFT JOIN groups g ON g.id = s.group_id
    ";
    foreach ($db->query($sql) as $row) {
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];
    if (($handle = fopen($file, 'r')) !== false) {
        fgetcsv($handle); // header
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            list($matricule, $first, $last, $groupName) = $data;
            $matricule = trim($matricule);
            if ($matricule === '') continue;

            $stmtG = $db->prepare("SELECT id FROM groups WHERE name = :g");
            $stmtG->execute([':g' => $groupName]);
            $g = $stmtG->fetch();
            if (!$g) {
                $insG = $db->prepare("INSERT INTO groups (name, level, year) VALUES (:n, 'L3', YEAR(CURDATE()))");
                $insG->execute([':n' => $groupName]);
                $groupId = $db->lastInsertId();
            } else {
                $groupId = $g['id'];
            }

            $stmtS = $db->prepare("SELECT u.id FROM students s JOIN users u ON u.id = s.id WHERE s.matricule = :m");
            $stmtS->execute([':m' => $matricule]);
            $ex = $stmtS->fetch();

            if (!$ex) {
                $insU = $db->prepare("
                    INSERT INTO users (username, password_hash, first_name, last_name, email, role)
                    VALUES (:u, :p, :f, :l, :e, 'student')
                ");
                $defaultPass = password_hash('123456', PASSWORD_DEFAULT);
                $username = $matricule;
                $email = strtolower($first.'.'.$last).'@univ.dz';

                $insU->execute([
                    ':u' => $username,
                    ':p' => $defaultPass,
                    ':f' => $first,
                    ':l' => $last,
                    ':e' => $email
                ]);
                $userId = $db->lastInsertId();

                $insS = $db->prepare("
                    INSERT INTO students (id, matricule, group_id) 
                    VALUES (:id, :m, :gid)
                ");
                $insS->execute([
                    ':id' => $userId,
                    ':m'  => $matricule,
                    ':gid'=> $groupId
                ]);
            } else {
                $upd = $db->prepare("UPDATE students SET group_id = :gid WHERE id = :id");
                $upd->execute([':gid' => $groupId, ':id' => $ex['id']]);
            }
        }
        fclose($handle);
    }
}

$sql = "
  SELECT s.matricule, u.first_name, u.last_name, g.name AS group_name
  FROM students s
  JOIN users u ON u.id = s.id
  LEFT JOIN student_groups g ON g.id = s.group_id
  ORDER BY g.name, u.last_name
";

$students = $db->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Gestion étudiants</title>
  <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

  <style>
    * {
      box-sizing: border-box;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    body {
      margin: 0;
      background: #f4f5fb;
      color: #111827;
    }

    header {
      background: #111827;
      color: #fff;
      padding: 1.2rem 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    header h1 {
      margin: 0;
      font-size: 1.4rem;
      letter-spacing: 0.03em;
    }

    main {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 0 1.5rem 3rem;
    }

    /* Formulaire import */
    form {
      background: #ffffff;
      padding: 1.25rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.8rem 1rem;
      margin-bottom: 1.5rem;
      border: 1px solid #e5e7eb;
    }

    form label {
      font-weight: 600;
      color: #374151;
    }

    input[type="file"] {
      padding: 0.4rem;
      font-size: 0.9rem;
      border-radius: 6px;
      border: 1px solid #d1d5db;
      background: #f9fafb;
    }

    button {
      padding: 0.55rem 1.1rem;
      border-radius: 999px;
      border: none;
      background: #2563eb;
      color: #fff;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
    }

    button:hover {
      background: #1d4ed8;
    }

    button:active {
      transform: translateY(1px);
      box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
    }

    /* Lien export */
    p a {
      display: inline-block;
      margin-bottom: 1rem;
      color: #2563eb;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      padding: 0.35rem 0.8rem;
      border-radius: 999px;
      background: #e0ecff;
      transition: background 0.2s, color 0.2s, transform 0.1s;
    }

    p a:hover {
      background: #c7ddff;
      transform: translateY(-1px);
    }

    /* Tableau */
    table {
      width: 100%;
      border-collapse: collapse;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
      border: 1px solid #e5e7eb;
    }

    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      font-size: 0.9rem;
    }

    th {
      background: #f3f4f6;
      color: #4b5563;
      font-weight: 600;
      border-bottom: 1px solid #e5e7eb;
    }

    tr:nth-child(even) {
      background: #f9fafb;
    }

    tr:hover {
      background: #eef2ff;
    }

    td {
      border-bottom: 1px solid #e5e7eb;
    }

    /* Responsive */
    @media (max-width: 700px) {
      form {
        flex-direction: column;
        align-items: flex-start;
      }

      table, thead, tbody, th, td, tr {
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
<header><h1>Gestion des étudiants</h1></header>
<main>
  <form method="post" enctype="multipart/form-data">
    <label>Importer étudiants (CSV):</label>
    <input type="file" name="csv_file" required>
    <button type="submit">Importer</button>
  </form>

  <p><a href="?export=1">Exporter liste (CSV)</a></p>

  <table>
    <tr><th>Matricule</th><th>Nom</th><th>Groupe</th></tr>
    <?php foreach ($students as $s): ?>
    <tr>
      <td><?= htmlspecialchars($s['matricule']) ?></td>
      <td><?= htmlspecialchars($s['last_name'].' '.$s['first_name']) ?></td>
      <td><?= htmlspecialchars($s['group_name']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</main>
</body>
</html>