<?php
require_once '../app/includes/auth.php';
requireRole('student');
require_once '../app/config/db.php';

$db = getConnection();
$studentId = $_SESSION['user_id'];
$courseGroupId = (int)($_GET['cg'] ?? 0);

$stmt = $db->prepare("
  SELECT ses.id AS session_id, ses.session_date,
  ar.status, jr.id AS justif_id, jr.status AS justif_status
  FROM attendance_sessions ses
  LEFT JOIN attendance_records ar 
    ON ar.session_id = ses.id AND ar.student_id = :sid
  LEFT JOIN justification_requests jr
    ON jr.attendance_record_id = ar.id
  WHERE ses.course_group_id = :cg
  ORDER BY ses.session_date ASC
");
$stmt->execute([':sid' => $studentId, ':cg' => $courseGroupId]);
$sessions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mes présences</title>
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
      font-size: 1.4rem;
      letter-spacing: 0.03em;
    }

    /* ===== MAIN ===== */
    main {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 0 1.5rem 3rem;
    }

    /* ===== TABLE ===== */
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

    /* Colonne Status un peu mise en avant */
    th:nth-child(2),
    td:nth-child(2) {
      font-weight: 600;
      color: #2563eb;
    }

    /* ===== BOUTON JUSTIFIER ===== */
    .justifBtn {
      padding: 0.4rem 0.9rem;
      border-radius: 999px;
      border: none;
      background: #f59e0b;
      color: #1f2933;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 6px 14px rgba(245, 158, 11, 0.4);
    }

    .justifBtn:hover {
      background: #d97706;
      transform: translateY(-1px);
    }

    .justifBtn:active {
      transform: translateY(0);
      box-shadow: 0 3px 7px rgba(245, 158, 11, 0.4);
    }

    /* ===== MODAL JUSTIFICATION ===== */
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.6);
      display: flex;               /* sera redéfini à "flex" par JS au lieu de "none" inline */
      align-items: center;
      justify-content: center;
      padding: 1rem;
      z-index: 50;
    }

    #justifForm {
      background: #ffffff;
      padding: 1.5rem 1.7rem;
      border-radius: 14px;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 18px 45px rgba(15, 23, 42, 0.35);
      border: 1px solid #e5e7eb;
      font-size: 0.9rem;
    }

    #justifForm label {
      font-weight: 600;
      color: #374151;
    }

    #justifForm textarea {
      width: 100%;
      min-height: 80px;
      margin: 0.3rem 0 0.9rem;
      padding: 0.6rem 0.7rem;
      border-radius: 8px;
      border: 1px solid #d1d5db;
      background: #f9fafb;
      resize: vertical;
      font-size: 0.9rem;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    #justifForm textarea:focus {
      outline: none;
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25);
      background-color: #fdfdff;
    }

    #justifForm input[type="file"] {
      margin: 0.3rem 0 1rem;
      font-size: 0.85rem;
    }

    #justifForm button {
      padding: 0.5rem 1.1rem;
      border-radius: 999px;
      border: none;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      margin-right: 0.4rem;
    }

    #justifForm button[type="submit"] {
      background: #2563eb;
      color: #fff;
      box-shadow: 0 8px 18px rgba(37, 99, 235, 0.4);
    }

    #justifForm button[type="submit"]:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    #justifForm button[type="submit"]:active {
      transform: translateY(0);
      box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
    }

    #closeModal {
      background: #e5e7eb;
      color: #111827;
    }

    #closeModal:hover {
      background: #d1d5db;
      transform: translateY(-1px);
    }

    #closeModal:active {
      transform: translateY(0);
      box-shadow: 0 3px 7px rgba(148, 163, 184, 0.4);
    }

    /* Responsive */
    @media (max-width: 700px) {
      table, thead, tbody, th, td, tr {
        font-size: 0.8rem;
      }

      main {
        padding: 0 1rem 2rem;
      }

      #justifForm {
        padding: 1.2rem 1.3rem;
      }
    }
  </style>
</head>
<body>
<header>
  <h1>Présence par cours</h1>
</header>
<main>
  <table>
    <tr><th>Date</th><th>Status</th><th>Justification</th></tr>
    <?php foreach ($sessions as $s): ?>
    <tr>
      <td><?= htmlspecialchars($s['session_date']) ?></td>
      <td><?= htmlspecialchars($s['status'] ?? 'N/A') ?></td>
      <td>
        <?php if (($s['status'] ?? '') === 'absent' && !$s['justif_id']): ?>
          <button class="justifBtn" data-session-id="<?= $s['session_id'] ?>">Justifier</button>
        <?php elseif ($s['justif_id']): ?>
          <?= htmlspecialchars($s['justif_status']) ?>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>


    </tr>
    <?php endforeach; ?>
  </table>

  <div id="justifModal" class="modal" style="display:none;">
    <form id="justifForm" enctype="multipart/form-data">
      <input type="hidden" name="session_id" id="session_id">
      <label>Raison :</label><br>
      <textarea name="reason" required></textarea><br>
      <label>Fichier (PDF/JPG/PNG) :</label><br>
      <input type="file" name="file" required><br><br>
      <button type="submit">Envoyer</button>
      <button type="button" id="closeModal">Annuler</button>
    </form>
  </div>
</main>

<script>
$(function(){
  $('.justifBtn').on('click', function(){
    $('#session_id').val($(this).data('session-id'));
    $('#justifModal').show();
  });
  $('#closeModal').on('click', function(){
    $('#justifModal').hide();
  });

  $('#justifForm').on('submit', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: 'student_submit_justification.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false
    }).done(function(resp){
      try {
        let r = JSON.parse(resp);
        if (r.success) {
          alert('Envoyé');
          location.reload();
        } else {
          alert('Erreur : ' + (r.error || ''));
        }
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