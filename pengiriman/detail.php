<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id_pengiriman = mysqli_real_escape_string($conn, $_GET['id']);

// Get delivery details with related information
$query = "SELECT pg.*, ps.id_pesanan, k.nama_kurir, k.no_telepon as kurir_telepon,
          pl.nama_pelanggan, pl.alamat, pl.telepon as pelanggan_telepon
          FROM pengiriman pg
          JOIN pesanan ps ON pg.id_pesanan = ps.id_pesanan
          JOIN kurir k ON pg.id_kurir = k.id_kurir
          JOIN pelanggan pl ON ps.id_pelanggan = pl.id_pelanggan
          WHERE pg.id_pengiriman = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_pengiriman);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengiriman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Informasi Pengiriman</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><td width="200">ID Pengiriman:</td><td><?= $data['id_pengiriman'] ?></td></tr>
                    <tr><td>ID Pesanan:</td><td><?= $data['id_pesanan'] ?></td></tr>
                    <tr><td>Tanggal Kirim:</td><td><?= $data['tanggal_kirim'] ?></td></tr>
                    <tr><td>Tanggal Sampai:</td><td><?= $data['tanggal_sampai'] ?? 'Belum sampai' ?></td></tr>
                    <tr><td>Status:</td><td><?= htmlspecialchars($data['status_pengiriman']) ?></td></tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Kurir</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr><td>Nama Kurir:</td><td><?= htmlspecialchars($data['nama_kurir']) ?></td></tr>
                            <tr><td>No. Telepon:</td><td><?= htmlspecialchars($data['kurir_telepon']) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Pengirim</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr><td>Nama:</td><td><?= htmlspecialchars($data['nama_pelanggan']) ?></td></tr>
                            <tr><td>Alamat:</td><td><?= htmlspecialchars($data['alamat']) ?></td></tr>
                            <tr><td>No. Telepon:</td><td><?= htmlspecialchars($data['pelanggan_telepon']) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <a href="list.php" class="btn btn-secondary">Kembali ke List Pengiriman</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
