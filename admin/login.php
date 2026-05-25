<?php
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    $errors[] = 'Username dan password wajib diisi.';
  } else {
    $dbPath = dirname(__DIR__) . '/data/ruangkayu.db';
    if (!is_file($dbPath)) {
      // DB belum ada — redirect ke setup (api akan create saat dipanggil)
      header('Location: /api/index.php');
      exit;
    }
    try {
      $db = new PDO("sqlite:$dbPath");
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $db->prepare("SELECT * FROM admin WHERE username = ?");
      $stmt->execute([$username]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && password_verify($password, $row['password'])) {
        // Simple session — token-based for API calls
        $token = base64_encode($username . ':' . $password);
        session_start();
        $_SESSION['admin_token'] = $token;
        $_SESSION['admin_user']  = $username;
        header('Location: /admin/dashboard.php');
        exit;
      } else {
        $errors[] = 'Username atau password salah.';
      }
    } catch (PDOException $e) {
      $errors[] = 'Tidak dapat terhubung ke database.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Ruang Kayu Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">

<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">Ruang <i>Kayu</i></div>
    <p class="login-sub">Admin Dashboard</p>

    <?php if ($errors): ?>
      <div class="login-err">
        <?php foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="login-form">
      <div class="fgrp">
        <label for="u">Username</label>
        <input type="text" id="u" name="username" autocomplete="username" required>
      </div>
      <div class="fgrp">
        <label for="p">Password</label>
        <input type="password" id="p" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn-p">Masuk</button>
    </form>

    <a href="/" class="login-back">← Kembali ke Website</a>
  </div>
</div>

</body>
</html>