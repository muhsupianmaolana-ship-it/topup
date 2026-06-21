<?php
session_start();
// 1. Load koneksi
require 'koneksi.php';

// 2. Cek koneksi & ambil data game
$db_status = '✅ Terhubung';
$games = [];
try {
    $pdo->query("SELECT 1");
    $stmt = $pdo->query("SELECT id, code, name, sort_order FROM games WHERE is_active = 1 ORDER BY sort_order ASC");
    $games = $stmt->fetchAll();
} catch (PDOException $e) {
    $db_status = '❌ Gagal: ' . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TopUpKu - Top Up Game Cepat & Aman</title>
  <link rel="stylesheet" href="web.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>

  <div class="db-badge <?= strpos($db_status, '✅') !== false ? 'success' : 'error' ?>">
    DB: <?= $db_status ?>
  </div>

  <!-- TOP BAR -->
  <div class="topbar">
    <div class="container topbar-inner">
      <span class="topbar-slogan">⚡ INSTANT TOP UP! INSTANT PLAY!</span>
      <a href="pesanan.php" class="btn btn-outline-sm">Cek Pesanan</a>
    </div>
  </div>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="container navbar-inner">
      <a href="index.php" class="brand">
        <span class="brand-icon">⚡</span>
        <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
      </a>

      <ul class="nav-menu">
  <li><a href="index.php#games" class="nav-link active">Game</a></li>
  <li><a href="promo.php" class="nav-link">Promo</a></li>
  <li><a href="pesanan.php" class="nav-link">Pesanan</a></li>
  <li><a href="bantuan.php" class="nav-link">Bantuan</a></li>
</ul>

      <div class="nav-right">
        <div class="search-wrapper">
          <span class="search-icon">🔍</span>
          <input type="text" class="search-input" placeholder="Cari game...">
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="nav-user">
            <span class="nav-username">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="btn-logout">Keluar</a>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-neon">Masuk</a>
        <?php endif; ?>
      </div>

      <button class="hamburger" onclick="toggleMenu()">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <!-- MOBILE MENU -->
  <div class="mobile-menu" id="mobileMenu">
  <a href="index.php#games" class="mobile-link" onclick="toggleMenu()">Game</a>
  <a href="promo.php" class="mobile-link" onclick="toggleMenu()">Promo</a>
  <a href="pesanan.php" class="mobile-link" onclick="toggleMenu()">Pesanan</a>
  <a href="bantuan.php" class="mobile-link" onclick="toggleMenu()">Bantuan</a>
</div>

  <!-- HERO BANNER / SLIDER -->
  <section class="hero-slider">
    <div class="slider-track" id="sliderTrack">

      <div class="slide slide-1">
        <div class="shooting-star s1"></div>
        <div class="shooting-star s2"></div>
        <div class="shooting-star s3"></div>
        <div class="shooting-star s4"></div>
        <div class="slide-content container">
          <div class="slide-badge">🔥 LIMITED TIME</div>
          <h1 class="slide-title">Top Up <span class="neon-text">Mobile Legends</span><br>Bonus Diamond Extra!</h1>
          <p class="slide-desc">Dapatkan bonus 20% diamond setiap top up di atas Rp 50.000</p>
          <div class="slide-actions">
            <a href="#games" class="btn btn-neon btn-lg">Top Up Sekarang</a>
            <a href="#" class="btn btn-ghost btn-lg">Lihat Promo</a>
          </div>
        </div>
        <div class="slide-overlay"></div>
      </div>

      <div class="slide slide-2">
        <div class="slide-content container">
          <div class="slide-badge">⚡ NEW</div>
          <h1 class="slide-title">Free Fire <span class="neon-text-green">Diamond</span><br>Harga Terbaik!</h1>
          <p class="slide-desc">Top up Free Fire mulai dari Rp 1.500. Proses instan 24/7</p>
          <div class="slide-actions">
            <a href="#games" class="btn btn-neon-green btn-lg">Top Up FF</a>
            <a href="#" class="btn btn-ghost btn-lg">Info Lebih</a>
          </div>
        </div>
        <div class="slide-overlay"></div>
      </div>

      <div class="slide slide-3">
        <div class="slide-content container">
          <div class="slide-badge">🎮 PROMO</div>
          <h1 class="slide-title">PUBG Mobile <span class="neon-text-orange">UC</span><br>Flash Sale!</h1>
          <p class="slide-desc">Beli UC PUBG sekarang dan dapatkan cashback hingga Rp 25.000</p>
          <div class="slide-actions">
            <a href="#games" class="btn btn-neon-orange btn-lg">Beli Sekarang</a>
            <a href="#" class="btn btn-ghost btn-lg">Syarat & Ketentuan</a>
          </div>
        </div>
        <div class="slide-overlay"></div>
      </div>

    </div>

    <!-- Slider Controls -->
    <button class="slider-btn slider-prev" onclick="changeSlide(-1)">&#8249;</button>
    <button class="slider-btn slider-next" onclick="changeSlide(1)">&#8250;</button>

    <!-- Dots -->
    <div class="slider-dots">
      <span class="dot active" onclick="goSlide(0)"></span>
      <span class="dot" onclick="goSlide(1)"></span>
      <span class="dot" onclick="goSlide(2)"></span>
    </div>
  </section>

  <!-- STATS BAR -->
  <div class="stats-bar">
    <div class="container stats-inner">
      <div class="stat-item">
        <span class="stat-num neon-text">Sat Set</span>
        <span class="stat-label">Tanpa Ribet</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-num neon-text-green">24/7</span>
        <span class="stat-label">Layanan Aktif</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-num neon-text-orange">< 1 Menit</span>
        <span class="stat-label">Proses Instan</span>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <span class="stat-num neon-text">100%</span>
        <span class="stat-label">Aman & Terpercaya</span>
      </div>
    </div>
  </div>

  <!-- GAME SECTION -->
  <main class="container" id="games">
    <div class="section-header">
      <h2 class="section-title"><span class="neon-bar"></span> Game Tersedia</h2>
      <div class="section-filter">
        <button class="filter-btn active" data-filter="all">Semua</button>
        <button class="filter-btn" data-filter="mobile">Mobile</button>
        <button class="filter-btn" data-filter="pc">PC</button>
      </div>
    </div>

    <div class="game-grid">
      <?php if (empty($games)): ?>
        <p class="empty-state">Belum ada game tersedia. Hubungi admin untuk aktivasi.</p>
      <?php else: ?>
        <?php foreach ($games as $i => $game): ?>
          <a href="topup.php?game=<?= htmlspecialchars($game['code']) ?>"
             class="game-card"
             data-aos="fade-up"
             data-aos-delay="<?= min($i * 60, 400) ?>">

            <div class="card-img-wrapper">
  <img src="asset/<?= htmlspecialchars($game['code']) ?>.jpg.webp"
       alt="<?= htmlspecialchars($game['name']) ?>"
       onerror="this.src='asset/<?= htmlspecialchars($game['code']) ?>.jpg'; this.onerror=function(){ this.style.display='none'; this.nextElementSibling.style.display='flex'; };">
  
  <div class="img-fallback"><?= strtoupper(substr($game['name'], 0, 3)) ?></div>
  
  <div class="card-hover-overlay">
    <span class="hover-btn">⚡ Top Up</span>
  </div>
</div>

            <div class="card-body">
              <h3><?= htmlspecialchars($game['name']) ?></h3>
              <p class="card-sub">Top up instan</p>
              <div class="card-footer-row">
                <span class="card-badge">24/7</span>
                <span class="topup-btn-small">Top Up →</span>
              </div>
            </div>

          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <!-- WHY TOPUPKU SECTION -->
  <section class="why-section">
    <div class="container">
      <h2 class="section-title"><span class="neon-bar"></span> Kenapa TopUpKu?</h2>
      <div class="why-grid">
        <div class="why-card" data-aos="fade-up" data-aos-delay="0">
          <div class="why-icon neon-icon">⚡</div>
          <h3>Proses Instan</h3>
          <p>Top up langsung masuk dalam hitungan detik, 24 jam sehari</p>
        </div>
        <div class="why-card" data-aos="fade-up" data-aos-delay="100">
          <div class="why-icon green-icon">🔒</div>
          <h3>100% Aman</h3>
          <p>Transaksi terenkripsi dan dijamin aman dari segala ancaman</p>
        </div>
        <div class="why-card" data-aos="fade-up" data-aos-delay="200">
          <div class="why-icon orange-icon">💸</div>
          <h3>Harga Terbaik</h3>
          <p>Harga bersaing dengan banyak pilihan metode pembayaran</p>
        </div>
        <div class="why-card" data-aos="fade-up" data-aos-delay="300">
          <div class="why-icon purple-icon">🎮</div>
          <h3>Banyak Game</h3>
          <p>Tersedia ratusan game populer mobile dan PC tersedia</p>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container footer-inner">
      <div class="footer-brand">
        <a href="index.php" class="brand">
          <span class="brand-icon">⚡</span>
          <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
        </a>
        <p>Platform top up game terpercaya,<br>cepat, dan aman 24/7.</p>
      </div>
      <div class="footer-links">
        <h4>Menu</h4>
        <a href="#games">Game</a>
        <a href="#">Promo</a>
        <a href="#">Pesanan</a>
        <a href="#">Bantuan</a>
      </div>
      <div class="footer-links">
        <h4>Ikuti Kami</h4>
        <a href="https://www.instagram.com/lana_pleaseimprove" target="_blank">Instagram</a>
        <a href="https://wa.me/6281998861649" target="_blank">WhatsApp</a>
        <a href="mailto:emailbisnismu@gmail.com?subject=Tanya%20Seputar%20TopUpKu" target="_blank">Email</a>

      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> TopUpKu. All rights reserved.</p>
    </div>
  </footer>

  <canvas id="bg-canvas"></canvas>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="web.js"></script>
</body>
</html>