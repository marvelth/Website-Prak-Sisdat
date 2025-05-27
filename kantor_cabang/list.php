<?php
session_start();
include("../config.php");

if (!isset($_SESSION['id_cabang']) || $_SESSION['id_cabang'] !== 'KC001') {
    header("Location: ../index.php");
    exit();
}

$query = "SELECT * FROM kantor_cabang ORDER BY nama_cabang";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kantor Cabang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php"><i class="fa fa-building"></i> Padjadjaran Express</a>
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
            <h2><i class="fa fa-list"></i> Data Kantor Cabang</h2>
            <a href="tambah.php" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Cabang</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><i class="fa fa-id-card"></i> ID Cabang</th>
                        <th><i class="fa fa-building"></i> Nama Cabang</th>
                        <th><i class="fa fa-map-marker"></i> Alamat</th>
                        <th><i class="fa fa-gears"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_cabang']) ?></td>
                        <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id_cabang'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <a href="hapus.php?id=<?= $row['id_cabang'] ?>" class="btn btn-danger btn-sm" 
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
