<?php
include '../config.php';
session_start();
$id_pelanggan = $_GET['id'];

// Cek apakah pelanggan memiliki pesanan
$cek = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE id_pelanggan = $id_pelanggan");
$data = mysqli_fetch_assoc($cek);

if ($data['total'] > 0) {
    // Redirect dengan pesan error
    $_SESSION['error'] = "Pelanggan tidak dapat dihapus karena masih memiliki pesanan.";
    header("Location: list.php");
    exit;
}

// Jika data pelanggan aman dihapus
mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan = $id_pelanggan");
$_SESSION['success'] = "Pelanggan berhasil dihapus.";
header("Location: list.php");
exit;
?>
