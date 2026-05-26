<?php
require 'auth.php';
require '../koneksi.php';

$success = '';
$error   = '';

// GANTI USERNAME
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'username') {
    $username_baru = trim($_POST['username_baru']);
    $password_konfirmasi = $_POST['password_konfirmasi'];

    if (!$username_baru || !$password_konfirmasi) {
        $error = 'Harap isi semua field.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!password_verify($password_konfirmasi, $admin['password_hash'])) {
            $error = 'Password konfirmasi salah.';
        } else {
            $cek = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
            $cek->execute([$username_baru, $_SESSION['admin_id']]);
            if ($cek->fetch()) {
                $error = 'Username sudah dipakai.';
            } else {
                $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?")
                    ->execute([$username_baru, $_SESSION['admin_id']]);
                $_SESSION['admin_name'] = $username_baru;
                $success = 'Username berhasil diubah!';
            }
        }
    }
}

// GANTI PASSWORD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'password') {
    $password_lama  = $_POST['password_lama'];
    $password_baru  = $_POST['password_baru'];
    $password_ulang = $_POST['password_ulang'];

    if (!$password_lama || !$password_baru || !$password_ulang) {
        $error = 'Harap isi semua field.';
    } elseif (strlen($password_baru) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } elseif ($password_baru !== $password_ulang) {
        $error = 'Password baru dan konfirmasi tidak cocok.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!password_verify($password_lama, $admin['password_hash'])) {
            $error = 'Password lama salah.';
        } else {
            $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?")
                ->execute([$hash_baru, $_SESSION['admin_id']]);
            $success = 'Password berhasil diubah!';
        }
    }
}

// Ambil data admin terkini
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Admin - TopUpKu</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: #f5f5f0; color: #1a1a1a; }

    .navbar {
      background: #fff;
      border-bottom: 1px solid #e5e5e0;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .navbar .brand { font-size: 16px; font-weight: 700; color: #1a1a1a; text-decoration: none; }
    .navbar .brand span { color: #6c5ce7; }
    .navbar-right { display: flex; align-items: center; gap: 16px; font-size: 13px; color: #666; }
    .navbar-right a { color: #c0392b; text-decoration: none; font-size: 13px; }

    .container { max-width: 600px; margin: 0 auto; padding: 24px 16px; }
    .page-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; }

    .alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
    .alert-success { background: #eafaf1; color: #1e8449; border: 1px solid #a9dfbf; }
    .alert-error   { background: #fff0f0; color: #c0392b; border: 1px solid #fcd; }

    .card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e5e5e0;
      padding: 24px;
      margin-bottom: 20px;
    }
    .card-title {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 4px;
    }
    .card-sub {
      font-size: 12px;
      color: #888;
      margin-bottom: 20px;
    }

    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; font-size: 12px; font-weight: 500; color: #555; margin-bottom: 5px; }
    .form-group input {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 13px;
      color: #1a1a1a;
      outline: none;
      transition: border-color .2s;
    }
    .form-group input:focus { border-color: #6c5ce7; }
    .form-group .hint { font-size: 11px; color: #aaa; margin-top: 4px; }

    .btn { padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
    .btn-primary { background: #1a1a1a; color: #fff; }
    .btn-primary:hover { background: #333; }

    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f5f5f0; font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #888; }
    .info-value { font-weight: 500; }
    .badge-role { background: #eef2ff; color: #3730a3; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 500; }
  </style>
</head>
<body>

<nav class="navbar">
  <a href="paket.php" class="brand">⚡ TopUp<span>Ku</span> <span style="font-weight:400;color:#888;font-size:13px;">Admin</span></a>
  <div class="navbar-right">
    <span>👤 <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
    <a href="logout.php">Keluar</a>
  </div>
</nav>

<div class="container">
  <div class="page-title">Profil Admin</div>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Info Akun -->
  <div class="card">
    <div class="card-title">Info Akun</div>
    <div class="card-sub">Data akun admin yang sedang login</div>
    <div class="info-row">
      <span class="info-label">Username</span>
      <span class="info-value"><?= htmlspecialchars($admin['username']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Role</span>
      <span class="badge-role"><?= htmlspecialchars($admin['role']) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Login terakhir</span>
      <span class="info-value"><?= $admin['last_login'] ?? '-' ?></span>
    </div>
  </div>

  <!-- Ganti Username -->
  <div class="card">
    <div class="card-title">Ganti Username</div>
    <div class="card-sub">Masukkan username baru dan konfirmasi dengan password</div>
    <form method="POST">
      <input type="hidden" name="action" value="username">
      <div class="form-group">
        <label>Username Baru</label>
        <input type="text" name="username_baru" placeholder="Masukkan username baru" autocomplete="off">
      </div>
      <div class="form-group">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_konfirmasi" placeholder="Masukkan password kamu">
        <div class="hint">Masukkan password saat ini untuk konfirmasi</div>
      </div>
      <button type="submit" class="btn btn-primary">Simpan Username</button>
    </form>
  </div>

  <!-- Ganti Password -->
  <div class="card">
    <div class="card-title">Ganti Password</div>
    <div class="card-sub">Password minimal 6 karakter</div>
    <form method="POST">
      <input type="hidden" name="action" value="password">
      <div class="form-group">
        <label>Password Lama</label>
        <input type="password" name="password_lama" placeholder="Masukkan password lama">
      </div>
      <div class="form-group">
        <label>Password Baru</label>
        <input type="password" name="password_baru" placeholder="Masukkan password baru">
      </div>
      <div class="form-group">
        <label>Ulangi Password Baru</label>
        <input type="password" name="password_ulang" placeholder="Ketik ulang password baru">
      </div>
      <button type="submit" class="btn btn-primary">Simpan Password</button>
    </form>
  </div>

</div>
</body>
</html>