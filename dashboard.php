<?php include("config.php");

$sql_total_pelanggan = "SELECT COUNT(*) AS total FROM pelanggan";
$sql_total_pesanan = "SELECT COUNT(*) AS total FROM pesanan WHERE DATE(tanggal_pemesanan) = CURDATE()";
$sql_total_pengirimanaktif = "SELECT COUNT(*) AS total FROM pengiriman WHERE status_pengiriman = 'Dalam Perjalanan'";

$query_total_pelanggan = mysqli_query($conn, $sql_total_pelanggan);
$query_total_pesanan = mysqli_query($conn, $sql_total_pesanan);
$query_total_pengirimanaktif = mysqli_query($conn, $sql_total_pengirimanaktif);

// Mengambil data untuk ditampilkan di ringkasan data
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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Padjadjaran Express</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
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
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">Ringkasan Data</h2>
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