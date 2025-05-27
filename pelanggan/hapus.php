<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID Pelanggan tidak ditemukan";
    header("Location: list.php");
    exit;
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');
$id_pelanggan = mysqli_real_escape_string($conn, $_GET['id']);

// Cek apakah pelanggan ada dan sesuai dengan cabang
$query_check = "SELECT id_cabang FROM pelanggan WHERE id_pelanggan = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "s", $id_pelanggan);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$pelanggan = mysqli_fetch_assoc($result_check);

if (!$pelanggan) {
    $_SESSION['error'] = "Pelanggan tidak ditemukan";
    header("Location: list.php");
    exit;
}

// Cek hak akses cabang
if (!$is_kantor_pusat && $pelanggan['id_cabang'] !== $_SESSION['id_cabang']) {
    $_SESSION['error'] = "Anda tidak memiliki akses untuk menghapus pelanggan ini";
    header("Location: list.php");
    exit;
}

// Cek pesanan dengan prepared statement
$query_pesanan = "SELECT COUNT(*) as total FROM pesanan WHERE id_pelanggan = ?";
$stmt_pesanan = mysqli_prepare($conn, $query_pesanan);
mysqli_stmt_bind_param($stmt_pesanan, "s", $id_pelanggan);
mysqli_stmt_execute($stmt_pesanan);
$result_pesanan = mysqli_stmt_get_result($stmt_pesanan);
$data = mysqli_fetch_assoc($result_pesanan);

if ($data['total'] > 0) {
    $_SESSION['error'] = "Pelanggan tidak dapat dihapus karena masih memiliki pesanan";
    header("Location: list.php");
    exit;
}

// Hapus pelanggan dengan prepared statement
$query_delete = "DELETE FROM pelanggan WHERE id_pelanggan = ?";
$stmt_delete = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt_delete, "s", $id_pelanggan);

if (mysqli_stmt_execute($stmt_delete)) {
    $_SESSION['success'] = "Pelanggan berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus pelanggan: " . mysqli_error($conn);
}

header("Location: list.php");
exit;
?>
