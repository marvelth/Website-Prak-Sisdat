<?php   
  include("config.php");  
  session_start();  

  if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {  
    // Clear any existing session data  
    session_unset();  
    session_destroy();  
    // Redirect to login page  
    header("Location: "); // Assuming 'login.php' is the login page  
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">  
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">  
    <style>  
        body {  
            font-family: 'Inter', sans-serif;  
            background-color: #f0f2f5; /* Light gray background */  
        }  
        .navbar {  
            border-bottom-left-radius: 15px; /* Rounded bottom corners */  
            border-bottom-right-radius: 15px;  
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Subtle shadow */  
        }  
        .card {  
            border-radius: 15px; /* More rounded corners for cards */  
            box-shadow: 0 6px 12px rgba(0,0,0,0.15); /* Stronger shadow for cards */  
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */  
        }  
        .card:hover {  
            transform: translateY(-5px); /* Lift card on hover */  
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);  
        }  
        .card-title {  
            font-weight: 600; /* Semi-bold card titles */  
            margin-bottom: 1rem;  
        }  
        .card-text.fs-2 {  
            font-weight: 700; /* Bold numbers */  
            font-size: 2.5rem !important; /* Slightly larger numbers */  
        }  
        .navbar-brand, .nav-link {  
            font-weight: 500; /* Medium weight for navigation links */  
        }  
        .navbar-nav .nav-link {  
            padding-right: 1rem;  
            padding-left: 1rem;  
        }  
        /* Custom colors for better visual appeal */  
        .bg-primary-custom {  
            background-color: #007bff; /* Standard Bootstrap Primary */  
        }  
        .bg-success-custom {  
            background-color: #28a745; /* Standard Bootstrap Success */  
        }  
        .bg-info-custom {  
            background-color: #17a2b8; /* Standard Bootstrap Info */  
        }  
        .bg-danger-custom {  
            background-color: #dc3545; /* Standard Bootstrap Danger */  
        }  
        /* Adjusting existing Bootstrap colors for consistency */  
        .bg-primary { background-color: #007bff !important; }  
        .bg-success { background-color: #28a745 !important; }  
        .bg-info { background-color: #17a2b8 !important; }  
        .bg-danger { background-color: #dc3545 !important; }  

    </style>  
</head>  
<body>  
    <nav class="navbar navbar-expand-lg navbar-dark <?= $is_kantor_pusat ? 'bg-danger' : 'bg-primary' ?> py-3 mb-5">  
        <div class="container">  
            <a class="navbar-brand fw-bold" href="#">  
                <?= $is_kantor_pusat ? 'Padjadjaran Express - Kantor Pusat' : 'Padjadjaran Express - Kantor Cabang' ?>  
            </a>  
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">  
                <span class="navbar-toggler-icon"></span>  
            </button>  
            <div class="collapse navbar-collapse" id="navbarNav">  
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">  
                    <?php if ($is_kantor_pusat): ?>  
                    <li class="nav-item">  
                        <a class="nav-link" href="kantor_cabang/list.php"><i class="fa fa-building me-2"></i>Kantor Cabang</a>  
                    </li>  
                    <?php endif; ?>  
                      
                    <li class="nav-item">  
                        <a class="nav-link" href="pelanggan/list.php"><i class="fa fa-users me-2"></i>Pelanggan</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="pesanan/list.php"><i class="fa fa-shopping-cart me-2"></i>Pesanan</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="kurir/list.php"><i class="fa fa-user me-2"></i>Kurir</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="kendaraan/list.php"><i class="fa fa-truck me-2"></i>Kendaraan</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="pengiriman/list.php"><i class="fa fa-box me-2"></i>Pengiriman</a>  
                    </li>  
                </ul>  
                <ul class="navbar-nav ms-auto">  
                    <li class="nav-item">  
                        <span class="nav-link text-white"><i class="fa fa-building me-2"></i><?= htmlspecialchars($_SESSION['id_cabang']) ?></span>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>  
                    </li>  
                </ul>  
            </div>  
        </div>  
    </nav>  

    <div class="container py-5">  
        <div class="row mb-5">  
            <div class="col-12 text-center">  
                <h2 class="display-4 fw-bold text-dark"><?= $is_kantor_pusat ? 'Dashboard Kantor Pusat' : 'Dashboard Kantor Cabang' ?></h2>  
                <p class="lead text-muted">Selamat datang di sistem manajemen Padjadjaran Express.</p>  
            </div>  
        </div>  

        <div class="row justify-content-center">  
            <div class="col-md-4 mb-4">  
                <div class="card bg-primary text-white p-4">  
                    <div class="card-body text-center">  
                        <h5 class="card-title">Total Pelanggan</h5>  
                        <p class="card-text fs-1"><?php echo $pelanggan; ?></p>  
                    </div>  
                </div>  
            </div>  
            <div class="col-md-4 mb-4">  
                <div class="card bg-success text-white p-4">  
                    <div class="card-body text-center">  
                        <h5 class="card-title">Pesanan Hari Ini</h5>  
                        <p class="card-text fs-1"><?php echo $pesanan_hari_ini; ?></p>  
                    </div>  
                </div>  
            </div>  
            <div class="col-md-4 mb-4">  
                <div class="card bg-info text-white p-4">  
                    <div class="card-body text-center">  
                        <h5 class="card-title">Pengiriman Aktif</h5>  
                        <p class="card-text fs-1"><?php echo $pengiriman_aktif; ?></p>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>
