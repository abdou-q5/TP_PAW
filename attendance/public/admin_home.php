<?php
require_once '../app/includes/auth.php';
requireRole('admin');
require_once '../app/config/db.php';

$db = getConnection();

$totStudents = $db->query("SELECT COUNT(*) AS c FROM students")->fetch()['c'];
$totProf    = $db->query("SELECT COUNT(*) AS c FROM professors")->fetch()['c'];
$totCourses = $db->query("SELECT COUNT(*) AS c FROM courses")->fetch()['c'];
$today      = date('Y-m-d');
$todaySess  = $db->query("SELECT COUNT(*) AS c FROM attendance_sessions WHERE session_date = '$today'")->fetch()['c'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Home</title>
 <!-- <link rel="stylesheet" href="assets/css/style.css"> -->

 <style>
    * {
      box-sizing: border-box;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    body {
      margin: 0;
      background: #f4f5fb;
      color: #1f2933;
    }

    /* ====== HEADER / NAVBAR ====== */
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

    /* ====== MAIN / STATS ====== */
    main {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 0 1.5rem;
    }

    main ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.5rem;
    }

    main li {
      background: #ffffff;
      border-radius: 14px;
      padding: 1.25rem 1.5rem;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.1);
      font-size: 0.95rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border: 1px solid #e5e7eb;
    }

    main li span.label {
      color: #6b7280;
    }

    main li span.value {
      font-weight: 700;
      font-size: 1.1rem;
      color: #111827;
    }

    /* Responsive */
    @media (max-width: 600px) {
      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      nav {
        flex-wrap: wrap;
        justify-content: flex-start;
      }
    }
  </style>
</head>
<body>
<header>
  <h1>Admin - Dashboard</h1>
  <nav>
    <a href="admin_home.php">Home</a>
    <a href="admin_students.php">Etudiants</a>
    <a href="admin_statistics.php">Stats</a>
    <a href="logout.php">Logout</a>
    

  </nav>
</header>
<main>
  <ul>
    <li>Total étudiants : <?= $totStudents ?></li>
    <li>Total profs : <?= $totProf ?></li>
    <li>Total cours : <?= $totCourses ?></li>
    <li>Sessions aujourd'hui : <?= $todaySess ?></li>
  </ul>
</main>
</body>
</html>