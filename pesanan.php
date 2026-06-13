<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        o.*,
        g.name AS game_name,
        p.name AS product_name,
        pm.name AS payment_name
    FROM orders o
    LEFT JOIN games g ON o.game_id = g.id
    LEFT JOIN products p ON o.product_id = p.id
    LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Saya - TopUpKu</title>

<link rel="stylesheet" href="web.css">

<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* BACKGROUND ANIMATION */



body{
    overflow-x:hidden;
}

.page-header,
.order-list,
.navbar{
    position:relative;
    z-index:2;
}#bg-canvas{
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
    background:#070b14;
    overflow-x:hidden;
}

.navbar,
.page-header,
.order-list{
    position:relative;
    z-index:2;
}

/* Glass Effect */

.order-card{
    background:rgba(15,23,37,.85);
    backdrop-filter:blur(10px);
    -webkit-backdrop-filter:blur(10px);
}

.info-box{
    background:rgba(255,255,255,.05);
    backdrop-filter:blur(10px);
}

.empty{
    background:rgba(15,23,37,.85);
    backdrop-filter:blur(10px);
}
:root{
    --bg:#070b14;
    --card:#0f1725;
    --border:rgba(0,245,255,.15);
    --cyan:#00f5ff;
    --green:#00ff88;
    --orange:#ff8a00;
    --red:#ff4560;
}

body{
    background:#070b14;
    color:white;
    font-family:'Exo 2',sans-serif;
}

.page-header{
    padding:40px 20px;
    text-align:center;
}

.page-title{
    font-size:32px;
    font-weight:800;
    color:var(--cyan);
    text-shadow:0 0 15px rgba(0,245,255,.5);
}

.page-subtitle{
    color:#9ca3af;
    margin-top:10px;
}

.order-list{
    max-width:1200px;
    margin:auto;
    padding:0 20px 60px;
}

.order-card{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:18px;
    padding:24px;
    margin-bottom:20px;
    transition:.3s;
}

.order-card:hover{
    transform:translateY(-3px);
    border-color:rgba(0,245,255,.4);
    box-shadow:0 0 25px rgba(0,245,255,.15);
}

.order-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
}

.order-id{
    color:var(--cyan);
    font-weight:700;
    font-size:15px;
}

.order-game{
    font-size:20px;
    font-weight:700;
    margin-top:10px;
}

.order-product{
    color:#9ca3af;
    margin-top:5px;
}

.order-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:15px;
    margin-top:20px;
}

.info-box{
    background:rgba(255,255,255,.03);
    border-radius:12px;
    padding:12px;
}

.info-label{
    font-size:12px;
    color:#9ca3af;
    margin-bottom:5px;
}

.info-value{
    font-weight:700;
}

.total{
    color:var(--green);
}

.status{
    display:inline-flex;
    align-items:center;
    padding:8px 14px;
    border-radius:999px;
    font-size:13px;
    font-weight:700;
}

.pending{
    background:rgba(255,170,0,.15);
    color:#ffaa00;
}

.waiting_confirmation{
    background:rgba(0,136,255,.15);
    color:#4da6ff;
}

.paid{
    background:rgba(0,255,136,.15);
    color:var(--green);
}

.processing{
    background:rgba(180,0,255,.15);
    color:#d16eff;
}

.success{
    background:rgba(0,255,136,.15);
    color:var(--green);
}

.rejected{
    background:rgba(255,0,80,.15);
    color:var(--red);
}

.btn-detail{
    margin-top:20px;
    display:inline-block;
    padding:12px 18px;
    border-radius:10px;
    background:linear-gradient(135deg,#00f5ff,#0084ff);
    color:#000;
    text-decoration:none;
    font-weight:700;
}

.empty{
    text-align:center;
    padding:80px 20px;
    background:var(--card);
    border-radius:20px;
    border:1px solid var(--border);
}

.empty h3{
    margin-top:20px;
    color:var(--cyan);
}

@media(max-width:768px){

.order-top{
    flex-direction:column;
    align-items:flex-start;
}


</style>
</head>
<body>

<nav class="navbar">
    <div class="container navbar-inner">
        <a href="index.php" class="brand">
            ⚡ TopUpKu
        </a>
    </div>
</nav>

<div class="page-header">
    <div class="page-title">
        📦 Pesanan Saya
    </div>

    <div class="page-subtitle">
        Riwayat seluruh transaksi top up kamu
    </div>
</div>

<div class="order-list">

<?php if(empty($orders)): ?>

<div class="empty">
    <div style="font-size:70px">🎮</div>
    <h3>Belum Ada Pesanan</h3>
    <p>Kamu belum pernah melakukan top up.</p>
</div>

<?php else: ?>

<?php foreach($orders as $order): ?>

<div class="order-card">

    <div class="order-top">

        <div>
            <div class="order-id">
                <?= htmlspecialchars($order['order_id']) ?>
            </div>

            <div class="order-game">
                <?= htmlspecialchars($order['game_name']) ?>
            </div>

            <div class="order-product">
                <?= htmlspecialchars($order['product_name']) ?>
            </div>
        </div>

        <div class="status <?= $order['status'] ?>">
            <?= strtoupper($order['status']) ?>
        </div>

    </div>

    <div class="order-grid">

        <div class="info-box">
            <div class="info-label">Metode Pembayaran</div>
            <div class="info-value">
                <?= htmlspecialchars($order['payment_name']) ?>
            </div>
        </div>

        <div class="info-box">
            <div class="info-label">Total Bayar</div>
            <div class="info-value total">
                Rp <?= number_format($order['total_paid'],0,',','.') ?>
            </div>
        </div>

        <div class="info-box">
            <div class="info-label">Tanggal</div>
            <div class="info-value">
                <?= date('d M Y H:i',strtotime($order['created_at'])) ?>
            </div>
        </div>

    </div>

    <a href="order_confirm.php?order=<?= urlencode($order['order_id']) ?>" class="btn-detail">
        Lihat Detail
    </a>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>


<canvas id="bg-canvas"></canvas>

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

    for(let i=0;i<60;i++){
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