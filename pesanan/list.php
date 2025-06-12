<?php
session_start();

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}

include("../config.php");

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

// Base query with branch info
if ($is_kantor_pusat) {
    $query = "SELECT p.*, pl.nama_pelanggan, kc.nama_cabang
              FROM pesanan p 
              JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
              JOIN kantor_cabang kc ON pl.id_cabang = kc.id_cabang
              ORDER BY p.tanggal_pemesanan DESC";
    $result = mysqli_query($conn, $query);
} else {
    $query = "SELECT p.*, pl.nama_pelanggan 
              FROM pesanan p 
              JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
              WHERE pl.id_cabang = ?
              ORDER BY p.tanggal_pemesanan DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    //Query untuk menampilkan nama sesuai cabang
    $sql_nama_kantor = "SELECT nama_cabang FROM kantor_cabang WHERE id_cabang = ?";
    $stmt = mysqli_prepare($conn, $sql_nama_kantor);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result_nama_kantor = mysqli_stmt_get_result($stmt);
    $nama_kantor = mysqli_fetch_assoc($result_nama_kantor)['nama_cabang'];
}

// Check for query errors
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan</title>
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
                <img src="../assets/img/logo.png" alt="Padjadjaran Express" height="60" class="me-2">
                <span>Padjadjaran Express</span>
            </a>
            <div class="navbar-nav ms-auto">
                <span class="nav-link">
                    <i class="fa fa-building me-2"></i><?= htmlspecialchars($_SESSION['id_cabang']) ?>
                </span>
                <a class="nav-link btn btn-danger btn-sm text-white px-3" href="../logout.php">
                    <i class="fa fa-sign-out me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="fa fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col">
                <h2 class="border-bottom pb-2">
                    <i class="fas fa-shopping-cart me-2"></i>Data Pesanan
                    <?= $is_kantor_pusat ? '' : $nama_kantor ?>
                </h2>
            </div>
            <div class="col text-end">
                <a href="tambah.php" class="btn btn-primary shadow-sm">
                    <i class="fa fa-plus me-2"></i>Tambah Pesanan
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-hashtag me-2"></i>ID Pesanan</th>
                                <th><i class="fa fa-user me-2"></i>Pelanggan</th>
                                <?php if ($is_kantor_pusat): ?>
                                <th><i class="fa fa-building me-2"></i>Cabang</th>
                                <?php endif; ?>
                                <th><i class="fa fa-box me-2"></i>Nama Barang</th>
                                <th><i class="fa fa-weight-scale me-2"></i>Berat (kg)</th>
                                <th><i class="fa fa-info-circle me-2"></i>Status</th>
                                <th><i class="fa fa-calendar me-2"></i>Tanggal Pemesanan</th>
                                <th><i class="fa fa-gears me-2"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['id_pesanan'] ?></td>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <?php if ($is_kantor_pusat): ?>
                                <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= $row['berat'] ?></td>
                                <td><?= htmlspecialchars($row['status_barang']) ?></td>
                                <td><?= $row['tanggal_pemesanan'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                    <a href="hapus.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fa fa-trash-o fa-fw"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="../dashboard.php" class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
