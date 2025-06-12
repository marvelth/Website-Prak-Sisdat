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

// Get courier data with vehicle and branch info
$query = "SELECT k.*, kc.nama_cabang, kd.nama_kendaraan, kd.jenis_kendaraan, kd.kapasitas
          FROM kurir k 
          LEFT JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
          LEFT JOIN kendaraan kd ON k.id_kendaraan = kd.id_kendaraan 
          WHERE k.id_kurir = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kurir);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kurir = mysqli_fetch_assoc($result);

// Untuk mendapatkan data kendaraan yang dipakai kurir
$query_kendaraan = "SELECT * FROM kendaraan WHERE id_kendaraan = ?";
$stmt_kendaraan = mysqli_prepare($conn, $query_kendaraan);
if ($kurir['id_kendaraan']) {
    mysqli_stmt_bind_param($stmt_kendaraan, "s", $kurir['id_kendaraan']);
    mysqli_stmt_execute($stmt_kendaraan);
    $kendaraan = mysqli_stmt_get_result($stmt_kendaraan);
} else {
    $kendaraan = false;
}

//Untuk mendapatkan data pengiriman yang aktif
$query_pengiriman = "SELECT p.id_pengiriman, p.id_pesanan, p.tanggal_kirim, 
                            p.tanggal_sampai, p.status_pengiriman 
                     FROM pengiriman p 
                     WHERE p.id_kurir = ? 
                     ORDER BY p.tanggal_kirim DESC";
$stmt_pengiriman = mysqli_prepare($conn, $query_pengiriman);
mysqli_stmt_bind_param($stmt_pengiriman, "s", $id_kurir);
mysqli_stmt_execute($stmt_pengiriman);
$pengiriman = mysqli_stmt_get_result($stmt_pengiriman);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Kurir</title>
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
                    <i class="fas fa-user me-2"></i>Detail Kurir
                </h2>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-info-circle me-2"></i>Informasi Kurir
                </h5>
                <table class="table">
                    <tr>
                        <td width="200"><i class="fas fa-user me-2"></i>Nama:</td>
                        <td><?= htmlspecialchars($kurir['nama_kurir']) ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-phone me-2"></i>Telepon:</td>
                        <td><?= htmlspecialchars($kurir['no_telepon']) ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-building me-2"></i>Kantor Cabang:</td>
                        <td><?= htmlspecialchars($kurir['nama_cabang']) ?></td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-toggle-on me-2"></i>Status:</td>
                        <td><?= htmlspecialchars($kurir['status_keaktifan']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-truck me-2"></i>Kendaraan yang Digunakan
                </h5>
                <?php if ($kurir['nama_kendaraan']): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-car me-2"></i>Nama Kendaraan</th>
                                <th><i class="fas fa-tag me-2"></i>Jenis Kendaraan</th>
                                <th><i class="fas fa-weight me-2"></i>Kapasitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= htmlspecialchars($kurir['nama_kendaraan']) ?></td>
                                <td><?= htmlspecialchars($kurir['jenis_kendaraan']) ?></td>
                                <td><?= htmlspecialchars($kurir['kapasitas']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted"><i class="fas fa-info-circle me-2"></i>Tidak ada kendaraan yang ditugaskan</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-shipping-fast me-2"></i>Pengiriman yang Ditangani
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>ID Pengiriman</th>
                                <th><i class="fas fa-box me-2"></i>ID Pesanan</th>
                                <th><i class="fas fa-calendar me-2"></i>Tanggal Kirim</th>
                                <th><i class="fas fa-calendar-check me-2"></i>Tanggal Sampai</th>
                                <th><i class="fas fa-info-circle me-2"></i>Status Pengiriman</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = mysqli_fetch_assoc($pengiriman)) { ?>
                            <tr>
                                <td><?= $p['id_pengiriman'] ?></td>
                                <td><?= $p['id_pesanan'] ?></td>
                                <td><?= $p['tanggal_kirim'] ?></td>
                                <td><?= $p['tanggal_sampai'] ?? 'Belum sampai' ?></td>
                                <td><?= htmlspecialchars($p['status_pengiriman']) ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <a href="list.php" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left me-2"></i>Kembali ke List Kurir
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
