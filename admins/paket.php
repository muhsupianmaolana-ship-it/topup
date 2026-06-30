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
    #bg-canvas{
        position:fixed;
        top:0;
        left:0;
        width:100%;
        height:100%;
        z-index:-1;
        pointer-events:none;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root { --primary: #00f5ff; --bg: #0f0c29; --card: #1b1b3a; }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        color: #fff;
        position: relative;
        overflow-x: hidden;
    }

    .navbar,
    .container {
        position: relative;
        z-index: 2;
    }

    .navbar {
      background: var(--card);
      border-bottom: 1px solid rgba(255,255,255,0.08);
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    }
    .navbar .brand { font-size: 16px; font-weight: 700; color: #fff; text-decoration: none; }
    .navbar .brand span { color: var(--primary); }
    .navbar-right { display: flex; align-items: center; gap: 16px; font-size: 13px; color: #aaa; }
    .navbar-right a { color: #ff4d6d; text-decoration: none; font-size: 13px; }
    .navbar-right a[href="profil.php"] { color: var(--primary); }

    .container { max-width: 1000px; margin: 0 auto; padding: 24px 16px; }

    .page-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #fff; }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 13px;
      margin-bottom: 16px;
    }
    .alert-success { background: #00ff8822; color: #00ff88; border: 1px solid #00ff88; }
    .alert-error   { background: #ff005522; color: #ff0055; border: 1px solid #ff0055; }

    .card {
      background: var(--card);
      border-radius: 15px;
      border: 1px solid rgba(255,255,255,0.05);
      padding: 24px;
      margin-bottom: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .card-title { font-size: 14px; font-weight: 600; margin-bottom: 16px; color: var(--primary); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-size: 12px; font-weight: 500; color: #aaa; margin-bottom: 5px; }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 9px 12px;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 8px;
      font-size: 13px;
      color: #fff;
      outline: none;
      transition: border-color .2s;
    }
    .form-group input::placeholder { color: #777; }
    .form-group input:focus,
    .form-group select:focus { border-color: var(--primary); }
    .form-group select option { background: var(--card); color: #fff; }

    .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
    .btn-primary { background: var(--primary); color: #000; }
    .btn-primary:hover { box-shadow: 0 0 15px rgba(0,245,255,0.5); }
    .btn-warning { background: #ffcc00; color: #000; }
    .btn-warning:hover { box-shadow: 0 0 15px rgba(255,204,0,0.5); }
    .btn-cancel { background: transparent; color: #aaa; border: 1px solid rgba(255,255,255,0.15); }
    .btn-cancel:hover { background: rgba(255,255,255,0.08); }

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
      border: 1px solid rgba(255,255,255,0.1);
      color: #aaa;
      transition: all .2s;
    }
    .filter-bar a.active,
    .filter-bar a:hover { background: var(--primary); color: #000; border-color: var(--primary); }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th { text-align: left; padding: 15px 12px; color: var(--primary); font-weight: 500; background: rgba(255,255,255,0.05); font-size: 12px; }
    td { padding: 15px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); vertical-align: middle; color: #ddd; }
    tr:last-child td { border-bottom: none; }

    .badge { display: inline-block; font-size: 11px; font-weight: 500; padding: 5px 12px; border-radius: 20px; text-transform: uppercase; }
    .badge-available { background: #00ff8822; color: #00ff88; border: 1px solid #00ff88; }
    .badge-unavailable { background: #ff444422; color: #ff4444; border: 1px solid #ff4444; }

    .game-tag { font-size: 11px; padding: 5px 12px; border-radius: 20px; background: rgba(0,245,255,0.1); color: var(--primary); font-weight: 500; border: 1px solid rgba(0,245,255,0.3); }

    .action-btns { display: flex; gap: 6px; }
    .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 500; }
    .btn-edit { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; }
    .btn-edit:hover { background: rgba(255,255,255,0.12); }
    .btn-del { background: #ff005522; border: 1px solid #ff0055; color: #ff0055; }
    .btn-del:hover { background: #ff005540; }

    .price-val { font-weight: 600; color: var(--primary); }
  </style>
</head>
<body>

<canvas id="bg-canvas"></canvas>

<nav class="navbar">
  <a href="dashboard.php" class="brand">⚡ TopUp<span>Ku</span> <span style="font-weight:400;color:#888;font-size:13px;">Admin</span></a>
  <div class="navbar-right">
    <span>👤 <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
    <a href="profil.php" style="color:var(--primary); text-decoration:none;">Pengaturan Akun</a>
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
          <tr><td colspan="6" style="text-align:center; color:#666; padding:32px;">Belum ada paket.</td></tr>
        <?php else: ?>
          <?php foreach ($products as $i => $p): ?>
            <tr>
              <td style="color:#666;"><?= $i + 1 ?></td>
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

<script>
const canvas = document.getElementById('bg-canvas');

if(canvas){
    const ctx = canvas.getContext('2d');

    let w,h;
    let particles = [];

    function resize(){
        w = canvas.width = window.innerWidth;
        h = canvas.height = window.innerHeight;
    }

    resize();
    window.addEventListener('resize', resize);

    class Particle{
        constructor(){
            this.x = Math.random() * w;
            this.y = Math.random() * h;
            this.vx = (Math.random() - 0.5) * 0.6;
            this.vy = (Math.random() - 0.5) * 0.6;
            this.size = Math.random() * 2 + 1;

            const colors = [
                'rgba(0,245,255,',
                'rgba(191,0,255,',
                'rgba(0,255,136,'
            ];

            this.color = colors[Math.floor(Math.random()*colors.length)];
        }

        update(){
            this.x += this.vx;
            this.y += this.vy;

            if(this.x < 0 || this.x > w) this.vx *= -1;
            if(this.y < 0 || this.y > h) this.vy *= -1;
        }

        draw(){
            ctx.beginPath();
            ctx.arc(this.x,this.y,this.size,0,Math.PI*2);
            ctx.fillStyle = this.color + '0.8)';
            ctx.fill();
        }
    }

    for(let i=0;i<70;i++){
        particles.push(new Particle());
    }

    function animate(){
        ctx.clearRect(0,0,w,h);

        particles.forEach((p,i)=>{

            p.update();
            p.draw();

            for(let j=i+1;j<particles.length;j++){

                const dx = p.x - particles[j].x;
                const dy = p.y - particles[j].y;

                const dist = Math.sqrt(dx*dx + dy*dy);

                if(dist < 130){

                    ctx.beginPath();
                    ctx.moveTo(p.x,p.y);
                    ctx.lineTo(particles[j].x,particles[j].y);

                    ctx.strokeStyle =
                    'rgba(0,245,255,' + (0.12*(1-dist/130)) + ')';

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

</body>
</html>