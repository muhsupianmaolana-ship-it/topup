<?php
session_start();
require 'koneksi.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validasi input
$product_id        = isset($input['product_id']) ? (int)$input['product_id'] : 0;
$payment_method_id = isset($input['payment_method_id']) ? (int)$input['payment_method_id'] : 0;
$user_id           = isset($input['user_id']) ? trim($input['user_id']) : '';
$zone_id           = isset($input['zone_id']) ? trim($input['zone_id']) : '';

if (!$product_id || !$payment_method_id || !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    // Ambil data produk
    $stmt = $pdo->prepare("SELECT p.*, g.id as game_id FROM products p JOIN games g ON p.game_id = g.id WHERE p.id = ? AND p.status = 'available'");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
        exit;
    }

    // Ambil data metode pembayaran
    $stmt2 = $pdo->prepare("SELECT * FROM payment_methods WHERE id = ? AND is_active = 1");
    $stmt2->execute([$payment_method_id]);
    $payment = $stmt2->fetch();

    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Metode pembayaran tidak valid']);
        exit;
    }

    // Hitung total
    $price_product = $product['price_customer'];
    $fee_payment   = $payment['fee_fixed'];
    $total_paid    = $price_product + $fee_payment;

    // Buat order_id unik
    $order_id = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('Ymd');

    // Simpan buyer_data sebagai JSON
    $buyer_data = json_encode([
        'user_id' => $user_id,
        'zone_id' => $zone_id
    ]);

    // Insert ke tabel orders
    $stmt3 = $pdo->prepare("
    INSERT INTO orders (
        user_id,
        order_id,
        game_id,
        product_id,
        payment_method_id,
        buyer_data,
        price_product,
        fee_payment,
        total_paid,
        status,
        created_at,
        updated_at
    )
    VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW()
    )
");

$stmt3->execute([
    $_SESSION['user_id'],
    $order_id,
    $product['game_id'],
    $product_id,
    $payment_method_id,
    $buyer_data,
    $price_product,
    $fee_payment,
    $total_paid
]);
    $id = $pdo->lastInsertId();

    // Simpan order ke session untuk halaman konfirmasi
    $_SESSION['current_order'] = [
        'id'                => $id,
        'order_id'          => $order_id,
        'product_name'      => $product['name'],
        'payment_name'      => $payment['name'],
        'payment_type'      => $payment['type'],
        'price_product'     => $price_product,
        'fee_payment'       => $fee_payment,
        'total_paid'        => $total_paid,
        'user_id'           => $user_id,
        'zone_id'           => $zone_id,
    ];

    echo json_encode([
        'success'  => true,
        'order_id' => $order_id,
        'redirect' => 'order_confirm.php?order=' . urlencode($order_id)
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>