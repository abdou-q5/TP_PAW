<?php
require_once '../app/config/db.php';
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getConnection();
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'student') {
            header("Location: student_home.php");
        } elseif ($user['role'] === 'professor') {
            header("Location: professor_home.php");
        } else {
            header("Location: admin_home.php");
        }
        exit;
    } else {
        $error = "Identifiants invalides.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .login-card {
      background: #ffffff;
      padding: 2rem 2.5rem;
      border-radius: 12px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
      width: 320px;
    }

    h1 {
      margin: 0 0 1.5rem;
      text-align: center;
      font-size: 1.6rem;
      color: #333;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
    }

    input {
      padding: 0.75rem 0.9rem;
      border-radius: 8px;
      border: 1px solid #ddd;
      font-size: 0.95rem;
      transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s;
    }

    input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
      background-color: #fdfdff;
    }

    button {
      margin-top: 0.5rem;
      padding: 0.75rem;
      border-radius: 8px;
      border: none;
      background: #667eea;
      color: #fff;
      font-weight: 600;
      font-size: 0.95rem;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 8px 18px rgba(102, 126, 234, 0.4);
    }

    button:hover {
      background: #5563d6;
    }

    button:active {
      transform: translateY(1px);
      box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
    }
    </style>


</head>
<body>
<h1>Login</h1>
<form method="post">
  <input name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>
<?php if ($error): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
</body>
</html>