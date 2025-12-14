<?php
// index.php
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Gestion Présence</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
/* --- (copie le CSS que tu avais déjà) --- */
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Poppins",sans-serif;background:#f4f6fb;color:#333;display:flex;}
#navbar{width:200px;background:linear-gradient(180deg,#2c3e50 0%,#34495e 100%);color:white;height:100vh;position:fixed;left:0;top:0;padding:20px 0;box-shadow:4px 0 15px rgba(0,0,0,0.2);overflow-y:auto}
#navbar h2{text-align:center;font-size:22px;margin-bottom:30px;color:#ecf0f1;padding:0 15px;border-bottom:2px solid rgba(255,255,255,0.2);padding-bottom:15px}
#navbar ul{list-style:none}
#navbar ul li{padding:15px 20px;cursor:pointer;transition:all .3s ease;border-left:4px solid transparent}
#navbar ul li:hover{background:rgba(255,255,255,0.1);border-left-color:#3498db}
#navbar ul li.active{background:rgba(52,152,219,.3);border-left-color:#3498db;font-weight:600}
#content{margin-left:250px;width:calc(100% - 250px);min-height:100vh}
#presentation{text-align:center;padding:60px 20px 40px 20px;background:linear-gradient(135deg,#333 0%,#333 100%);color:white;border-bottom-left-radius:50% 10%;border-bottom-right-radius:50% 10%;box-shadow:0 6px 20px rgba(0,0,0,.15)}
#presentation h1{font-size:42px;margin-bottom:10px;letter-spacing:1px;animation:fadeIn 1.2s ease-in-out}
@keyframes fadeIn{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
.page{display:none}.page.active{display:block}
.container{width:90%;max-width:1100px;margin:30px auto}
.card{background:white;border-radius:12px;padding:25px;box-shadow:0 4px 15px rgba(0,0,0,.05);margin-bottom:25px;overflow-x:auto}
h2{color:#007bff;border-left:5px solid #007bff;padding-left:10px;margin-bottom:20px}
label{display:block;margin-top:10px;font-weight:500;font-family:Arial,Helvetica,sans-serif;color:black}
input{padding:8px;width:250px;border-radius:6px;border:1px solid #ccc;margin-top:5px;transition:all .2s}input:focus{border-color:#007bff;outline:none}
span{color:red;font-size:13px}
.bo{margin-top:15px;padding:10px 20px;background-color:#333;color:white;border:none;border-radius:6px;cursor:pointer;font-size:16px;transition:background-color .3s}
.bo:hover{background-color:#555}
#attendanceTable{border-collapse:collapse;width:100%;max-width:100%;margin-top:20px;font-size:14px;table-layout:fixed;display:block;overflow-x:auto}
#attendanceTable th,#attendanceTable td{border:1px solid #ddd;padding:6px 4px;text-align:center;word-wrap:break-word;white-space:normal}
#attendanceTable tr:nth-child(even){background-color:#fafbff}#attendanceTable tr:hover{background-color:#eaf4ff}
td.clickable{cursor:pointer;transition:background .3s}
.green{background-color:#c3f7c0!important}.yellow{background-color:#fff6b2!important}.red{background-color:#ffc2c2!important}
#reportSection{display:none;margin-top:30px;border-top:2px solid #007bff;padding-top:20px}
canvas{margin-top:20px}
#buttonsZone{display:flex;gap:15px;flex-wrap:wrap;justify-content:center;margin-top:25px}
.btn{border:none;padding:12px 25px;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:all .25s ease;color:white;letter-spacing:.3px;box-shadow:0 4px 10px rgba(0,0,0,.1)}.btn:hover{transform:translateY(-3px);box-shadow:0 6px 15px rgba(0,0,0,.15)}
.btn-blue{background:linear-gradient(45deg,#fff6b2,#ffc2c2)}.btn-green{background:linear-gradient(45deg,#7ee27d,#94c696)}.btn-orange{background:linear-gradient(45deg,#ecd207,#ecd207)}
tr.highlight{background-color:#d7ebff!important}
@keyframes blink{0%{background-color:#a3f0a2}50%{background-color:#7ee27d}100%{background-color:#a3f0a2}}
.excellent{animation:blink 1s infinite}
.di1{text-align:center;margin-top:20px}
#in1{padding:8px;width:500px;border-radius:6px;border:1px solid black;margin-top:5px;transition:all .2s;font-family:'Courier New',Courier,monospace}
#in1:focus{border-color:#007bff;outline:none}
.a,.b{margin-left:10px;padding:10px 20px;background-color:#333;color:white;border:none;border-radius:6px;cursor:pointer;font-size:16px}
.a:hover,.b:hover{background-color:#555}
.welcome-content{text-align:center;padding:80px 20px}
.welcome-content h1{font-size:48px;color:#2c3e50;margin-bottom:20px}
.welcome-content p{font-size:20px;color:#7f8c8d;max-width:700px;margin:0 auto;line-height:1.6}
@media (max-width:768px){#navbar{width:100%;height:auto;position:relative}#content{margin-left:0;width:100%}#in1{width:100%;max-width:400px}}
</style>
</head>
<body>

<nav id="navbar">
  <h2>📚 Menu</h2>
  <ul>
    <li class="nav-item active" data-page="home">🏠 Accueil</li>
    <li class="nav-item" data-page="addStudent">➕ Ajouter Étudiant</li>
    <li class="nav-item" data-page="attendance">✅ Tableau de Présence</li>
    <li class="nav-item" data-page="report">📊 Rapport</li>
  </ul>
</nav>

<div id="content">
  <section id="presentation">
    <h1>Programme de Présence</h1>
    <hr>
  </section>

  <div id="home" class="page active">
    <div class="container">
      <div class="card welcome-content">
        <h1>Bienvenue!</h1>
        <p>Gérez la présence et la participation. Le backend MySQL est connecté via l'API PHP.</p>
      </div>
    </div>
  </div>

  <div id="addStudent" class="page">
    <div class="container">
      <div class="card">
        <h2>Ajouter un Étudiant</h2>
        <form id="studentForm" novalidate>
          <label>Student ID:
            <input type="text" id="studentId"><span id="idError"></span>
          </label>
          <label>Last Name:
            <input type="text" id="lastName"><span id="lastError"></span>
          </label>
          <label>First Name:
            <input type="text" id="firstName"><span id="firstError"></span>
          </label>
          <label>Email:
            <input type="text" id="email"><span id="emailError"></span>
          </label>
          <button type="submit" class="bo">Add Student</button>
        </form>
      </div>
    </div>
  </div>

  <div id="attendance" class="page">
    <div class="container">
      <div class="di1">
        <label for="in1">Recherche par nom</label>
        <input type="text" id="in1" placeholder="Rechercher...">
        <button id="b1" class="a">Trier par absence</button>
        <button id="b2" class="b">Trier par participation</button>
      </div>
      <br>

      <div class="card">
        <h2>Tableau Interactif de Présence</h2>
        <table id="attendanceTable">
          <thead>
            <tr>
              <th>Last Name</th><th>First Name</th>
              <th colspan="12">Sessions</th>
              <th>Absences</th><th>Participations</th><th>Message</th>
            </tr>
            <tr id="sessionsRow">
              <th></th><th></th>
            </tr>
          </thead>
          <tbody>
            <!-- rempli dynamiquement -->
          </tbody>
        </table>

        <div id="buttonsZone">
          <button class="btn btn-green" id="highlightExcellent">Highlight Excellent Students</button>
          <button class="btn btn-orange" id="resetColors">Reset Colors</button>
        </div>
      </div>
    </div>
  </div>

  <div id="report" class="page">
    <div class="container">
      <div class="card">
        <h2>Rapport de la Classe</h2>
        <button class="btn btn-blue" id="showReport">Générer le Rapport</button>
        <div id="reportSection">
          <h3>Statistiques</h3>
          <p id="reportText"></p>
          <canvas id="reportChart" width="400" height="200"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Navigation
$('.nav-item').on('click', function(){
  $('.nav-item').removeClass('active');
  $(this).addClass('active');
  $('.page').removeClass('active');
  $('#' + $(this).data('page')).addClass('active');
});

// Variables globales
let SESSIONS = 6;
let ATT_DATA = []; // loaded from server

// Charger la structure (sessions) + données
function loadAttendance() {
  fetch('api/get_attendance.php').then(r=>r.json()).then(data=>{
    SESSIONS = data.sessions || 6;
    ATT_DATA = data.students || [];
    buildTable();
  })
  .catch(e => { alert('Erreur chargement: ' + e); console.error(e); });
}

function buildTable(){
  // header sessions
  const sessionsRow = $('#sessionsRow');
  sessionsRow.empty();
  sessionsRow.append('<th></th><th></th>');
  for (let i=1;i<=SESSIONS;i++){
    sessionsRow.append('<th>S'+i+' (P)</th><th>S'+i+' (Par)</th>');
  }
  sessionsRow.append('<th></th><th></th><th></th>');

  // body
  const tbody = $('#attendanceTable tbody');
  tbody.empty();

  ATT_DATA.forEach(s=>{
    const idDb = s.id;
    let row = $('<tr></tr>').attr('data-id', idDb);
    row.append(`<td>${escapeHtml(s.last_name)}</td>`);
    row.append(`<td>${escapeHtml(s.first_name)}</td>`);

    for (let i=1;i<=SESSIONS;i++){
      const att = s.attendance && s.attendance[i] ? s.attendance[i] : {presence:0, participation:0};
      const p = att.presence ? '✓' : '';
      const par = att.participation ? '✓' : '';
      row.append(`<td class="clickable cell-pres" data-session="${i}" data-field="presence">${p}</td>`);
      row.append(`<td class="clickable cell-par" data-session="${i}" data-field="participation">${par}</td>`);
    }

    row.append('<td class="absCell"></td><td class="parCell"></td><td class="msgCell"></td>');
    tbody.append(row);
    updateRow(row[0]);
  });

  attachCellHandlers();
}

// escape simple
function escapeHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// Met à jour une ligne (calcul absences/participations/messages/colors)
function updateRow(rowEl){
  const row = $(rowEl);
  const cells = row.find('td');
  let absences = 0, participations = 0;
  for (let i=2; i< 2 + SESSIONS*2; i+=2){
    if ($(cells[i]).text().trim() === '') absences++;
  }
  for (let i=3; i< 2 + SESSIONS*2; i+=2){
    if ($(cells[i]).text().trim() === '✓') participations++;
  }
  row.find('.absCell').text(absences + ' Abs');
  row.find('.parCell').text(participations + ' Par');

  row.removeClass('green yellow red excellent').css({});
  if (absences < 3) row.addClass('green');
  else if (absences <= 4) row.addClass('yellow');
  else row.addClass('red');

  let msg = '';
  if (absences < 3 && participations >= 3) msg = 'Good attendance – Excellent participation';
  else if (absences >= 5) msg = 'Excluded – too many absences';
  else if (absences < 3 && participations < 3) msg = 'Good attendance – You need to participate more';
  else if (absences >= 3 && participations >= 3) msg = 'Warning – low attendance but good participation';
  else msg = 'Warning – low attendance and low participation';
  row.find('.msgCell').text(msg);
}

// Attacher écouteurs aux cellules cliquables
function attachCellHandlers(){
  $('#attendanceTable td.clickable').off('click').on('click', function(){
    const cell = $(this);
    const row = cell.closest('tr');
    const sid = row.data('id');
    const sessionIndex = parseInt(cell.attr('data-session'));
    const field = cell.attr('data-field'); // presence or participation

    // toggle UI immediately for responsiveness
    const newVal = cell.text().trim() === '' ? '✓' : '';
    cell.text(newVal);
    updateRow(row[0]);

    // envoyer toggle au serveur
    fetch('api/toggle_field.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ studentIdDb: sid, sessionIndex: sessionIndex, field: field })
    })
    .then(r => r.json())
    .then(resp => {
      if (!resp.success) {
        // revert si erreur
        cell.text(cell.text().trim() === '' ? '✓' : '');
        updateRow(row[0]);
        alert('Erreur sauvegarde: ' + (resp.message || JSON.stringify(resp)));
      }
    })
    .catch(err=>{
      console.error(err);
      // revert
      cell.text(cell.text().trim() === '' ? '✓' : '');
      updateRow(row[0]);
      alert('Erreur réseau lors de la sauvegarde');
    });
  });
}

// Recherche
$("#in1").on("keyup", function() {
  const value = $(this).val().toLowerCase();
  $("#attendanceTable tbody tr").filter(function() {
    const lastName = $(this).find("td:eq(0)").text().toLowerCase();
    const firstName = $(this).find("td:eq(1)").text().toLowerCase();
    $(this).toggle(lastName.indexOf(value) > -1 || firstName.indexOf(value) > -1);
  });
});

// Trier par absence
$("#b1").on("click", function() {
  const rows = $("#attendanceTable tbody tr").get();
  rows.sort(function(a, b) {
    const A = parseInt($(a).find("td.absCell").text()) || 0;
    const B = parseInt($(b).find("td.absCell").text()) || 0;
    return A - B;
  });
  $.each(rows, function(index, row) { $("#attendanceTable tbody").append(row); });
});

// Trier par participation
$("#b2").on("click", function() {
  const rows = $("#attendanceTable tbody tr").get();
  rows.sort(function(a, b) {
    const A = parseInt($(a).find("td.parCell").text()) || 0;
    const B = parseInt($(b).find("td.parCell").text()) || 0;
    return B - A;
  });
  $.each(rows, function(index, row) { $("#attendanceTable tbody").append(row); });
});

// Highlight excellent
$("#highlightExcellent").on("click", function() {
  let count = 0;
  $("#attendanceTable tbody tr").each(function() {
    const abs = parseInt($(this).find('.absCell').text()) || 0;
    const par = parseInt($(this).find('.parCell').text()) || 0;
    $(this).removeClass('excellent');
    if (abs <= 2 && par >= 3) { $(this).addClass('excellent'); count++; }
  });
  alert('🌟 ' + count + ' excellent student(s) highlighted!');
});

// Reset
$("#resetColors").on("click", function() {
  $("#attendanceTable tbody tr").each(function() {
    $(this).removeClass('excellent green yellow red').removeAttr('style');
    updateRow(this);
  });
});

// Formulaire ajout étudiant (validation simple)
$("#studentForm").on("submit", function(e){
  e.preventDefault();

  const student = {
    studentId: $("#studentId").val().trim(),
    lastName: $("#lastName").val().trim(),
    firstName: $("#firstName").val().trim(),
    email: $("#email").val().trim()
  };

  let valid = true;
  $("#idError,#lastError,#firstError,#emailError").text('');

  if (!/^[0-9]+$/.test(student.studentId)) { $("#idError").text('ID must contain only numbers.'); valid=false; }
  if (!/^[a-zA-Z]+$/.test(student.lastName)) { $("#lastError").text('Last name letters only.'); valid=false; }
  if (!/^[a-zA-Z]+$/.test(student.firstName)) { $("#firstError").text('First name letters only.'); valid=false; }
  if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(student.email)) { $("#emailError").text('Enter a valid email.'); valid=false; }

  if (!valid) return;

  fetch('api/add_student.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(student)
  })
  .then(r=>r.json())
  .then(resp =>{
    if (resp.success) {
      alert('✅ Student added successfully!');
      // recharger
      loadAttendance();
      $('#studentForm')[0].reset();
    } else {
      alert('Erreur ajout: ' + (resp.message || JSON.stringify(resp)));
    }
  })
  .catch(err => { console.error(err); alert('Erreur réseau'); });
});

// Rapport (chart)
let reportChart = null;
$("#showReport").on('click', function(){
  const rows = $("#attendanceTable tbody tr");
  const totalStudents = rows.length;
  let totalPresents = 0, totalParticipants = 0;

  rows.each(function(){
    const c = $(this).find('td');
    for (let i=2;i<2+SESSIONS*2;i+=2) if ($(c[i]).text().trim() === '✓') totalPresents++;
    for (let i=3;i<2+SESSIONS*2;i+=2) if ($(c[i]).text().trim() === '✓') totalParticipants++;
  });

  $("#reportSection").show();
  $("#reportText").html(`<strong>Total Students:</strong> ${totalStudents}<br><strong>Total Presences:</strong> ${totalPresents}<br><strong>Total Participations:</strong> ${totalParticipants}`);

  const ctx = document.getElementById('reportChart').getContext('2d');
  if (reportChart) reportChart.destroy();
  reportChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Students','Presences','Participations'],
      datasets: [{ label: 'Class Report', data: [totalStudents,totalPresents,totalParticipants], backgroundColor: ['#007bff','#28a745','#ffc107'] }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true, ticks:{ stepSize:1 } } } }
  });
});

// initial load
$(document).ready(function(){ loadAttendance(); });
</script>

</body>
</html>
