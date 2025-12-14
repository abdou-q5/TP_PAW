<?php
require_once '../app/includes/auth.php';
requireRole('professor');
require_once '../app/config/db.php';

$db = getConnection();
$profId = $_SESSION['user_id'];
$courseGroupId = (int)($_GET['cg'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_session'])) {
    $date = $_POST['session_date'];
    $time = $_POST['start_time'];

    $ins = $db->prepare("
        INSERT INTO attendance_sessions (course_group_id, session_date, start_time, status, created_by)
        VALUES (:cg, :d, :t, 'open', :pid)
    ");
    $ins->execute([
        ':cg' => $courseGroupId,
        ':d'  => $date,
        ':t'  => $time,
        ':pid'=> $profId
    ]);
    header("Location: professor_sessions.php?cg=".$courseGroupId);
    exit;
}

$stmt = $db->prepare("
    SELECT * FROM attendance_sessions 
    WHERE course_group_id = :cg
    ORDER BY session_date DESC, start_time DESC
");
$stmt->execute([':cg' => $courseGroupId]);
$sessions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sessions</title>
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

    /* ===== HEADER ===== */
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 2rem;
      background: #111827;
      color: #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    header h1 {
      margin: 0;
      font-size: 1.4rem;
      letter-spacing: 0.03em;
    }

    nav {
      display: flex;
      gap: 0.75rem;
    }

    nav a {
      color: #e5e7eb;
      text-decoration: none;
      font-size: 0.9rem;
      padding: 0.45rem 0.9rem;
      border-radius: 999px;
      transition: background 0.2s, color 0.2s, transform 0.1s;
    }

    nav a:hover {
      background: #1f2937;
      color: #fff;
      transform: translateY(-1px);
    }

    nav a:last-child { /* Logout */
      background: #ef4444;
      color: #fff;
    }

    nav a:last-child:hover {
      background: #dc2626;
    }

    /* ===== MAIN ===== */
    main {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1.5rem 3rem;
    }

    /* ===== FORMULAIRE CRÉATION SESSION ===== */
    form {
      background: #ffffff;
      padding: 1.25rem 1.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
      border: 1px solid #e5e7eb;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.8rem 1.2rem;
      margin-bottom: 1.6rem;
    }

    form label {
      font-weight: 600;
      color: #374151;
      font-size: 0.9rem;
    }

    input[type="date"],
    input[type="time"] {
      padding: 0.45rem 0.6rem;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      background: #f9fafb;
      font-size: 0.9rem;
      min-width: 160px;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    input[type="date"]:focus,
    input[type="time"]:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
      background-color: #fdfdff;
    }

    button[name="create_session"] {
      padding: 0.55rem 1.2rem;
      border-radius: 999px;
      border: none;
      background: #10b981;
      color: #fff;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 8px 18px rgba(16, 185, 129, 0.35);
    }

    button[name="create_session"]:hover {
      background: #059669;
      transform: translateY(-1px);
    }

    button[name="create_session"]:active {
      transform: translateY(0);
      box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
    }

    /* ===== TABLE SESSIONS ===== */
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

    /* Colonne Status mise en valeur */
    th:nth-child(3),
    td:nth-child(3) {
      font-weight: 600;
      color: #2563eb;
    }

    /* Lien "Marquer présences" comme bouton */
    td:last-child a {
      text-decoration: none;
      font-size: 0.85rem;
      padding: 0.35rem 0.9rem;
      border-radius: 999px;
      background: #2563eb;
      color: #fff;
      font-weight: 500;
      display: inline-block;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4);
    }

    td:last-child a:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    td:last-child a:active {
      transform: translateY(0);
      box-shadow: 0 3px 7px rgba(37, 99, 235, 0.4);
    }

    /* Responsive */
    @media (max-width: 800px) {
      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      nav {
        flex-wrap: wrap;
        justify-content: flex-start;
      }

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
<header>
  <h1>Sessions de cours</h1>
  <nav>
    <a href="professor_home.php">Home</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
  <form method="post">
    <label>Date :</label>
    <input type="date" name="session_date" required>
    <label>Heure début :</label>
    <input type="time" name="start_time" required>
    <button type="submit" name="create_session">Créer session</button>
  </form>

  <table>
    <tr><th>Date</th><th>Heure</th><th>Status</th><th>Action</th></tr>
    <?php foreach ($sessions as $s): ?>
    <tr>
      <td><?= htmlspecialchars($s['session_date']) ?></td>
      <td><?= htmlspecialchars($s['start_time']) ?></td>
      <td><?= htmlspecialchars($s['status']) ?></td>
      <td><a href="professor_session_attendance.php?id=<?= $s['id'] ?>">Marquer présences</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
</main>
</body>
</html>