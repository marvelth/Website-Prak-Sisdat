<?php
session_start();
include("../config.php");
$result = mysqli_query($conn, "SELECT id_kendaraan, nama_kendaraan, jenis_kendaraan, kc.nama_cabang
        FROM kendaraan kn
        JOIN kantor_cabang kc
        WHERE kn.id_cabang = kc.id_cabang");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kendaraan</title>
    <!-- Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Padjadjaran Express</a>
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
            <h2>Data Pelanggan</h2>
            <a href="tambah.php" class="btn btn-primary">Tambah Pelanggan</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th>ID Kendaraan</th>
                    <th>Nama Kendaraan</th>
                    <th>Jenis Kendaraan</th>
                    <th>Kapasitas</th>
                    <th>Pemilik Kendaraan</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id_pelanggan'] ?></td>
                    <td><?= $row['nama_pelanggan'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['telepon'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <a href="../index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
