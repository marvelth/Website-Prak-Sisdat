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

    //Query untuk menampilkan nama sesuai cabang
    $sql_nama_kantor = "SELECT nama_cabang FROM kantor_cabang WHERE id_cabang = ?";
    $stmt = mysqli_prepare($conn, $sql_nama_kantor);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result_nama_kantor = mysqli_stmt_get_result($stmt);
    $nama_kantor = mysqli_fetch_assoc($result_nama_kantor)['nama_cabang'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kendaraan</title>
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
                    <i class="fas fa-truck me-2"></i>Data Kendaraan
                    <?= $is_kantor_pusat ? '' : $nama_kantor ?>
                </h2>
            </div>
            <div class="col text-end">
                <a href="tambah.php" class="btn btn-primary shadow-sm">
                    <i class="fa fa-plus me-2"></i>Tambah Kendaraan
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-id-card me-2"></i>ID Kendaraan</th>
                                <th><i class="fa fa-truck me-2"></i>Nama Kendaraan</th>
                                <th><i class="fa fa-tags me-2"></i>Jenis Kendaraan</th>
                                <th><i class="fa fa-weight-scale me-2"></i>Kapasitas</th>
                                <?php if ($is_kantor_pusat): ?>
                                <th><i class="fa fa-building me-2"></i>Kantor Cabang</th>
                                <?php endif; ?>
                                <th><i class="fa fa-gears me-2"></i>Aksi</th>
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
