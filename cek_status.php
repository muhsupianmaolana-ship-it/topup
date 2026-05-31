<?php
require 'koneksi.php';

header('Content-Type: application/json');

$order_id = $_GET['order'] ?? '';

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID kosong']);
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$row = $stmt->fetch();

if ($row) {
    echo json_encode(['success' => true, 'status' => $row['status']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order tidak ditemukan']);
}
?>