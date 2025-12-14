<?php
require_once '../app/includes/auth.php';
requireRole('admin');
require_once '../app/config/db.php';

$db = getConnection();

$sql = "
SELECT c.name, c.code,
       AVG(
         CASE 
           WHEN ar.status IN ('present','excused') THEN 1
           ELSE 0
         END
       ) * 100 AS attendance_rate
FROM attendance_records ar
JOIN attendance_sessions ses ON ses.id = ar.session_id
JOIN course_groups cg ON cg.id = ses.course_group_id
JOIN courses c ON c.id = cg.course_id
GROUP BY c.id
";
$data = $db->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Statistiques</title>
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
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1.5rem 3rem;
    }

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

    /* Colonne % Présence alignée à droite */
    th:nth-child(3),
    td:nth-child(3) {
      text-align: right;
      font-weight: 600;
      color: #2563eb;
    }

    @media (max-width: 700px) {
      table, thead, tbody, th, td, tr {
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
<header><h1>Statistiques</h1></header>
<main>
  <table>
    <tr><th>Cours</th><th>Code</th><th>% Présence</th></tr>
    <?php foreach ($data as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['code']) ?></td>
      <td><?= round($row['attendance_rate'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</main>
</body>
</html>