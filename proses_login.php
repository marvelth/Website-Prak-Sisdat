<?php
session_start();
include 'config.php';

if (isset($_POST['id_cabang']) && isset($_POST['password'])) {
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM kantor_cabang WHERE id_cabang = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $id_cabang, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['id_cabang'] = $row['id_cabang'];
        $_SESSION['nama_cabang'] = $row['nama_cabang'];
        $_SESSION['is_pusat'] = ($row['id_cabang'] == 'KP001'); // Asumsikan KP001 adalah ID kantor pusat
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "ID Cabang atau Password salah!";
        header("Location: index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Silakan isi semua field!";
    header("Location: index.php");
    exit;
}