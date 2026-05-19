<?php
require 'auth.php';
require '../koneksi.php';

$success = '';
$error   = '';

// TAMBAH paket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'tambah') {
    $game_id  = (int)$_POST['game_id'];
    $name     = trim($_POST['name']);
    $price    = (int)$_POST['price_customer'];
    $status   = $_POST['status'] ?? 'available';

    if ($game_id && $name && $price) {
        $stmt = $pdo->prepare("INSERT INTO products (game_id, name, price_customer, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$game_id, $name, $price, $status]);
        $success = 'Paket berhasil ditambahkan!';
    } else {
        $error = 'Harap isi semua field.';
    }
}

// EDIT paket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id      = (int)$_POST['id'];
    $name    = trim($_POST['name']);
    $price   = (int)$_POST['price_customer'];
    $status  = $_POST['status'] ?? 'available';

    if ($id && $name && $price) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price_customer = ?, status = ? WHERE id = ?");
        $stmt->execute([$name, $price, $status, $id]);
        $success = 'Paket berhasil diupdate!';
    } else {
        $error = 'Data tidak lengkap.';
    }
}

// HAPUS paket
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    $success = 'Paket berhasil dihapus!';
}

// Filter game
$filter_game = (int)($_GET['game_id'] ?? 0);

// Ambil semua game
$games = $pdo->query("SELECT * FROM games WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Ambil produk
if ($filter_game) {
    $stmt = $pdo->prepare("SELECT p.*, g.name as game_name FROM products p JOIN games g ON p.game_id = g.id WHERE p.game_id = ? ORDER BY p.price_customer ASC");
    $stmt->execute([$filter_game]);
} else {
    $stmt = $pdo->query("SELECT p.*, g.name as game_name FROM products p JOIN games g ON p.game_id = g.id ORDER BY g.sort_order ASC, p.price_customer ASC");
}
$products = $stmt->fetchAll();

// Ambil satu paket untuk edit
$edit_item = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_item = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Paket Diamond - Admin TopUpKu</title>
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

    .container { max-width: 1000px; margin: 0 auto; padding: 24px 16px; }

    .page-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 16px;
    }
    .alert-success { background: #eafaf1; color: #1e8449; border: 1px solid #a9dfbf; }
    .alert-error   { background: #fff0f0; color: #c0392b; border: 1px solid #fcd; }

    .card {
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e5e5e0;
      padding: 24px;
      margin-bottom: 20px;
    }
    .card-title { font-size: 14px; font-weight: 600; margin-bottom: 16px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-size: 12px; font-weight: 500; color: #555; margin-bottom: 5px; }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 9px 12px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 13px;
      color: #1a1a1a;
      outline: none;
      transition: border-color .2s;
    }
    .form-group input:focus,
    .form-group select:focus { border-color: #6c5ce7; }

    .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
    .btn-primary { background: #1a1a1a; color: #fff; }
    .btn-primary:hover { background: #333; }
    .btn-warning { background: #f39c12; color: #fff; }
    .btn-warning:hover { background: #d68910; }
    .btn-cancel { background: #f5f5f0; color: #555; border: 1px solid #e0e0e0; }
    .btn-cancel:hover { background: #e5e5e0; }

    .filter-bar {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }
    .filter-bar a {
      padding: 7px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      text-decoration: none;
      border: 1px solid #e0e0e0;
      color: #555;
      transition: all .2s;
    }
    .filter-bar a.active,
    .filter-bar a:hover { background: #1a1a1a; color: #fff; border-color: #1a1a1a; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { text-align: left; padding: 10px 12px; color: #888; font-weight: 500; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
    td { padding: 12px 12px; border-bottom: 1px solid #f5f5f0; vertical-align: middle; }
    tr:last-child td { border-bottom: none; }

    .badge { display: inline-block; font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 20px; }
    .badge-available { background: #eafaf1; color: #1e8449; }
    .badge-unavailable { background: #f5f5f0; color: #888; }

    .game-tag { font-size: 11px; padding: 3px 8px; border-radius: 20px; background: #eef2ff; color: #3730a3; font-weight: 500; }

    .action-btns { display: flex; gap: 6px; }
    .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 500; }
    .btn-edit { background: #fff; border: 1px solid #e0e0e0; color: #333; }
    .btn-edit:hover { background: #f5f5f0; }
    .btn-del { background: #fff0f0; border: 1px solid #fcd; color: #c0392b; }
    .btn-del:hover { background: #ffe0e0; }

    .price-val { font-weight: 600; }
  </style>
</head>
<body>

<nav class="navbar">
  <a href="dashboard.php" class="brand">⚡ TopUp<span>Ku</span> <span style="font-weight:400;color:#888;font-size:13px;">Admin</span></a>
  <div class="navbar-right">
    <span>👤 <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
    <a href="profil.php" style="color:#555; text-decoration:none;">Profil</a>
    <a href="logout.php">Keluar</a>
  </div>
</nav>

<div class="container">
  <div class="page-title">Kelola Paket Diamond</div>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Form Tambah / Edit -->
  <div class="card">
    <div class="card-title"><?= $edit_item ? 'Edit Paket' : 'Tambah Paket Baru' ?></div>
    <form method="POST">
      <input type="hidden" name="action" value="<?= $edit_item ? 'edit' : 'tambah' ?>">
      <?php if ($edit_item): ?>
        <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
      <?php endif; ?>
      <div class="form-row">
        <div class="form-group">
          <label>Game</label>
          <select name="game_id" required>
            <option value="">Pilih game...</option>
            <?php foreach ($games as $g): ?>
              <option value="<?= $g['id'] ?>"
                <?= ($edit_item && $edit_item['game_id'] == $g['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($g['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Paket</label>
          <input type="text" name="name" placeholder="contoh: 86 Diamond"
                 value="<?= htmlspecialchars($edit_item['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>Harga (Rp)</label>
          <input type="number" name="price_customer" placeholder="contoh: 19000"
                 value="<?= $edit_item['price_customer'] ?? '' ?>" required>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            <option value="available" <?= (!$edit_item || $edit_item['status'] === 'available') ? 'selected' : '' ?>>Tersedia</option>
            <option value="unavailable" <?= ($edit_item && $edit_item['status'] === 'unavailable') ? 'selected' : '' ?>>Nonaktif</option>
          </select>
        </div>
      </div>
      <div style="display:flex; gap:10px; margin-top:4px;">
        <button type="submit" class="btn <?= $edit_item ? 'btn-warning' : 'btn-primary' ?>">
          <?= $edit_item ? 'Simpan Perubahan' : 'Tambah Paket' ?>
        </button>
        <?php if ($edit_item): ?>
          <a href="paket.php" class="btn btn-cancel">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Tabel Paket -->
  <div class="card">
    <div class="filter-bar">
      <a href="paket.php" class="<?= !$filter_game ? 'active' : '' ?>">Semua Game</a>
      <?php foreach ($games as $g): ?>
        <a href="paket.php?game_id=<?= $g['id'] ?>"
           class="<?= $filter_game == $g['id'] ? 'active' : '' ?>">
          <?= htmlspecialchars($g['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Paket</th>
          <th>Game</th>
          <th>Harga</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
          <tr><td colspan="6" style="text-align:center; color:#aaa; padding:32px;">Belum ada paket.</td></tr>
        <?php else: ?>
          <?php foreach ($products as $i => $p): ?>
            <tr>
              <td style="color:#aaa;"><?= $i + 1 ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><span class="game-tag"><?= htmlspecialchars($p['game_name']) ?></span></td>
              <td class="price-val">Rp <?= number_format($p['price_customer'], 0, ',', '.') ?></td>
              <td>
                <span class="badge <?= $p['status'] === 'available' ? 'badge-available' : 'badge-unavailable' ?>">
                  <?= $p['status'] === 'available' ? 'Tersedia' : 'Nonaktif' ?>
                </span>
              </td>
              <td>
                <div class="action-btns">
                  <a href="paket.php?edit=<?= $p['id'] ?><?= $filter_game ? '&game_id='.$filter_game : '' ?>" class="btn-sm btn-edit">Edit</a>
                  <a href="paket.php?hapus=<?= $p['id'] ?><?= $filter_game ? '&game_id='.$filter_game : '' ?>"
                     class="btn-sm btn-del"
                     onclick="return confirm('Hapus paket <?= htmlspecialchars($p['name']) ?>?')">Hapus</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>