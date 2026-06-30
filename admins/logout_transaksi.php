<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus hanya session admin transaksi, tidak ganggu session admin master
unset($_SESSION['transaksi_logged_in']);
unset($_SESSION['transaksi_id']);
unset($_SESSION['transaksi_username']);

header("Location: login_transaksi.php");
exit();