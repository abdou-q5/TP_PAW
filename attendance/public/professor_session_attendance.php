<?php
require_once '../app/includes/auth.php';
requireRole('professor');
require_once '../app/config/db.php';

$db = getConnection();
$sessionId = (int)($_GET['id'] ?? 0);

$stmt = $db->prepare("
    SELECT ses.*, c.name AS course_name, g.name AS group_name
    FROM attendance_sessions ses
    JOIN course_groups cg ON cg.id = ses.course_group_id
    JOIN courses c ON c.id = cg.course_id
    JOIN student_groups g ON g.id = cg.group_id
    WHERE ses.id = :sid
");
$stmt->execute([':sid' => $sessionId]);
$session = $stmt->fetch();

$stmt2 = $db->prepare("
    SELECT s.id AS student_id, s.matricule, u.first_name, u.last_name,
    ar.status, ar.participation_score
    FROM enrollments e
    JOIN students s ON s.id = e.student_id
    JOIN users u ON u.id = s.id
    LEFT JOIN attendance_records ar 
      ON ar.student_id = s.id AND ar.session_id = :sid
    WHERE e.course_group_id = :cg
    ORDER BY u.last_name, u.first_name
");
$stmt2->execute([
    ':sid' => $sessionId,
    ':cg'  => $session['course_group_id']
]);
$students = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Marquer présence</title>
  <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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
      background: #111827;
      color: #fff;
      padding: 1.1rem 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    header h1 {
      margin: 0;
      font-size: 1.3rem;
      letter-spacing: 0.02em;
    }

    /* ===== MAIN ===== */
    main {
      max-width: 1100px;
      margin: 2rem auto;
      padding: 0 1.5rem 3rem;
    }

    /* Info session */
    main p {
      background: #e5f3ff;
      border: 1px solid #bfdbfe;
      color: #1e3a8a;
      padding: 0.7rem 1rem;
      border-radius: 10px;
      font-size: 0.9rem;
      margin-top: 0;
      margin-bottom: 1.3rem;
    }

    /* ===== TABLE ===== */
    #attendanceTable {
      width: 100%;
      border-collapse: collapse;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
      border: 1px solid #e5e7eb;
      margin-bottom: 1.5rem;
    }

    #attendanceTable th,
    #attendanceTable td {
      padding: 0.65rem 0.9rem;
      text-align: left;
      font-size: 0.9rem;
    }

    #attendanceTable th {
      background: #f3f4f6;
      color: #4b5563;
      font-weight: 600;
      border-bottom: 1px solid #e5e7eb;
    }

    #attendanceTable tr:nth-child(even) {
      background: #f9fafb;
    }

    #attendanceTable tr:hover {
      background: #eef2ff;
    }

    #attendanceTable td {
      border-bottom: 1px solid #e5e7eb;
    }

    #attendanceTable td:nth-child(1) {
      font-family: "SF Mono", ui-monospace, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
      font-size: 0.85rem;
      color: #374151;
    }

    #attendanceTable td:nth-child(2) {
      font-weight: 500;
      color: #111827;
    }

    /* ===== CHAMPS STATUS & PARTICIPATION ===== */
    select.status {
      padding: 0.35rem 0.5rem;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      background: #f9fafb;
      font-size: 0.85rem;
      color: #111827;
      min-width: 110px;
      cursor: pointer;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    select.status:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
      background-color: #fdfdff;
    }

    input.participation {
      width: 4rem;
      padding: 0.3rem 0.4rem;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      background: #f9fafb;
      font-size: 0.85rem;
      text-align: center;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    input.participation:focus {
      outline: none;
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
      background-color: #fdfdfb;
    }

    /* ===== BOUTON ENREGISTRER ===== */
    #saveBtn {
      display: inline-block;
      padding: 0.65rem 1.5rem;
      border-radius: 999px;
      border: none;
      background: #2563eb;
      color: #fff;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 10px 22px rgba(37, 99, 235, 0.4);
    }

    #saveBtn:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    #saveBtn:active {
      transform: translateY(0);
      box-shadow: 0 5px 10px rgba(37, 99, 235, 0.4);
    }

    /* Responsive */
    @media (max-width: 800px) {
      #attendanceTable,
      #attendanceTable thead,
      #attendanceTable tbody,
      #attendanceTable th,
      #attendanceTable td,
      #attendanceTable tr {
        font-size: 0.8rem;
      }

      main {
        padding: 0 1rem 2rem;
      }
    }
  </style>
</head>
<body>
<header>
  <h1><?= htmlspecialchars($session['course_name']) ?> - <?= htmlspecialchars($session['group_name']) ?></h1>
</header>
<main>
  <p>Date : <?= $session['session_date'] ?> | Status : <?= $session['status'] ?></p>

  <table id="attendanceTable">
    <tr>
      <th>Matricule</th><th>Nom</th><th>Status</th><th>Participation</th>
    </tr>
    <?php foreach ($students as $st): ?>
    <tr data-student-id="<?= $st['student_id'] ?>">
      <td><?= htmlspecialchars($st['matricule']) ?></td>
      <td><?= htmlspecialchars($st['last_name'].' '.$st['first_name']) ?></td>
      <td>
        <select class="status">
          <?php
          $current = $st['status'] ?? 'absent';
          foreach (['present','absent','late','excused'] as $opt) {
              $sel = ($opt === $current) ? 'selected' : '';
              echo "<option value=\"$opt\" $sel>$opt</option>";
          }
          ?>
        </select>
      </td>
      <td>
        <input type="number" class="participation" min="0" max="5"
               value="<?= htmlspecialchars($st['participation_score'] ?? '') ?>">
      </td>
    </tr>
    <?php endforeach; ?>
  </table>

  <button id="saveBtn">Enregistrer</button>
</main>

<script>
$(function(){
  $('#saveBtn').on('click', function(){
    let records = [];
    $('#attendanceTable tr[data-student-id]').each(function(){
      let sid = $(this).data('student-id');
      let status = $(this).find('.status').val();
      let part = $(this).find('.participation').val();
      records.push({
        student_id: sid,
        status: status,
        participation: part
      });
    });

    $.post('professor_update_attendance.php', {
      session_id: <?= $sessionId ?>,
      records: JSON.stringify(records)
    }).done(function(resp){
      try {
        let r = JSON.parse(resp);
        if (r.success) alert('Enregistré');
        else alert('Erreur : ' + (r.error || ''));
      } catch(e) {
        alert('Réponse invalide');
      }
    }).fail(function(){
      alert('Erreur AJAX');
    });
  });
});
</script>
</body>
</html>