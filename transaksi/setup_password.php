<?php
// ============================================
// setup_password.php
// Gunakan file ini SEKALI untuk set/ganti password
// Setelah selesai, HAPUS file ini dari server!
// ============================================

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'tugas_web';

$message = '';
$type    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm      = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($new_password)) {
        $message = 'Username dan password tidak boleh kosong.';
        $type    = 'error';
    } elseif ($new_password !== $confirm) {
        $message = 'Konfirmasi password tidak cocok.';
        $type    = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'Password minimal 6 karakter.';
        $type    = 'error';
    } else {
        $conn = new mysqli($host, $user, $pass, $db);
        if ($conn->connect_error) {
            $message = 'Koneksi database gagal: ' . $conn->connect_error;
            $type    = 'error';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            // Cek apakah username sudah ada
            $check = $conn->prepare("SELECT id FROM admin_transaksi WHERE username = ?");
            $check->bind_param("s", $username);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;
            $check->close();

            if ($exists) {
                // Update password
                $stmt = $conn->prepare("UPDATE admin_transaksi SET password = ? WHERE username = ?");
                $stmt->bind_param("ss", $hashed, $username);
                $stmt->execute();
                $stmt->close();
                $message = "✅ Password untuk '$username' berhasil diperbarui! Hapus file ini sekarang.";
                $type    = 'success';
            } else {
                // Insert akun baru
                $stmt = $conn->prepare("INSERT INTO admin_transaksi (username, password, nama) VALUES (?, ?, ?)");
                $nama = 'Admin Transaksi';
                $stmt->bind_param("sss", $username, $hashed, $nama);
                $stmt->execute();
                $stmt->close();
                $message = "✅ Akun '$username' berhasil dibuat! Hapus file ini sekarang.";
                $type    = 'success';
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Password — Admin Transaksi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f0c29;
            color: #e0e0ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #1b1b3a;
            border: 1px solid rgba(0,245,255,0.18);
            border-radius: 16px;
            padding: 36px 32px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .card h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #00f5ff;
            margin-bottom: 6px;
        }
        .card p.sub {
            font-size: 0.78rem;
            color: #8888aa;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .warning {
            background: rgba(255,200,0,0.08);
            border: 1px solid rgba(255,200,0,0.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.78rem;
            color: #ffcc00;
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #00f5ff;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            background: #13112a;
            border: 1px solid rgba(0,245,255,0.18);
            border-radius: 8px;
            padding: 11px 14px;
            color: #e0e0ff;
            font-size: 0.9rem;
            outline: none;
            margin-bottom: 16px;
            transition: border-color 0.2s;
        }
        input:focus { border-color: #00f5ff; }
        input::placeholder { color: #8888aa; }
        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #00f5ff, #0099ff);
            color: #000;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        button:hover { opacity: 0.88; }
        .alert {
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 0.84rem;
            margin-bottom: 20px;
        }
        .alert.success { background: rgba(0,255,136,0.08); border: 1px solid rgba(0,255,136,0.3); color: #00ff88; }
        .alert.error   { background: rgba(255,68,68,0.08);  border: 1px solid rgba(255,68,68,0.3);  color: #ff7070; }
    </style>
</head>
<body>
<div class="card">
    <h2>🔑 Setup Password Admin Transaksi</h2>
    <p class="sub">Buat atau ganti password akun admin transaksi. Hapus file ini setelah selesai.</p>

    <div class="warning">⚠️ Hapus file ini dari server setelah digunakan!</div>

    <?php if ($message): ?>
    <div class="alert <?php echo $type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" placeholder="admin_transaksi" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>

        <label>Password Baru</label>
        <input type="password" name="new_password" placeholder="Minimal 6 karakter" required>

        <label>Konfirmasi Password</label>
        <input type="password" name="confirm_password" placeholder="Ulangi password" required>

        <button type="submit">Simpan Password</button>
    </form>
</div>
</body>
</html>