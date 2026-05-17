<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        try {
            // Cek username/email sudah dipakai
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $check->execute([$username, $email]);
            if ($check->fetch()) {
                $error = 'Username atau email sudah terdaftar.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hash]);
                $success = 'Akun berhasil dibuat! Silakan masuk.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan server. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - TopUpKu</title>
  <link rel="stylesheet" href="web.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">

  <canvas id="bg-canvas"></canvas>

  <div class="auth-wrapper">

    <a href="index.php" class="auth-logo">
      <span class="brand-icon">⚡</span>
      <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
    </a>

    <div class="auth-card">
      <div class="auth-header">
        <h2>Buat Akun Baru</h2>
        <p>Daftar dan mulai top up game favoritmu</p>
      </div>

      <?php if ($error): ?>
        <div class="auth-alert error">❌ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="auth-alert success">✅ <?= htmlspecialchars($success) ?>
          <a href="login.php" class="auth-link"> Masuk sekarang →</a>
        </div>
      <?php endif; ?>

      <form method="POST" class="auth-form">
        <div class="field-group">
          <label class="field-label">Username</label>
          <div class="field-input-wrap">
            <span class="field-icon">🎮</span>
            <input type="text" name="username" class="field-input"
                   placeholder="Minimal 3 karakter"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   required autofocus>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Email</label>
          <div class="field-input-wrap">
            <span class="field-icon">📧</span>
            <input type="email" name="email" class="field-input"
                   placeholder="contoh@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   required>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Password</label>
          <div class="field-input-wrap">
            <span class="field-icon">🔒</span>
            <input type="password" name="password" id="passwordInput" class="field-input"
                   placeholder="Minimal 6 karakter" required>
            <button type="button" class="toggle-pw" onclick="togglePassword('passwordInput')">👁</button>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Konfirmasi Password</label>
          <div class="field-input-wrap">
            <span class="field-icon">🔑</span>
            <input type="password" name="confirm" id="confirmInput" class="field-input"
                   placeholder="Ulangi password" required>
            <button type="button" class="toggle-pw" onclick="togglePassword('confirmInput')">👁</button>
          </div>
        </div>

        <button type="submit" class="btn btn-neon btn-full">
          🚀 Daftar Sekarang
        </button>
      </form>

      <div class="auth-divider"><span>atau</span></div>

      <p class="auth-switch">
        Sudah punya akun?
        <a href="login.php" class="auth-link">Masuk di sini</a>
      </p>
    </div>

    <p class="auth-back">
      <a href="index.php" class="auth-link">← Kembali ke Beranda</a>
    </p>

  </div>

  <script src="web.js"></script>
  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>