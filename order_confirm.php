<?php
session_start();
require 'koneksi.php';

$order_code = $_GET['order'] ?? '';

// Ambil data order dari database
$order = null;

if ($order_code) {
    $stmt = $pdo->prepare("
        SELECT o.*, p.name as product_name, p.price_customer,
               pm.name as payment_name, pm.type as payment_type, pm.code as payment_code
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN payment_methods pm ON o.payment_method_id = pm.id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$order_code]);
    $order = $stmt->fetch();
}

// Kalau order tidak ditemukan, redirect
if (!$order) {
    header("Location: index.php");
    exit;
}

// Ambil buyer_data
$buyer = json_decode($order['buyer_data'], true);

// Info rekening per metode pembayaran
$rekening = [
    'qris'   => ['label' => 'QRIS', 'nomor' => 'Scan QR di bawah', 'atas_nama' => 'MUH. SUPIAN MAOLANA'],
    'dana'   => ['label' => 'DANA', 'nomor' => '081998861649', 'atas_nama' => 'MUH. SUPIAN MAOLANA'],
    'gopay'  => ['label' => 'GoPay', 'nomor' => '081998861649', 'atas_nama' => 'TopUpKu Store'],
    'va_bca' => ['label' => 'Virtual Account BCA', 'nomor' => '1234567890123', 'atas_nama' => 'TopUpKu'],
];

$info_bayar = $rekening[$order['payment_code']] ?? ['label' => $order['payment_name'], 'nomor' => '-', 'atas_nama' => 'TopUpKu'];

