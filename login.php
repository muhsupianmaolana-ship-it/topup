<?php
session_start();
require 'koneksi.php';

// Kalau sudah login, langsung ke index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error = 'Username/email dan password wajib diisi.';
    } else {
        try {
            // Cari user berdasarkan username atau email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$login, $login]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Username/email atau password salah.';
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
  <title>Masuk - TopUpKu</title>
  <link rel="stylesheet" href="web.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">

  <canvas id="bg-canvas"></canvas>

  <div class="auth-wrapper">

    <!-- Logo -->
    <a href="index.php" class="auth-logo">
      <span class="brand-icon">⚡</span>
      <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
    </a>

    <div class="auth-card">
      <div class="auth-header">
        <h2>Selamat Datang</h2>
        <p>Masuk ke akun TopUpKu kamu</p>
      </div>

      <?php if ($error): ?>
        <div class="auth-alert error">❌ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" class="auth-form">
        <div class="field-group">
          <label class="field-label">Username atau Email</label>
          <div class="field-input-wrap">
            <span class="field-icon">👤</span>
            <input type="text" name="login" class="field-input"
                   placeholder="Masukkan username atau email"
                   value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                   required autofocus>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Password</label>
          <div class="field-input-wrap">
            <span class="field-icon">🔒</span>
            <input type="password" name="password" id="passwordInput" class="field-input"
                   placeholder="Masukkan password" required>
            <button type="button" class="toggle-pw" onclick="togglePassword()">👁</button>
          </div>
        </div>

        <button type="submit" class="btn btn-neon btn-full">
          ⚡ Masuk Sekarang
        </button>
      </form>

      <div class="auth-divider"><span>atau</span></div>

      <p class="auth-switch">
        Belum punya akun?
        <a href="register.php" class="auth-link">Daftar Sekarang</a>
      </p>
    </div>

    <p class="auth-back">
      <a href="index.php" class="auth-link">← Kembali ke Beranda</a>
    </p>

  </div>

  <script src="web.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById('passwordInput');
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>