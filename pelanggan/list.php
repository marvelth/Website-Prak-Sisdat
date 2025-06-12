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

// Use prepared statement for non-pusat query
if ($is_kantor_pusat) {
    $result = mysqli_query($conn, "SELECT * FROM pelanggan");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE id_cabang = ?");
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
    <title>Data Pelanggan</title>
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

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="fa fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col">
                <h2 class="border-bottom pb-2">
                    <i class="fa fa-users me-2"></i>Data Pelanggan
                    <?= $is_kantor_pusat ? '' : $nama_kantor ?> 
                </h2>
            </div>
            <div class="col text-end">
                <a href="tambah.php" class="btn btn-primary shadow-sm">
                    <i class="fa fa-plus me-2"></i>Tambah Pelanggan
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-id-card me-2"></i>ID</th>
                                <th><i class="fa fa-user me-2"></i>Nama</th>
                                <th><i class="fa fa-location-dot me-2"></i>Alamat</th>
                                <th><i class="fa fa-phone me-2"></i>No. Telepon</th>
                                <th><i class="fa fa-envelope me-2"></i>Email</th>
                                <th><i class="fa fa-gears me-2"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['id_pelanggan'] ?></td>
                                <td><?= $row['nama_pelanggan'] ?></td>
                                <td><?= $row['alamat'] ?></td>
                                <td><?= $row['telepon'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-pencil me-1"></i>Edit
                                    </a>
                                    <a href="hapus.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                                        <i class="fa fa-trash me-1"></i>Hapus
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
