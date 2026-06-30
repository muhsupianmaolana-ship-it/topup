<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['transaksi_logged_in']) || $_SESSION['transaksi_logged_in'] !== true) {
    header("Location: login_transaksi.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'tugas_web';
$conn = new mysqli($host, $user, $pass, $db);

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password  = $_POST['current_password'] ?? '';
    $new_username      = trim($_POST['new_username'] ?? '');
    $new_password      = $_POST['new_password'] ?? '';
    $confirm_password  = $_POST['confirm_password'] ?? '';

    // Ambil data admin saat ini
    $id   = $_SESSION['transaksi_id'];
    $stmt = $conn->prepare("SELECT * FROM admins_transaksi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!password_verify($current_password, $admin['password'])) {
        $error = "Password saat ini salah.";
    } elseif (empty($new_username)) {
        $error = "Username baru tidak boleh kosong.";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = "Konfirmasi password baru tidak cocok.";
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } else {
        // Cek username sudah dipakai orang lain
        $cek = $conn->prepare("SELECT id FROM admins_transaksi WHERE username = ? AND id != ?");
        $cek->bind_param("si", $new_username, $id);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $error = "Username '$new_username' sudah digunakan.";
        } else {
            if (!empty($new_password)) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE admins_transaksi SET username = ?, password = ? WHERE id = ?");
                $upd->bind_param("ssi", $new_username, $hashed, $id);
            } else {
                $upd = $conn->prepare("UPDATE admins_transaksi SET username = ? WHERE id = ?");
                $upd->bind_param("si", $new_username, $id);
            }
            $upd->execute();
            $upd->close();

            $_SESSION['transaksi_username'] = $new_username;
            $success = "Akun berhasil diperbarui!";
        }
        $cek->close();
    }
}

