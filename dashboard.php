<?php 
include("config.php");
session_start();

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: index.php");
    exit();
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

$sql_total_kurir = $is_kantor_pusat ? 
    "SELECT COUNT(*) AS total FROM kurir WHERE status_keaktifan = 1" :
    "SELECT COUNT(*) AS total FROM kurir WHERE id_cabang = ? AND status_keaktifan = 1";

// Eksekusi query dengan parameter untuk kantor cabang
if ($is_kantor_pusat) {
    $query_total_pelanggan = mysqli_query($conn, $sql_total_pelanggan);
    $query_total_pesanan = mysqli_query($conn, $sql_total_pesanan);
    $query_total_pengirimanaktif = mysqli_query($conn, $sql_total_pengirimanaktif);
    $query_total_kurir = mysqli_query($conn, $sql_total_kurir);
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

    $stmt = mysqli_prepare($conn, $sql_total_kurir);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $query_total_kurir = mysqli_stmt_get_result($stmt);
}

//Query untuk menampilkan nama sesuai cabang
$sql_nama_kantor = "SELECT nama_cabang FROM kantor_cabang WHERE id_cabang = ?";
$stmt = mysqli_prepare($conn, $sql_nama_kantor);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
mysqli_stmt_execute($stmt);
$result_nama_kantor = mysqli_stmt_get_result($stmt);
$nama_kantor = mysqli_fetch_assoc($result_nama_kantor)['nama_cabang'];

$pelanggan = mysqli_fetch_assoc($query_total_pelanggan)['total'];
$pesanan_hari_ini = mysqli_fetch_assoc($query_total_pesanan)['total'];
$pengiriman_aktif = mysqli_fetch_assoc($query_total_pengirimanaktif)['total'];
$kurir_aktif = mysqli_fetch_assoc($query_total_kurir)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Padjadjaran Express</title>
    <!--Bootstrap-->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
    <!--CSS-->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/img/logo.png" alt="Padjadjaran Express" height="30" class="me-2">
                <span>Padjadjaran Express</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($is_kantor_pusat): ?>
                    <li class="nav-item">
                        <a class="nav-link rounded px-3" href="kantor_cabang/list.php">
                            <i class="fa fa-building me-2"></i>Kantor Cabang
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link rounded px-3" href="pelanggan/list.php">
                            <i class="fa fa-users me-2"></i>Pelanggan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded px-3" href="pesanan/list.php">
                            <i class="fa fa-shopping-cart me-2"></i>Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded px-3" href="kurir/list.php">
                            <i class="fa fa-user me-2"></i>Kurir
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded px-3" href="kendaraan/list.php">
                            <i class="fa fa-truck me-2"></i>Kendaraan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded px-3" href="pengiriman/list.php">
                            <i class="fa fa-box me-2"></i>Pengiriman
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fa fa-building me-2"></i><?= htmlspecialchars($_SESSION['id_cabang']) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger btn-sm text-white px-3" href="logout.php">
                            <i class="fa fa-sign-out me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="border-bottom pb-2">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                    <?= $is_kantor_pusat ? 'Kantor Pusat' : $nama_kantor ?>
                </h2>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient card-stats-primary">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title text-white-50">Total Pelanggan</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $pelanggan; ?></h2>
                            </div>
                            <div class="rounded-circle bg-white p-3">
                                <i class="fas fa-users fa-2x" style="color: #827397;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient card-stats-primary">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title text-white-50">Total Kurir</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $kurir_aktif; ?></h2>
                            </div>
                            <div class="rounded-circle bg-white p-3">
                                <i class="fas fa-user fa-2x" style="color: #827397;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient card-stats-secondary">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title text-white-50">Pesanan Hari Ini</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $pesanan_hari_ini; ?></h2>
                            </div>
                            <div class="rounded-circle bg-white p-3">
                                <i class="fas fa-shopping-cart fa-2x" style="color: #827397;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 bg-gradient card-stats-accent">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title text-white-50">Pengiriman Aktif</h6>
                                <h2 class="display-6 fw-bold mb-0"><?php echo $pengiriman_aktif; ?></h2>
                            </div>
                            <div class="rounded-circle bg-white p-3">
                                <i class="fas fa-truck fa-2x" style="color: #363062;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>