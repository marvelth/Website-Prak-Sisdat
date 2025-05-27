<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id_kendaraan = mysqli_real_escape_string($conn, $_GET['id']);

// Cek apakah kendaraan sedang digunakan oleh kurir
$query_check = "SELECT COUNT(*) as count FROM kurir WHERE id_kendaraan = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "s", $id_kendaraan);
mysqli_stmt_execute($stmt_check);
$result = mysqli_stmt_get_result($stmt_check);
$count = mysqli_fetch_assoc($result)['count'];

if ($count > 0) {
    $_SESSION['error'] = "Kendaraan tidak dapat dihapus karena sedang digunakan oleh kurir";
    header("Location: list.php");
    exit;
}

// Jika aman, hapus kendaraan
$query = "DELETE FROM kendaraan WHERE id_kendaraan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kendaraan);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Kendaraan berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus kendaraan: " . mysqli_error($conn);
}

header("Location: list.php");
exit;