// Ambil data terbaru
$stmt  = $conn->prepare("SELECT * FROM admins_transaksi WHERE id = ?");
$stmt->bind_param("i", $_SESSION['transaksi_id']);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Akun — Admin Transaksi</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cyan:   #00f5ff;
            --purple: #bf00ff;
            --bg:     #0f0c29;
            --card:   #1b1b3a;
            --card2:  #13112a;
            --border: rgba(0,245,255,0.18);
            --text:   #e0e0ff;
            --muted:  #8888aa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 30px 20px;
        }

        #bg-canvas { position: fixed; inset: 0; z-index: 0; pointer-events: none; }

        .container {
            position: relative;
            z-index: 2;
            max-width: 520px;
            margin: 0 auto;
        }

        /* Topbar */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--cyan);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid var(--border);
            padding: 8px 14px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .back-btn:hover { background: rgba(0,245,255,0.08); }

        .page-title {
            font-family: 'Exo 2', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--cyan);
        }

        /* Card */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .card-header h2 {
            font-family: 'Exo 2', sans-serif;
            font-size: 1rem;
            font-weight: 700;
        }

        .card-header p {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 3px;
        }

        /* Section label */
        .section-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 14px;
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* Form */
        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 0.73rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--cyan);
            margin-bottom: 7px;
        }

        .input-wrap { position: relative; }

        .input-wrap .ico {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            opacity: 0.45;
            pointer-events: none;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            background: var(--card2);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 12px 12px 12px 40px;
            color: var(--text);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(0,245,255,0.1);
        }

        input::placeholder { color: var(--muted); }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 15px;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: var(--cyan); }

        .hint {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 5px;
        }

        /* Alerts */
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.84rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: rgba(0,255,136,0.08); border: 1px solid rgba(0,255,136,0.3); color: #00ff88; }
        .alert-error   { background: rgba(255,68,68,0.08);  border: 1px solid rgba(255,68,68,0.3);  color: #ff7070; }

        /* Button */
        .btn-save {
            width: 100%;
            padding: 13px;
            margin-top: 24px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(90deg, var(--cyan), #0099ff);
            color: #000;
            font-family: 'Exo 2', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 6px 20px rgba(0,245,255,0.2);
        }
        .btn-save:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 10px 26px rgba(0,245,255,0.3);
        }
        .btn-save:active { transform: translateY(0); }
    </style>
</head>
<body>
<canvas id="bg-canvas"></canvas>

<div class="container">
    <div class="topbar">
        <a href="admin_transaksi.php" class="back-btn">← Kembali</a>
        <span class="page-title">⚙️ Pengaturan Akun</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="avatar">👤</div>
            <div>
                <h2><?php echo htmlspecialchars($admin['nama'] ?? 'Admin Transaksi'); ?></h2>
                <p>@<?php echo htmlspecialchars($admin['username']); ?> · Admin Transaksi</p>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="section-label">Verifikasi</div>

            <div class="form-group">
                <label>Password Saat Ini</label>
                <div class="input-wrap">
                    <span class="ico">🔑</span>
                    <input type="password" name="current_password" placeholder="Wajib diisi untuk menyimpan" required>
                    <button type="button" class="toggle-pass" onclick="toggleVis(this)">👁️</button>
                </div>
            </div>

            <div class="section-label">Ganti Username</div>

            <div class="form-group">
                <label>Username Baru</label>
                <div class="input-wrap">
                    <span class="ico">👤</span>
                    <input type="text" name="new_username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                </div>
            </div>

            <div class="section-label">Ganti Password</div>

            <div class="form-group">
                <label>Password Baru</label>
                <div class="input-wrap">
                    <span class="ico">🔒</span>
                    <input type="password" name="new_password" placeholder="Kosongkan jika tidak ingin ganti">
                    <button type="button" class="toggle-pass" onclick="toggleVis(this)">👁️</button>
                </div>
                <p class="hint">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.</p>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <div class="input-wrap">
                    <span class="ico">🔒</span>
                    <input type="password" name="confirm_password" placeholder="Ulangi password baru">
                    <button type="button" class="toggle-pass" onclick="toggleVis(this)">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn-save">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
function toggleVis(btn) {
    const input = btn.previousElementSibling;
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.textContent = isPass ? '🙈' : '👁️';
}
</script>

<script>
(function () {
    const canvas = document.getElementById('bg-canvas');
    const ctx = canvas.getContext('2d');
    let w, h, particles = [];
    function resize() { w = canvas.width = window.innerWidth; h = canvas.height = window.innerHeight; }
    resize();
    window.addEventListener('resize', resize);
    const COLORS = ['rgba(0,245,255,', 'rgba(191,0,255,', 'rgba(0,255,136,'];
    class Particle {
        constructor() {
            this.x = Math.random()*w; this.y = Math.random()*h;
            this.vx = (Math.random()-0.5)*0.6; this.vy = (Math.random()-0.5)*0.6;
            this.size = Math.random()*2+1;
            this.color = COLORS[Math.floor(Math.random()*COLORS.length)];
        }
        update() {
            this.x += this.vx; this.y += this.vy;
            if (this.x < 0 || this.x > w) this.vx *= -1;
            if (this.y < 0 || this.y > h) this.vy *= -1;
        }
        draw() {
            ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI*2);
            ctx.fillStyle = this.color+'0.8)'; ctx.fill();
        }
    }
    for (let i = 0; i < 70; i++) particles.push(new Particle());
    function animate() {
        ctx.clearRect(0, 0, w, h);
        particles.forEach((p, i) => {
            p.update(); p.draw();
            for (let j = i+1; j < particles.length; j++) {
                const dx = p.x-particles[j].x, dy = p.y-particles[j].y;
                const dist = Math.sqrt(dx*dx+dy*dy);
                if (dist < 130) {
                    ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = 'rgba(0,245,255,'+(0.12*(1-dist/130))+')';
                    ctx.lineWidth = 1; ctx.stroke();
                }
            }
        });
        requestAnimationFrame(animate);
    }
    animate();
})();
</script>
</body>
</html>