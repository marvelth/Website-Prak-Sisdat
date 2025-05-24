<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);

// Check if order has active delivery
$query = "SELECT COUNT(*) as total FROM pengiriman WHERE id_pesanan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_pesanan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['total'] > 0) {
    $_SESSION['error'] = "Pesanan tidak dapat dihapus karena sedang dalam proses pengiriman";
    header("Location: list.php");
    exit;
}

// If safe to delete, proceed with deletion
$query = "DELETE FROM pesanan WHERE id_pesanan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_pesanan);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Pesanan berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus pesanan: " . mysqli_error($conn);
}

header("Location: list.php");
exit;
