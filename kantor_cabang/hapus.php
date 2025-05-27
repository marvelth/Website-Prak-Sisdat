<?php
include '../config.php';
session_start();

// Check if user is head office
if (!isset($_SESSION['id_cabang']) || $_SESSION['id_cabang'] !== 'KC001') {
    header("Location: ../index.php");
    exit();
}

// Validate ID parameter
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID Cabang tidak ditemukan";
    header("Location: list.php");
    exit;
}

$id_cabang = mysqli_real_escape_string($conn, $_GET['id']);

// Prevent deletion of head office
if ($id_cabang === 'KC001') {
    $_SESSION['error'] = "Kantor Pusat tidak dapat dihapus";
    header("Location: list.php");
    exit;
}

// Check related data before deletion
$tables = [
    'kurir' => 'kurir',
    'kendaraan' => 'kendaraan',
    'pelanggan' => 'pelanggan'
];

foreach ($tables as $name => $table) {
    $query = "SELECT COUNT(*) as count FROM $table WHERE id_cabang = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $id_cabang);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_assoc($result)['count'];
    
    if ($count > 0) {
        $_SESSION['error'] = "Tidak dapat menghapus cabang karena masih memiliki data $name";
        header("Location: list.php");
        exit;
    }
}

// Delete using prepared statement
$query_delete = "DELETE FROM kantor_cabang WHERE id_cabang = ?";
$stmt_delete = mysqli_prepare($conn, $query_delete);
mysqli_stmt_bind_param($stmt_delete, "s", $id_cabang);

if (mysqli_stmt_execute($stmt_delete)) {
    $_SESSION['success'] = "Kantor cabang berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus kantor cabang: " . mysqli_error($conn);
}

header("Location: list.php");
exit;
?>
