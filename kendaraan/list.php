<?php
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
}

include("../config.php");

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

// Query for vehicles
if ($is_kantor_pusat) {
    $query = "SELECT k.*, kc.nama_cabang 
              FROM kendaraan k
              JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
              ORDER BY k.id_kendaraan";
    $result = mysqli_query($conn, $query);
} else {
    $query = "SELECT k.*, kc.nama_cabang 
              FROM kendaraan k
              JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
              WHERE k.id_cabang = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">Padjadjaran Express</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['id_cabang']) ?></span>
                <a class="nav-item nav-link" href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="fa fa-truck"></i> Data Kendaraan</h2>
            <a href="tambah.php" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Kendaraan</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th><i class="fa fa-id-card"></i> ID Kendaraan</th>
                        <th><i class="fa fa-truck"></i> Nama Kendaraan</th>
                        <th><i class="fa fa-tags"></i> Jenis Kendaraan</th>
                        <th><i class="fa fa-weight-scale"></i> Kapasitas</th>
                        <?php if ($is_kantor_pusat): ?>
                        <th><i class="fa fa-building"></i> Kantor Cabang</th>
                        <?php endif; ?>
                        <th><i class="fa fa-gears"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_kendaraan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kendaraan']) ?></td>
                        <td><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                        <td><?= htmlspecialchars($row['kapasitas']) ?></td>
                        <?php if ($is_kantor_pusat): ?>
                        <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                        <?php endif; ?>
                        <td>
                            <a href="edit.php?id=<?= $row['id_kendaraan'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <a href="hapus.php?id=<?= $row['id_kendaraan'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Yakin hapus?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
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
