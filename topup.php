<?php
require 'koneksi.php';

$gameCode = $_GET['game'] ?? 'ml';
$stmt = $pdo->prepare("SELECT * FROM games WHERE code = ? AND is_active = 1");
$stmt->execute([$gameCode]);
$game = $stmt->fetch();

if (!$game) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM products WHERE game_id = ? AND status = 'available' ORDER BY price_customer ASC");
$stmt->execute([$game['id']]);
$products = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order ASC");
$paymentMethods = $stmt->fetchAll();

$idFormat = json_decode($game['id_format'], true);
$popularIndex = floor(count($products) / 2); // Tengah dianggap popular
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Top Up <?= htmlspecialchars($game['name']) ?> - TopUpKu</title>
  <link rel="stylesheet" href="web.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ====== BACKGROUND ANIMASI (khusus halaman ini, terpisah dari web.css) ====== */
    #bg-canvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        pointer-events: none;
    }

    body {
        position: relative;
        background: #0f0c29 !important;
        overflow-x: hidden;
    }

    .navbar,
    .container {
        position: relative;
        z-index: 2;
    }

    /* Hapus background navbar sepenuhnya */
    .navbar {
        background: transparent !important;
    }

    /* Buat kotak-kotak jadi semi-transparan agar partikel terlihat menembus */
    .form-section {
        background: rgba(15, 17, 32, 0.45) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .nominal-item {
        background: rgba(0, 0, 0, 0.25) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .nominal-item.active {
        background: rgba(0, 245, 255, 0.10) !important;
    }
    .payment-item {
        background: rgba(0, 0, 0, 0.25) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .payment-item.active {
        background: rgba(0, 245, 255, 0.10) !important;
    }
    .summary-box {
        background: linear-gradient(145deg, rgba(0,245,255,0.06), rgba(10,11,16,0.45)) !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .game-logo-fallback {
        background: rgba(26, 29, 38, 0.5) !important;
    }

    /* Perbaikan ukuran logo game & fallback text (class belum ada di web.css) */
    .game-logo-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        background: linear-gradient(135deg, #1a1d26, #0f1118);
    }
    .game-logo-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .logo-fallback {
        display: none;
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--neon-cyan);
        background: rgba(26, 29, 38, 0.7);
    }
    /* ====== END BACKGROUND ANIMASI ====== */
  </style>
</head>
<body>

  <nav class="navbar container">
    <a href="index.php" class="brand">
      <span class="brand-icon">⚡</span>
      <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
    </a>
  </nav>

  <main class="container">
    <div class="topup-header">
      <div class="game-logo-wrapper">
        <img src="asset/<?= htmlspecialchars($game['code']) ?>.jpg.webp" 
             alt="<?= htmlspecialchars($game['name']) ?>"
             onerror="this.src='asset/<?= htmlspecialchars($game['code']) ?>.jpg'; this.onerror=function(){ this.style.display='none'; this.nextElementSibling.style.display='flex'; };">
        <div class="logo-fallback"><?= strtoupper(substr($game['name'], 0, 3)) ?></div>
      </div>
      <div>
        <h2><?= htmlspecialchars($game['name']) ?></h2>
        <p>Isi data akun & pilih nominal top up</p>
      </div>
    </div>

    <section class="form-section">
      <label class="form-label">1. Masukkan Data Akun</label>
      <div class="form-group">
        <?php foreach ($idFormat['fields'] as $field): ?>
          <input type="text" id="<?= htmlspecialchars($field) ?>" class="input-field" 
                 placeholder="<?= htmlspecialchars($idFormat['placeholder'][$field]) ?>" autocomplete="off">
        <?php endforeach; ?>
      </div>
      <button id="check-id-btn" class="btn btn-primary" style="margin-top:16px;">Cek ID</button>
      <div id="validation-result" class="validation-result"></div>
    </section>

    <section class="form-section">
      <label class="form-label">2. Pilih Nominal Top Up</label>
      <div class="nominal-grid">
        <?php if (empty($products)): ?>
          <p class="text-muted">Belum ada nominal tersedia.</p>
        <?php else: ?>
          <?php foreach ($products as $i => $product): ?>
            <div class="nominal-item" data-id="<?= $product['id'] ?>" data-price="<?= $product['price_customer'] ?>">
              <?php if ($i === $popularIndex): ?>
                <span class="badge-popular">POPULAR</span>
              <?php endif; ?>
              <div class="name"><?= htmlspecialchars($product['name']) ?></div>
              <div class="price">Rp <?= number_format($product['price_customer'], 0, ',', '.') ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <section class="form-section">
      <label class="form-label">3. Pilih Metode Pembayaran</label>
      <div class="payment-grid">
        <?php foreach ($paymentMethods as $method): ?>
          <div class="payment-item" data-id="<?= $method['id'] ?>" data-fee="<?= $method['fee_fixed'] ?>">
            <div class="payment-icon"><?= strtoupper(substr($method['name'], 0, 2)) ?></div>
            <div class="payment-info">
              <div class="payment-name"><?= htmlspecialchars($method['name']) ?></div>
              <div class="payment-fee"><?= $method['fee_fixed'] > 0 ? 'Fee: Rp '.number_format($method['fee_fixed'],0,',','.') : 'Fee: Rp 0' ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <div class="summary-box">
      <div class="summary-row"><span>Harga Produk</span><span id="summary-product">-</span></div>
      <div class="summary-row"><span>Fee Pembayaran</span><span id="summary-fee">-</span></div>
      <div class="summary-row total"><span>Total Pembayaran</span><span id="total-price">Rp 0</span></div>
      <button id="buy-btn" class="btn btn-primary" style="width:100%; margin-top:20px; padding:16px; font-size:1.1rem;">
        Beli Sekarang
      </button>
    </div>
  </main>

  <script src="web.js"></script>

  <!-- ====== BACKGROUND ANIMASI (khusus halaman ini) ====== -->
  <canvas id="bg-canvas"></canvas>
  <script>
    const canvas = document.getElementById('bg-canvas');

    if (canvas) {
      const ctx = canvas.getContext('2d');
      let w, h;
      let particles = [];

      function resize() {
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
      }
      resize();
      window.addEventListener('resize', resize);

      class Particle {
        constructor() {
          this.x = Math.random() * w;
          this.y = Math.random() * h;
          this.vx = (Math.random() - 0.5) * 0.6;
          this.vy = (Math.random() - 0.5) * 0.6;
          this.size = Math.random() * 2 + 1;
          const colors = ['rgba(0,245,255,', 'rgba(191,0,255,', 'rgba(0,255,136,'];
          this.color = colors[Math.floor(Math.random() * colors.length)];
        }
        update() {
          this.x += this.vx;
          this.y += this.vy;
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

      for (let i = 0; i < 70; i++) {
        particles.push(new Particle());
      }

      function animate() {
        ctx.clearRect(0, 0, w, h);

        particles.forEach((p, i) => {
          p.update();
          p.draw();

          for (let j = i + 1; j < particles.length; j++) {
            const dx = p.x - particles[j].x;
            const dy = p.y - particles[j].y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < 130) {
              ctx.beginPath();
              ctx.moveTo(p.x, p.y);
              ctx.lineTo(particles[j].x, particles[j].y);
              ctx.strokeStyle = 'rgba(0,245,255,' + (0.12 * (1 - dist / 130)) + ')';
              ctx.lineWidth = 1;
              ctx.stroke();
            }
          }
        });

        requestAnimationFrame(animate);
      }

      animate();
    }
  </script>
  <!-- ====== END BACKGROUND ANIMASI ====== -->

</body>
</html>