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
</head>
<body>

  <nav class="navbar container">
    <a href="index.php" class="brand">⚡ TopUpKu</a>
  </nav>

  <main class="container">
    <div class="topup-header">
      <div class="game-logo-wrapper">
        <img src="assets/images/<?= htmlspecialchars($game['code']) ?>.png" 
             alt="<?= htmlspecialchars($game['name']) ?>"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
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
</body>
</html>