<?php
session_start();
// 1. Load koneksi
require 'koneksi.php';

// 2. Ambil data game
$games = [];
try {
    $stmt = $pdo->query("SELECT id, code, name, sort_order FROM games WHERE is_active = 1 ORDER BY sort_order ASC");
    $games = $stmt->fetchAll();
} catch (PDOException $e) {
    // Kalau gagal, biarkan $games kosong — badge status DB sudah ditangani di header.php
}

$pageTitle = 'TopUpKu - Top Up Game Cepat & Aman';
include 'header.php';
?>

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

<?php include 'footer.php'; ?>