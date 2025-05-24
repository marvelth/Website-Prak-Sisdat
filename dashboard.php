<?php 
include("config.php");
session_start();

// Cek jenis kantor (pusat/cabang)
if (!isset($_SESSION['id_cabang'])) {
    header("Location: index.php");
    exit;
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

// Query untuk statistik
$sql_total_pelanggan = $is_kantor_pusat ? 
    "SELECT COUNT(*) AS total FROM pelanggan" :
    "SELECT COUNT(*) AS total FROM pelanggan WHERE id_cabang = ?";

$sql_total_pesanan = $is_kantor_pusat ?
    "SELECT COUNT(*) AS total FROM pesanan WHERE DATE(tanggal_pemesanan) = CURDATE()" :
    "SELECT COUNT(*) AS total FROM pesanan p 
     JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
     WHERE pl.id_cabang = ? AND DATE(p.tanggal_pemesanan) = CURDATE()";

$sql_total_pengirimanaktif = $is_kantor_pusat ?
    "SELECT COUNT(*) AS total FROM pengiriman WHERE status_pengiriman = 'Dalam Perjalanan'" :
    "SELECT COUNT(*) AS total FROM pengiriman pg 
     JOIN kurir k ON pg.id_kurir = k.id_kurir 
     WHERE k.id_cabang = ? AND pg.status_pengiriman = 'Dalam Perjalanan'";

// Eksekusi query dengan parameter untuk kantor cabang
if ($is_kantor_pusat) {
    $query_total_pelanggan = mysqli_query($conn, $sql_total_pelanggan);
    $query_total_pesanan = mysqli_query($conn, $sql_total_pesanan);
    $query_total_pengirimanaktif = mysqli_query($conn, $sql_total_pengirimanaktif);
} else {
    $stmt = mysqli_prepare($conn, $sql_total_pelanggan);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $query_total_pelanggan = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($conn, $sql_total_pesanan);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $query_total_pesanan = mysqli_stmt_get_result($stmt);

    $stmt = mysqli_prepare($conn, $sql_total_pengirimanaktif);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $query_total_pengirimanaktif = mysqli_stmt_get_result($stmt);
}

$pelanggan = mysqli_fetch_assoc($query_total_pelanggan)['total'];
$pesanan_hari_ini = mysqli_fetch_assoc($query_total_pesanan)['total'];
$pengiriman_aktif = mysqli_fetch_assoc($query_total_pengirimanaktif)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Padjadjaran Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="..\assets\css\font-awesome.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark <?= $is_kantor_pusat ? 'bg-danger' : 'bg-primary' ?> mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <?= $is_kantor_pusat ? 'Padjadjaran Express - Kantor Pusat' : 'Padjadjaran Express - Kantor Cabang' ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($is_kantor_pusat): ?>
                    <!-- Menu untuk kantor pusat -->
                    <li class="nav-item">
                        <a class="nav-link" href="kantor_cabang/list.php">Kantor Cabang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan/overview.php">Laporan Overview</a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Menu untuk semua kantor -->
                    <li class="nav-item">
                        <a class="nav-link" href="pelanggan/list.php">Pelanggan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pesanan/list.php">Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kurir/list.php">Kurir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pengiriman/list.php">Pengiriman</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2><?= $is_kantor_pusat ? 'Dashboard Kantor Pusat' : 'Dashboard Kantor Cabang' ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Pelanggan</h5>
                        <p class="card-text fs-2"><?php echo $pelanggan; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pesanan Hari Ini</h5>
                        <p class="card-text fs-2"><?php echo $pesanan_hari_ini; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pengiriman Aktif</h5>
                        <p class="card-text fs-2"><?php echo $pengiriman_aktif; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>