<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
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
                <h3 class="card-title">Informasi Kurir</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><td width="200">Nama:</td><td><?= htmlspecialchars($kurir['nama_kurir']) ?></td></tr>
                    <tr><td>Telepon:</td><td><?= htmlspecialchars($kurir['no_telepon']) ?></td></tr>
                    <tr><td>Kantor Cabang:</td><td><?= htmlspecialchars($kurir['nama_cabang']) ?></td></tr>
                    <tr><td>Status:</td><td><?= htmlspecialchars($kurir['status_keaktifan']) ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Kendaraan yang Digunakan</h3>
            </div>
            <div class="card-body">
                <?php if ($kurir['nama_kendaraan']): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Kendaraan</th>
                            <th>Jenis Kendaraan</th>
                            <th>Kapasitas</th>
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
                <?php else: ?>
                    <p class="text-muted">Tidak ada kendaraan yang ditugaskan</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Pengiriman yang Ditangani</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID Pengiriman</th>
                                <th>ID Pesanan</th>
                                <th>Tanggal Kirim</th>
                                <th>Tanggal Sampai</th>
                                <th>Status Pengiriman</th>
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

        <a href="list.php" class="btn btn-secondary">Kembali ke List Kurir</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
