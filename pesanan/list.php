<?php
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">Padjadjaran Express</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['id_cabang']) ?></span>
                <a class="nav-item nav-link" href="../logout.php"> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Data Pesanan</h2>
            <a href="tambah.php" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Pesanan</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th><i class="fa fa-hashtag"></i> ID Pesanan</th>
                        <th><i class="fa fa-user"></i> Pelanggan</th>
                        <?php if ($is_kantor_pusat): ?>
                        <th><i class="fa fa-building"></i> Cabang</th>
                        <?php endif; ?>
                        <th><i class="fa fa-box"></i> Nama Barang</th>
                        <th><i class="fa fa-weight-scale"></i> Berat (kg)</th>
                        <th><i class="fa fa-info-circle"></i> Status</th>
                        <th><i class="fa fa-calendar"></i> Tanggal Pemesanan</th>
                        <th><i class="fa fa-gears"></i> Aksi</th>
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
        
        <a href="../dashboard.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