// Handle upload bukti transfer
$upload_success = false;
$upload_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti'])) {
    $file = $_FILES['bukti'];
    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    if ($file['error'] !== 0) {
        $upload_error = 'Gagal upload file.';
    } elseif (!in_array($file['type'], $allowed)) {
        $upload_error = 'Format file harus JPG, PNG, atau WEBP.';
    } elseif ($file['size'] > 5 * 1024 * 1024) {
        $upload_error = 'Ukuran file maksimal 5MB.';
    } else {
        $upload_dir = 'uploads/bukti/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename  = 'bukti_' . $order['order_id'] . '_' . time() . '.' . $ext;
        $filepath  = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $stmt = $pdo->prepare("UPDATE orders SET payment_proof = ?, status = 'waiting_confirmation', updated_at = NOW() WHERE order_id = ?");
            $stmt->execute([$filename, $order['order_id']]);
            $upload_success = true;

            $stmt2 = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt2->execute([$order['order_id']]);
            $order = array_merge($order, $stmt2->fetch());
        } else {
            $upload_error = 'Gagal menyimpan file. Cek folder uploads/bukti/.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Pembayaran - TopUpKu</title>
  <link rel="stylesheet" href="web.css">
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ====== BACKGROUND ANIMASI (baru) ====== */
    #bg-canvas{
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:-1;
        pointer-events:none;
    }

    body{
        position:relative;
        background:#0f0c29 !important;
        overflow-x:hidden;
    }

    .navbar,
    .confirm-wrapper{
        position:relative;
        z-index:2;
    }
    /* ====== END BACKGROUND ANIMASI ====== */

    .confirm-wrapper {
      max-width: 560px;
      margin: 40px auto;
      padding: 0 20px 60px;
    }

    .confirm-card {
      background: var(--bg-card);
      border: 1px solid rgba(0,245,255,0.12);
      border-radius: 18px;
      padding: 28px;
      margin-bottom: 20px;
    }

    .confirm-card h3 {
      font-family: var(--font-display);
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--neon-cyan);
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .notif-banner {
      border-radius: 18px;
      padding: 32px 28px;
      margin-bottom: 24px;
      text-align: center;
      position: relative;
      overflow: hidden;
      animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .notif-banner.notif-paid {
      background: linear-gradient(135deg, rgba(0,255,136,0.12), rgba(0,200,100,0.06));
      border: 2px solid rgba(0,255,136,0.5);
      box-shadow: 0 0 40px rgba(0,255,136,0.15), inset 0 0 60px rgba(0,255,136,0.04);
    }

    .notif-banner.notif-paid::before {
      content: '';
      position: absolute;
      top: -50%; left: -50%;
      width: 200%; height: 200%;
      background: radial-gradient(circle, rgba(0,255,136,0.06) 0%, transparent 60%);
      animation: pulse-green 2s ease-in-out infinite;
    }

    @keyframes pulse-green {
      0%, 100% { transform: scale(1); opacity: 0.5; }
      50%       { transform: scale(1.1); opacity: 1; }
    }

    .notif-banner.notif-rejected {
      background: linear-gradient(135deg, rgba(255,0,85,0.12), rgba(200,0,60,0.06));
      border: 2px solid rgba(255,0,85,0.5);
      box-shadow: 0 0 40px rgba(255,0,85,0.15), inset 0 0 60px rgba(255,0,85,0.04);
    }

    .notif-banner.notif-rejected::before {
      content: '';
      position: absolute;
      top: -50%; left: -50%;
      width: 200%; height: 200%;
      background: radial-gradient(circle, rgba(255,0,85,0.06) 0%, transparent 60%);
      animation: pulse-red 2s ease-in-out infinite;
    }

    @keyframes pulse-red {
      0%, 100% { transform: scale(1); opacity: 0.5; }
      50%       { transform: scale(1.1); opacity: 1; }
    }

    .notif-banner .notif-icon {
      font-size: 3.5rem;
      margin-bottom: 12px;
      display: block;
      position: relative;
      z-index: 1;
      animation: popIn 0.5s 0.2s ease both;
    }

    @keyframes popIn {
      0%   { transform: scale(0.4); opacity: 0; }
      70%  { transform: scale(1.15); }
      100% { transform: scale(1); opacity: 1; }
    }

    .notif-banner .notif-title {
      font-family: 'Exo 2', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      letter-spacing: 0.04em;
      margin-bottom: 8px;
      position: relative;
      z-index: 1;
    }

    .notif-paid .notif-title     { color: #00ff88; text-shadow: 0 0 20px rgba(0,255,136,0.6); }
    .notif-rejected .notif-title { color: #ff0055; text-shadow: 0 0 20px rgba(255,0,85,0.6); }

    .notif-banner .notif-desc {
      font-size: 0.9rem;
      line-height: 1.6;
      position: relative;
      z-index: 1;
    }

    .notif-paid .notif-desc     { color: rgba(0,255,136,0.8); }
    .notif-rejected .notif-desc { color: rgba(255,100,130,0.9); }

    .notif-banner .notif-order {
      display: inline-block;
      margin-top: 14px;
      padding: 6px 16px;
      border-radius: 20px;
      font-family: 'Exo 2', monospace;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 1px;
      position: relative;
      z-index: 1;
    }

    .notif-paid .notif-order {
      background: rgba(0,255,136,0.1);
      border: 1px solid rgba(0,255,136,0.3);
      color: #00ff88;
    }

    .notif-rejected .notif-order {
      background: rgba(255,0,85,0.1);
      border: 1px solid rgba(255,0,85,0.3);
      color: #ff0055;
    }

    .notif-banner.notif-waiting {
      background: linear-gradient(135deg, rgba(0,245,255,0.10), rgba(0,120,200,0.06));
      border: 2px solid rgba(0,245,255,0.4);
      box-shadow: 0 0 30px rgba(0,245,255,0.10);
    }
    .notif-waiting .notif-title { color: #00f5ff; text-shadow: 0 0 15px rgba(0,245,255,0.5); }
    .notif-waiting .notif-desc  { color: rgba(0,245,255,0.75); }
    .notif-waiting .notif-order {
      background: rgba(0,245,255,0.08);
      border: 1px solid rgba(0,245,255,0.25);
      color: #00f5ff;
    }

    .order-id-box {
      background: rgba(0,245,255,0.05);
      border: 1px dashed rgba(0,245,255,0.25);
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .order-id-box span:first-child { font-size: 0.8rem; color: var(--text-muted); }
    .order-id-box span:last-child {
      font-family: 'Exo 2', monospace;
      font-weight: 700;
      font-size: 0.9rem;
      color: var(--neon-cyan);
      letter-spacing: 1px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255,255,255,0.04);
      font-size: 0.9rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: var(--text-muted); }
    .info-row .value { font-weight: 600; text-align: right; }
    .info-row .value.accent { color: var(--neon-cyan); }
    .info-row .value.total { font-size: 1.1rem; font-weight: 800; color: var(--neon-green); }

    .rekening-box {
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(0,245,255,0.2);
      border-radius: 12px;
      padding: 20px;
      margin-top: 4px;
    }
    .rek-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 1px; }
    .rek-nomor { font-size: 1.4rem; font-weight: 800; font-family: 'Exo 2', monospace; color: #fff; letter-spacing: 2px; margin-bottom: 4px; }
    .rek-nama  { font-size: 0.85rem; color: var(--text-muted); }
    .copy-btn  {
      margin-top: 12px; padding: 8px 16px;
      background: rgba(0,245,255,0.1); border: 1px solid rgba(0,245,255,0.3);
      border-radius: 8px; color: var(--neon-cyan);
      font-family: 'Exo 2', sans-serif; font-size: 0.82rem; font-weight: 600;
      cursor: pointer; transition: all 0.2s;
    }
    .copy-btn:hover  { background: rgba(0,245,255,0.2); }
    .copy-btn.copied { background: rgba(0,255,136,0.15); border-color: rgba(0,255,136,0.4); color: var(--neon-green); }

    .timer-box {
      display: flex; align-items: center; justify-content: center; gap: 10px;
      padding: 14px;
      background: rgba(255,106,0,0.08); border: 1px solid rgba(255,106,0,0.25);
      border-radius: 10px; margin-bottom: 20px;
    }
    .timer-box span:first-child { font-size: 0.85rem; color: var(--text-muted); }
    #countdown { font-family: 'Exo 2', monospace; font-weight: 800; font-size: 1.1rem; color: var(--neon-orange); text-shadow: 0 0 10px rgba(255,106,0,0.5); }

    .upload-area {
      border: 2px dashed rgba(0,245,255,0.2); border-radius: 12px;
      padding: 32px; text-align: center; cursor: pointer;
      transition: all 0.2s; position: relative;
    }
    .upload-area:hover, .upload-area.drag-over { border-color: var(--neon-cyan); background: rgba(0,245,255,0.04); }
    .upload-icon { font-size: 2.2rem; margin-bottom: 10px; }
    .upload-area p { color: var(--text-muted); font-size: 0.88rem; margin-bottom: 12px; }
    .upload-area input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }

    .preview-img { max-width: 100%; max-height: 300px; border-radius: 10px; margin-top: 14px; display: none; border: 1px solid rgba(255,255,255,0.1); }

    .status-badge {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 5px 12px; border-radius: 20px;
      font-size: 0.78rem; font-weight: 700; letter-spacing: 0.5px;
    }
    .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .badge-pending   { background: rgba(240,160,64,0.15); color: #f0a040; }
    .badge-waiting   { background: rgba(96,144,240,0.15); color: #6090f0; }
    .badge-confirmed { background: rgba(0,255,136,0.15); color: var(--neon-green); }
    .badge-rejected  { background: rgba(255,0,85,0.15); color: #ff0055; }

    .alert { padding: 13px 16px; border-radius: 10px; font-size: 0.88rem; margin-bottom: 16px; }
    .alert-success { background: rgba(0,255,136,0.1); border: 1px solid rgba(0,255,136,0.3); color: var(--neon-green); }
    .alert-error   { background: rgba(255,0,80,0.1); border: 1px solid rgba(255,0,80,0.3); color: #ff3060; }

    .btn-submit {
      width: 100%; padding: 15px;
      background: linear-gradient(135deg, var(--neon-cyan), #0080ff);
      border: none; border-radius: 12px; color: #000;
      font-family: 'Exo 2', sans-serif; font-size: 1rem; font-weight: 800;
      cursor: pointer; transition: all 0.2s; margin-top: 16px; letter-spacing: 0.04em;
    }
    .btn-submit:hover     { transform: translateY(-2px); box-shadow: 0 0 25px rgba(0,245,255,0.5); }
    .btn-submit:disabled  { opacity: 0.5; cursor: not-allowed; transform: none; }

    .steps-hint { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
    .step-item {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 12px 16px; background: rgba(0,0,0,0.2);
      border-radius: 10px; border: 1px solid rgba(255,255,255,0.04);
    }
    .step-num {
      width: 24px; height: 24px; border-radius: 50%;
      background: rgba(0,245,255,0.15); border: 1px solid rgba(0,245,255,0.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 0.75rem; font-weight: 700; color: var(--neon-cyan); flex-shrink: 0;
    }
    .step-text { font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; }
    .step-text strong { color: var(--text-primary); }
  </style>
</head>
<body>

  <nav class="navbar container">
    <a href="index.php" class="brand">
      <span class="brand-icon">⚡</span>
      <span class="brand-text">TopUp<span class="brand-highlight">Ku</span></span>
    </a>
  </nav>

  <main class="confirm-wrapper">

    <?php if ($order['status'] === 'paid'): ?>
    <div class="notif-banner notif-paid" id="notif-banner">
      <span class="notif-icon">🎉</span>
      <div class="notif-title">PEMBAYARAN DIKONFIRMASI!</div>
      <div class="notif-desc">
        Yeay! Pembayaran kamu <strong>berhasil diverifikasi</strong> oleh admin.<br>
        Diamond akan segera masuk ke akunmu dalam beberapa menit.
      </div>
      <span class="notif-order">Order <?= htmlspecialchars($order['order_id']) ?> ✓ Lunas</span>
    </div>

    <?php elseif ($order['status'] === 'rejected'): ?>
    <div class="notif-banner notif-rejected" id="notif-banner">
      <span class="notif-icon">❌</span>
      <div class="notif-title">PEMBAYARAN DITOLAK!</div>
      <div class="notif-desc">
        Admin <strong>menolak bukti pembayaran</strong> yang kamu kirim.<br>
        Kemungkinan bukti tidak jelas, nominal tidak sesuai, atau sudah kadaluarsa.<br><br>
        <strong>Silakan upload ulang bukti transfer yang benar di bawah.</strong>
      </div>
      <span class="notif-order">Order <?= htmlspecialchars($order['order_id']) ?> ✗ Ditolak</span>
    </div>

    <?php elseif ($order['status'] === 'waiting_confirmation'): ?>
    <div class="notif-banner notif-waiting" id="notif-banner">
      <span class="notif-icon">⏳</span>
      <div class="notif-title">MENUNGGU KONFIRMASI ADMIN</div>
      <div class="notif-desc">
        Bukti transfer kamu sudah diterima.<br>
        Admin sedang memverifikasi pembayaranmu, harap tunggu maksimal <strong>1×24 jam</strong>.
      </div>
      <span class="notif-order">Order <?= htmlspecialchars($order['order_id']) ?></span>
    </div>
    <?php endif; ?>

    <div class="order-id-box">
      <span>Nomor Order</span>
      <span><?= htmlspecialchars($order['order_id']) ?></span>
    </div>

    <?php if (in_array($order['status'], ['pending', 'rejected'])): ?>
    <div class="timer-box">
      <span>⏱ Selesaikan pembayaran dalam</span>
      <span id="countdown">24:00:00</span>
    </div>
    <?php endif; ?>

    <div class="confirm-card">
      <h3>📋 Detail Order</h3>
      <div class="info-row">
        <span class="label">Game</span>
        <span class="value"><?= htmlspecialchars($order['product_name']) ?></span>
      </div>
      <div class="info-row">
        <span class="label">User ID</span>
        <span class="value accent"><?= htmlspecialchars($buyer['user_id'] ?? '-') ?></span>
      </div>
      <?php if (!empty($buyer['zone_id'])): ?>
      <div class="info-row">
        <span class="label">Zone ID</span>
        <span class="value"><?= htmlspecialchars($buyer['zone_id']) ?></span>
      </div>
      <?php endif; ?>
      <div class="info-row">
        <span class="label">Metode Bayar</span>
        <span class="value"><?= htmlspecialchars($order['payment_name']) ?></span>
      </div>
      <div class="info-row">
        <span class="label">Harga Produk</span>
        <span class="value">Rp <?= number_format($order['price_product'], 0, ',', '.') ?></span>
      </div>
      <div class="info-row">
        <span class="label">Fee Pembayaran</span>
        <span class="value">Rp <?= number_format($order['fee_payment'], 0, ',', '.') ?></span>
      </div>
      <div class="info-row">
        <span class="label">Total Bayar</span>
        <span class="value total">Rp <?= number_format($order['total_paid'], 0, ',', '.') ?></span>
      </div>
      <div class="info-row">
        <span class="label">Status</span>
        <span class="value">
          <?php
          $status_map = [
            'pending'              => ['class' => 'badge-pending',   'label' => 'Menunggu Pembayaran'],
            'waiting_confirmation' => ['class' => 'badge-waiting',   'label' => 'Menunggu Konfirmasi Admin'],
            'rejected'             => ['class' => 'badge-rejected',  'label' => '❌ Pembayaran Ditolak'],
            'paid'                 => ['class' => 'badge-confirmed', 'label' => '✅ Lunas'],
            'processing'           => ['class' => 'badge-waiting',   'label' => 'Diproses'],
            'success'              => ['class' => 'badge-confirmed', 'label' => 'Selesai'],
          ];
          $s = $status_map[$order['status']] ?? ['class' => 'badge-pending', 'label' => $order['status']];
          ?>
          <span class="status-badge <?= $s['class'] ?>" id="status-badge"><?= $s['label'] ?></span>
        </span>
      </div>
    </div>

    <?php if (in_array($order['status'], ['pending', 'rejected'])): ?>
    <div class="confirm-card">
      <h3>💳 Cara Pembayaran</h3>
      <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:16px;">
        Transfer tepat sesuai nominal ke rekening berikut:
      </p>
      <div class="rekening-box">
        <div class="rek-label"><?= htmlspecialchars($info_bayar['label']) ?></div>
        <div class="rek-nomor" id="rek-nomor"><?= htmlspecialchars($info_bayar['nomor']) ?></div>
        <div class="rek-nama">a/n <?= htmlspecialchars($info_bayar['atas_nama']) ?></div>
        <button class="copy-btn" id="copy-btn" onclick="copyNomor()">📋 Salin Nomor</button>
      </div>
      <div class="rekening-box" style="margin-top:12px;">
        <div class="rek-label">Jumlah Transfer</div>
        <div class="rek-nomor">Rp <?= number_format($order['total_paid'], 0, ',', '.') ?></div>
        <div class="rek-nama">Transfer tepat sesuai nominal di atas</div>
        <button class="copy-btn" onclick="copyJumlah()">📋 Salin Jumlah</button>
      </div>
    </div>

    <div class="confirm-card" id="upload-card">
      <h3>📤 Upload Bukti Transfer</h3>

      <?php if ($upload_success): ?>
        <div class="alert alert-success">✅ Bukti transfer berhasil diupload! Admin akan memverifikasi dalam 1×24 jam.</div>

      <?php elseif ($order['status'] === 'rejected'): ?>
        <div class="alert alert-error">
          ❌ Bukti pembayaran sebelumnya ditolak admin.
          <br><small>Silakan upload ulang bukti yang jelas dan sesuai nominal.</small>
        </div>
        <form method="POST" enctype="multipart/form-data" id="upload-form">
          <div class="upload-area" id="upload-area">
            <div class="upload-icon">📸</div>
            <p>Klik atau drag & drop foto bukti transfer BARU</p>
            <p style="font-size:0.78rem;">Format: JPG, PNG, WEBP • Maks. 5MB</p>
            <input type="file" name="bukti" id="bukti-input" accept="image/*" required>
          </div>
          <img id="preview-img" class="preview-img" src="" alt="Preview">
          <button type="submit" class="btn-submit" id="submit-btn" disabled>
            🔄 Kirim Ulang Bukti Transfer
          </button>
        </form>

      <?php else: ?>
        <?php if ($upload_error): ?>
          <div class="alert alert-error">❌ <?= htmlspecialchars($upload_error) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" id="upload-form">
          <div class="upload-area" id="upload-area">
            <div class="upload-icon">📸</div>
            <p>Klik atau drag & drop foto bukti transfer</p>
            <p style="font-size:0.78rem;">Format: JPG, PNG, WEBP • Maks. 5MB</p>
            <input type="file" name="bukti" id="bukti-input" accept="image/*" required>
          </div>
          <img id="preview-img" class="preview-img" src="" alt="Preview">
          <button type="submit" class="btn-submit" id="submit-btn" disabled>
            Kirim Bukti Transfer
          </button>
        </form>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($order['status'] !== 'paid'): ?>
    <div class="confirm-card">
      <h3>📌 Langkah Selanjutnya</h3>
      <div class="steps-hint">
        <div class="step-item">
          <div class="step-num">1</div>
          <div class="step-text"><strong>Transfer</strong> ke nomor rekening di atas sesuai total pembayaran</div>
        </div>
        <div class="step-item">
          <div class="step-num">2</div>
          <div class="step-text"><strong>Upload bukti</strong> screenshot/foto bukti transfer kamu</div>
        </div>
        <div class="step-item">
          <div class="step-num">3</div>
          <div class="step-text"><strong>Tunggu konfirmasi</strong> admin. Diamond akan langsung masuk ke akunmu</div>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </main>

  <script>
    // ============================================
    // SCRIPT UTK SCROLL OTOMATIS KE KARTU UPLOAD
    // ============================================
    (function() {
      if (window.location.href.includes('scrollTo=upload')) {
        setTimeout(() => {
          const uploadCard = document.getElementById('upload-card');
          if (uploadCard) {
            uploadCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }, 400);
      }
    })();

    // ============================================
    // Timer countdown 24 jam
    // ============================================
    (function() {
      const el = document.getElementById('countdown');
      if (!el) return;
      const created = new Date('<?= $order['created_at'] ?>').getTime();
      const deadline = created + (24 * 60 * 60 * 1000);
      const tick = () => {
        const now = Date.now();
        const diff = deadline - now;
        if (diff <= 0) { el.textContent = 'Waktu habis'; return; }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        setTimeout(tick, 1000);
      };
      tick();
    })();

    // ============================================
    // Copy rekening
    // ============================================
    function copyNomor() {
      const nomor = document.getElementById('rek-nomor').textContent;
      navigator.clipboard.writeText(nomor).then(() => {
        const btn = document.getElementById('copy-btn');
        btn.textContent = '✅ Tersalin!';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = '📋 Salin Nomor'; btn.classList.remove('copied'); }, 2000);
      });
    }

    function copyJumlah() {
      const jumlah = '<?= $order['total_paid'] ?>';
      navigator.clipboard.writeText(jumlah).then(() => alert('Jumlah tersalin: Rp ' + parseInt(jumlah).toLocaleString('id-ID')));
    }

    // ============================================
    // Preview gambar & drag drop
    // ============================================
    const input      = document.getElementById('bukti-input');
    const preview    = document.getElementById('preview-img');
    const submitBtn  = document.getElementById('submit-btn');
    const uploadArea = document.getElementById('upload-area');

    if (input) {
      input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            submitBtn.disabled = false;
          };
          reader.readAsDataURL(file);
        }
      });
    }

    if (uploadArea) {
      uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('drag-over'); });
      uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('drag-over'));
      uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files[0]) {
          input.files = e.dataTransfer.files;
          input.dispatchEvent(new Event('change'));
        }
      });
    }

    // ============================================
    // POLLING REAL-TIME — cek status tiap 5 detik
    // ============================================
    (function() {
      const statusAwal = '<?= $order['status'] ?>';
      if (statusAwal === 'paid' || statusAwal === 'pending') return;

      const orderId = '<?= htmlspecialchars($order['order_id'], ENT_QUOTES) ?>';
      let pollInterval;

      function ubahBanner(status) {
        const isPaid = status === 'paid';
        const banner = document.getElementById('notif-banner');
        if (!banner) return;

        // Ganti class banner agar warna ikut berubah
        banner.className = 'notif-banner ' + (isPaid ? 'notif-paid' : 'notif-rejected');

        // Ganti isi banner langsung di tempat
        banner.innerHTML = `
          <span class="notif-icon">${isPaid ? '🎉' : '❌'}</span>
          <div class="notif-title">${isPaid ? 'PEMBAYARAN DIKONFIRMASI!' : 'PEMBAYARAN DITOLAK!'}</div>
          <div class="notif-desc">
            ${isPaid
              ? 'Yeay! Pembayaran kamu <strong>berhasil diverifikasi</strong> oleh admin.<br>Diamond akan segera masuk ke akunmu dalam beberapa menit.'
              : 'Admin <strong>menolak bukti pembayaran</strong> yang kamu kirim.<br>Silakan upload ulang bukti transfer yang benar di bawah.'
            }
          </div>
          <span class="notif-order">${isPaid ? '✓ Lunas' : '✗ Ditolak'}</span>
        `;

        // Update juga badge status di tabel detail
        const badge = document.getElementById('status-badge');
        if (badge) {
          badge.className = 'status-badge ' + (isPaid ? 'badge-confirmed' : 'badge-rejected');
          badge.textContent = isPaid ? '✅ Lunas' : '❌ Pembayaran Ditolak';
        }

        // Kalau ditolak, reload setelah 7 detik lalu scroll ke form upload
        if (!isPaid) {
          setTimeout(() => {
            location.href = location.href + '&scrollTo=upload';
          }, 7000);
        }
      }

      function cekStatus() {
        fetch('cek_status.php?order=' + encodeURIComponent(orderId))
          .then(r => r.json())
          .then(data => {
            if (!data.success) return;
            if (data.status === 'paid' || data.status === 'rejected') {
              clearInterval(pollInterval);
              ubahBanner(data.status);
            }
          })
          .catch(() => {});
      }

      // Mulai polling setiap 5 detik
      pollInterval = setInterval(cekStatus, 5000);

      // Pause saat tab tidak aktif, resume saat aktif lagi
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          clearInterval(pollInterval);
        } else {
          cekStatus();
          pollInterval = setInterval(cekStatus, 5000);
        }
      });
    })();
  </script>

  <!-- ====== BACKGROUND ANIMASI (baru) ====== -->
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