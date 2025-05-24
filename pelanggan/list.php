<?php
session_start();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="..\assets\font-awesome-4.7.0\font-awesome-4.7.0\css\font-awesome.min.css">
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
            <h2>Data Pelanggan</h2>
            <a href="tambah.php" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Pelanggan</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['id_pelanggan'] ?></td>
                    <td><?= $row['nama_pelanggan'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['telepon'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                        <a href="hapus.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fa fa-trash-o fa-fw"></i> Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <a href="../dashboard.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
