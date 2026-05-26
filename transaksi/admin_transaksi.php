<?php
// 1. Koneksi Database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'tugas_web';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// 2. Logika Update Status (Konfirmasi / Tolak)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'confirm') {
        $status = 'paid';
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
    } elseif ($action == 'reject') {
        $status = 'rejected';
        $stmt = $conn->prepare("UPDATE orders SET status = ?, payment_proof = NULL WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
    }
    
    if ($stmt) {
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: admin_transaksi.php");
    exit();
}

// 3. Ambil data
$sql = "SELECT orders.*, games.name as nama_game, payment_methods.name as nama_metode 
        FROM orders 
        LEFT JOIN games ON orders.game_id = games.id 
        LEFT JOIN payment_methods ON orders.payment_method_id = payment_methods.id 
        ORDER BY orders.created_at DESC";
$result = $conn->query($sql);

// 4. Hitung Total Uang Masuk
$total_income = $conn->query("SELECT SUM(total_paid) as total FROM orders WHERE status = 'paid'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Transaksi - TopUpKu</title>
    <style>
        :root { --primary: #00f5ff; --bg: #0f0c29; --card: #1b1b3a; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: white; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--card); padding: 20px; border-radius: 15px; border-left: 5px solid var(--primary); }
        .stat-card p { margin: 10px 0 0; font-size: 24px; font-weight: bold; color: var(--primary); }
        .table-container { background: var(--card); border-radius: 15px; overflow-x: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(255,255,255,0.05); padding: 15px; text-align: left; color: var(--primary); }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; text-transform: uppercase; }
        .status-paid { background: #00ff8822; color: #00ff88; border: 1px solid #00ff88; }
        .status-waiting { background: #ffcc0022; color: #ffcc00; border: 1px solid #ffcc00; }
        .status-pending { background: #ff444422; color: #ff4444; border: 1px solid #ff4444; }
        .status-rejected { background: #ff005522; color: #ff0055; border: 1px solid #ff0055; }
        
        .proof-img { width: 60px; height: 40px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 1px solid var(--primary); transition: all 0.2s ease; }
        .proof-img:hover { opacity: 0.8; transform: scale(1.05); }

        .modal-bukti { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center; padding: 20px; }
        .modal-bukti.active { display: flex; }
        .modal-bukti img { max-width: 90%; max-height: 85vh; border-radius: 12px; border: 2px solid var(--primary); box-shadow: 0 20px 60px rgba(0,0,0,0.8); }
        .modal-bukti .close-btn { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; z-index: 10000; background: rgba(0,0,0,0.5); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid var(--primary); transition: all 0.2s; }
        .modal-bukti .close-btn:hover { background: var(--primary); color: black; }
        .modal-bukti .order-label { position: absolute; top: 30px; left: 30px; color: var(--primary); font-family: 'Exo 2', monospace; font-weight: 700; font-size: 1rem; background: rgba(0,0,0,0.6); padding: 8px 16px; border-radius: 8px; border: 1px solid var(--primary); }
        .btn-download { position: absolute; bottom: 30px; background: var(--primary); color: black; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 14px; transition: all 0.2s; box-shadow: 0 5px 15px rgba(0,245,255,0.3); }
        .btn-download:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,245,255,0.5); }
        
        .btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: bold; cursor: pointer; display: inline-block; }
        .btn-confirm { background: var(--primary); color: #000; }
        .btn-reject { background: transparent; color: #ff4444; border: 1px solid #ff4444; margin-left: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>🚀 Dashboard Admin Transaksi</h1>
    <div class="stats-grid">
        <div class="stat-card"><h3>Total Pendapatan (Paid)</h3><p>Rp <?php echo number_format($total_income['total'] ?? 0, 0, ',', '.'); ?></p></div>
        <div class="stat-card"><h3>Total Pesanan Masuk</h3><p><?php echo $result->num_rows; ?> Transaksi</p></div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID Order</th><th>Game & Data</th><th>Total Bayar</th><th>Metode</th><th>Bukti</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><b>#<?php echo $row['id']; ?></b></td>
                    <td><span style="color: var(--primary)"><?php echo $row['nama_game']; ?></span><br><small><?php echo $row['buyer_data']; ?></small></td>
                    <td>Rp <?php echo number_format($row['total_paid'], 0, ',', '.'); ?></td>
                    <td><?php echo $row['nama_metode']; ?></td>
                    <td>
                        <?php if($row['payment_proof']): ?>
                            <img src="../uploads/bukti/<?php echo htmlspecialchars($row['payment_proof']); ?>" class="proof-img" alt="Bukti" onclick="bukaModalBukti(this.src, '#<?php echo $row['id']; ?>')">
                        <?php else: ?>
                            <small style="color: #666;">Belum Upload</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                            $st = $row['status'];
                            $class = ($st == 'paid') ? 'status-paid' : (($st == 'waiting_confirmation') ? 'status-waiting' : (($st == 'rejected') ? 'status-rejected' : 'status-pending'));
                        ?>
                        <span class="badge <?php echo $class; ?>"><?php echo str_replace('_', ' ', $st); ?></span>
                    </td>
                    <td>
                        <?php if($row['status'] == 'waiting_confirmation'): ?>
                            <a href="?action=confirm&id=<?php echo $row['id']; ?>" class="btn btn-confirm" onclick="return confirm('✅ Yakin ingin MENGONFIRMASI?')">Konfirmasi</a>
                            <a href="?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-reject" onclick="return confirm('❌ Yakin ingin MENOLAK?')">Tolak</a>
                        <?php elseif($row['status'] == 'paid'): ?>
                            <small style="color: #00ff88;">✓ Terverifikasi</small>
                        <?php elseif($row['status'] == 'rejected'): ?>
                            <small style="color: #ff4444;">✗ Ditolak</small>
                        <?php else: ?>
                            <small style="color: #888;">Menunggu Pembayaran</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalBukti" class="modal-bukti" onclick="tutupModal(event)">
    <span class="close-btn" onclick="tutupModal(event)">&times;</span>
    <span class="order-label" id="orderLabel"></span>
    <img id="imgModal" src="" alt="Bukti Pembayaran">
    <a href="#" id="btnDownload" class="btn-download" download target="_blank">⬇️ Download Gambar</a>
</div>

<script>
function bukaModalBukti(src, orderId) {
    document.getElementById('imgModal').src = src;
    document.getElementById('btnDownload').href = src;
    document.getElementById('orderLabel').textContent = 'Order ' + orderId;
    document.getElementById('modalBukti').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function tutupModal(e) {
    if (e.target.id === 'modalBukti' || e.target.classList.contains('close-btn')) {
        document.getElementById('modalBukti').classList.remove('active');
        document.body.style.overflow = 'auto';
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('modalBukti').classList.remove('active');
        document.body.style.overflow = 'auto';
    }
});
</script>

</body>
</html>