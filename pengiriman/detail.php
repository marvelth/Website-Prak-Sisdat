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
    <!--Bootstrap-->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="../assets/font-awesome/css/all.min.css">
    <!--CSS-->
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top" style="background-color: #003B73;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
                <img src="../assets/img/logo.png" alt="Padjadjaran Express" height="30" class="me-2">
                <span>Padjadjaran Express</span>
            </a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link">
                    <i class="fa fa-building me-2"></i><?= htmlspecialchars($_SESSION['id_cabang']) ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="border-bottom pb-2">
                    <i class="fas fa-info-circle me-2"></i>Detail Pengiriman
                </h2>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman
                </h5>
                <table class="table">
                    <tr><td width="200">ID Pengiriman:</td><td><?= $data['id_pengiriman'] ?></td></tr>
                    <tr><td>ID Pesanan:</td><td><?= $data['id_pesanan'] ?></td></tr>
                    <tr><td>Tanggal Kirim:</td><td><?= $data['tanggal_kirim'] ?></td></tr>
                    <tr><td>Tanggal Sampai:</td><td><?= $data['tanggal_sampai'] ?? 'Belum sampai' ?></td></tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            <span class="badge <?php
                                echo match($data['status_pengiriman']) {
                                    'Terkirim' => 'bg-success',
                                    'Dalam Perjalanan' => 'bg-primary',
                                    default => 'bg-warning'
                                };
                            ?>"><?= htmlspecialchars($data['status_pengiriman']) ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-user me-2"></i>Informasi Kurir
                        </h5>
                        <table class="table">
                            <tr><td>Nama Kurir:</td><td><?= htmlspecialchars($data['nama_kurir']) ?></td></tr>
                            <tr><td>No. Telepon:</td><td><?= htmlspecialchars($data['kurir_telepon']) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-user me-2"></i>Informasi Pengirim
                        </h5>
                        <table class="table">
                            <tr><td>Nama:</td><td><?= htmlspecialchars($data['nama_pelanggan']) ?></td></tr>
                            <tr><td>Alamat:</td><td><?= htmlspecialchars($data['alamat']) ?></td></tr>
                            <tr><td>No. Telepon:</td><td><?= htmlspecialchars($data['pelanggan_telepon']) ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="list.php" class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left me-2"></i>Kembali ke List Pengiriman
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
