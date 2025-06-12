<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}

$id_kurir = mysqli_real_escape_string($conn, $_GET['id']);

// Check if courier has active deliveries
$query = "SELECT COUNT(*) as total FROM pengiriman WHERE id_kurir = ? AND status_pengiriman != 'Selesai'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kurir);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['total'] > 0) {
    $_SESSION['error'] = "Kurir tidak dapat dihapus karena masih memiliki pengiriman aktif";
    header("Location: list.php");
    exit;
}

// If safe to delete, proceed with deletion
$query = "DELETE FROM kurir WHERE id_kurir = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kurir);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Kurir berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus kurir: " . mysqli_error($conn);
}

header("Location: list.php");
exit;
