<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sudah login? langsung ke admin_transaksi
if (isset($_SESSION['transaksi_logged_in']) && $_SESSION['transaksi_logged_in'] === true) {
    header("Location: admin_transaksi.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'tugas_web';
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        $error = "Koneksi database gagal.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT * FROM admins_transaksi WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin  = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['transaksi_logged_in'] = true;
            $_SESSION['transaksi_id']        = $admin['id'];
            $_SESSION['transaksi_username']  = $admin['username'];
            header("Location: admin_transaksi.php");
            exit();
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Transaksi — TopUpKu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cyan:   #00f5ff;
            --purple: #bf00ff;
            --green:  #00ff88;
            --bg:     #0f0c29;
            --card:   #1b1b3a;
            --card2:  #13112a;
            --border: rgba(0, 245, 255, 0.18);
            --text:   #e0e0ff;
            --muted:  #8888aa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        #bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .login-wrap {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
        }

        .login-wrap::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--cyan) 0%, var(--purple) 100%);
            z-index: -1;
            opacity: 0.35;
            filter: blur(8px);
        }

        .login-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px 40px 40px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.55);
        }

        /* Badge role */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 245, 255, 0.08);
            border: 1px solid rgba(0, 245, 255, 0.25);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--cyan);
            margin-bottom: 16px;
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--cyan), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(0,245,255,0.3);
        }

        .brand h1 {
            font-family: 'Exo 2', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, var(--cyan), var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand p {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 6px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            color: var(--muted);
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--cyan);
            margin-bottom: 8px;
        }

        .input-wrapper { position: relative; }

        .input-wrapper .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.5;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            background: var(--card2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 14px 13px 42px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s;
        }

        .form-group input::placeholder { color: var(--muted); }

        .form-group input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 0 3px rgba(0,245,255,0.1);
        }

        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
        }
        .toggle-pass:hover { color: var(--cyan); }

        .alert-error {
            background: rgba(255,68,68,0.1);
            border: 1px solid rgba(255,68,68,0.4);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.84rem;
            color: #ff7070;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, var(--cyan), #0099ff);
            color: #000;
            font-family: 'Exo 2', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 6px 20px rgba(0,245,255,0.25);
            margin-top: 4px;
        }

        .btn-login:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(0,245,255,0.35);
        }

        .btn-login:active { transform: translateY(0); }
        .btn-login.loading { opacity: 0.7; pointer-events: none; }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .login-footer span { color: var(--cyan); font-weight: 600; }
    </style>
</head>
<body>

<canvas id="bg-canvas"></canvas>

<div class="login-wrap">
    <div class="login-card">

        <div class="brand">
            <div class="brand-icon">💳</div>
            <h1>TopUpKu</h1>
            <div style="display:flex;justify-content:center;margin-top:8px;">
                <span class="role-badge">🔐 Admin Transaksi</span>
            </div>
            <p>Manajemen Pembayaran & Verifikasi</p>
        </div>

        <div class="divider">Masuk ke akun Anda</div>

        <?php if ($error): ?>
        <div class="alert-error">
            <span>⚠️</span> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <span class="icon">👤</span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Username admin transaksi"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <span class="icon">🔒</span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="toggle-pass" id="togglePass">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                Masuk ke Panel Transaksi
            </button>
        </form>

        <div class="login-footer">
            Akses khusus <span>Admin Transaksi</span>. Bukan admin master.
        </div>

    </div>
</div>

<script>
document.getElementById('togglePass').addEventListener('click', function () {
    const input = document.getElementById('password');
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    this.textContent = isText ? '👁️' : '🙈';
});

document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.classList.add('loading');
    btn.textContent = 'Memverifikasi…';
});
</script>

<script>
(function () {
    const canvas = document.getElementById('bg-canvas');
    const ctx    = canvas.getContext('2d');
    let w, h, particles = [];

    function resize() {
        w = canvas.width  = window.innerWidth;
        h = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    const COLORS = ['rgba(0,245,255,', 'rgba(191,0,255,', 'rgba(0,255,136,'];

    class Particle {
        constructor() {
            this.x     = Math.random() * w;
            this.y     = Math.random() * h;
            this.vx    = (Math.random() - 0.5) * 0.6;
            this.vy    = (Math.random() - 0.5) * 0.6;
            this.size  = Math.random() * 2 + 1;
            this.color = COLORS[Math.floor(Math.random() * COLORS.length)];
        }
        update() {
            this.x += this.vx; this.y += this.vy;
            if (this.x < 0 || this.x > w) this.vx *= -1;
            if (this.y < 0 || this.y > h) this.vy *= -1;
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fillStyle = this.color + '0.8)';
            ctx.fill();
        }
    }

    for (let i = 0; i < 70; i++) particles.push(new Particle());

    function animate() {
        ctx.clearRect(0, 0, w, h);
        particles.forEach((p, i) => {
            p.update(); p.draw();
            for (let j = i + 1; j < particles.length; j++) {
                const dx = p.x - particles[j].x, dy = p.y - particles[j].y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 130) {
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = 'rgba(0,245,255,' + (0.12*(1-dist/130)) + ')';
                    ctx.lineWidth = 1;
                    ctx.stroke();
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