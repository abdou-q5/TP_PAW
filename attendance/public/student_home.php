<?php
require_once '../app/includes/auth.php';
requireRole('student');
require_once '../app/config/db.php';

$db = getConnection();
$studentId = $_SESSION['user_id'];

$stmt = $db->prepare("
  SELECT cg.id AS course_group_id, c.name, c.code, g.name AS group_name
  FROM enrollments e
  JOIN course_groups cg ON cg.id = e.course_group_id
  JOIN courses c ON c.id = cg.course_id
  JOIN student_groups g ON g.id = cg.group_id
  WHERE e.student_id = :sid
");
$stmt->execute([':sid' => $studentId]);
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Accueil étudiant</title>
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

    /* ===== HEADER / NAVBAR ===== */
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

    main h2 {
      margin-top: 0;
    }

    ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 0.9rem;
    }

    li {
      background: #ffffff;
      border-radius: 12px;
      padding: 0.9rem 1.2rem;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
      border: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.95rem;
      flex-wrap: wrap;
      gap: 0.4rem 0.8rem;
    }

    li span.course-info {
      color: #374151;
      font-weight: 500;
    }

    li a {
      text-decoration: none;
      font-size: 0.85rem;
      padding: 0.35rem 0.8rem;
      border-radius: 999px;
      background: #2563eb;
      color: #fff;
      font-weight: 500;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 6px 14px rgba(37, 99, 235, 0.4);
    }

    li a:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    li a:active {
      transform: translateY(0);
      box-shadow: 0 3px 7px rgba(37, 99, 235, 0.4);
    }

    /* Responsive */
    @media (max-width: 700px) {
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
  <h1>Étudiant - Mes cours</h1>
  <nav>
    <a href="student_home.php">Home</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<main>
  <ul>
  <?php foreach ($courses as $c): ?>
    <li>
      <?= htmlspecialchars($c['name']) ?> - <?= htmlspecialchars($c['group_name']) ?>
      <a href="student_course_attendance.php?cg=<?= $c['course_group_id'] ?>">Détails</a>
    </li>
  <?php endforeach; ?>
  </ul>
</main>
</body>
</html>