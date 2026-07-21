<?php
// =========================================================
// HEADER.PHP — bagian umum (doctype, head, topbar, navbar)
// Dipakai bersama oleh index.php, bantuan.php, dan halaman lain.
// =========================================================

// Session: hanya start kalau belum ada, supaya aman dipanggil
// dari halaman yang sudah session_start() duluan.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi DB tersedia (aman dipanggil berkali-kali).
require_once __DIR__ . '/koneksi.php';

// Cek status koneksi untuk badge DB di pojok.
$db_status = '✅ Terhubung';
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    $db_status = '❌ Gagal: ' . htmlspecialchars($e->getMessage());
}

// Deteksi halaman aktif untuk highlight menu.
$current_page = basename($_SERVER['PHP_SELF']);

// Judul halaman default, bisa dioverride dengan set $pageTitle
// SEBELUM include header.php.
if (!isset($pageTitle)) {
    $pageTitle = 'TopUpKu - Top Up Game Cepat & Aman';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="stylesheet" href="web.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        <li><a href="index.php#games" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">Game</a></li>
        <li><a href="promo.php" class="nav-link <?= $current_page === 'promo.php' ? 'active' : '' ?>">Promo</a></li>
        <li><a href="pesanan.php" class="nav-link <?= $current_page === 'pesanan.php' ? 'active' : '' ?>">Pesanan</a></li>
        <li><a href="bantuan.php" class="nav-link <?= $current_page === 'bantuan.php' ? 'active' : '' ?>">Bantuan</a></li>
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