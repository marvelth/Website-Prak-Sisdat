<?php
session_start();
include("../config.php");

// Get selected status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Base query for deliveries with related info
$query = "SELECT p.*, ps.id_pesanan, k.nama_kurir
          FROM pengiriman p 
          JOIN pesanan ps ON p.id_pesanan = ps.id_pesanan
          JOIN kurir k ON p.id_kurir = k.id_kurir";

if (!empty($status_filter)) {
    $query .= " WHERE p.status_pengiriman = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

$query .= " ORDER BY p.tanggal_kirim DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Pengiriman</title>
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
            <h2>Data Pengiriman</h2>
            <div class="d-flex gap-2">
                <form method="get" class="d-flex align-items-center gap-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Kurir" <?= $status_filter == 'Menunggu Kurir' ? 'selected' : '' ?>>Menunggu Kurir</option>
                        <option value="Dalam Perjalanan" <?= $status_filter == 'Dalam Perjalanan' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                        <option value="Terkirim" <?= $status_filter == 'Terkirim' ? 'selected' : '' ?>>Terkirim</option>
                    </select>
                </form>
                <a href="tambah.php" class="btn btn-primary">Tambah Pengiriman</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID Pengiriman</th>
                        <th>ID Pesanan</th>
                        <th>Kurir</th>
                        <th>Tanggal Kirim</th>
                        <th>Tanggal Sampai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id_pengiriman'] ?></td>
                        <td><?= $row['id_pesanan'] ?></td>
                        <td><?= htmlspecialchars($row['nama_kurir']) ?></td>
                        <td><?= $row['tanggal_kirim'] ?></td>
                        <td><?= $row['tanggal_sampai'] ?? 'Belum sampai' ?></td>
                        <td><?= htmlspecialchars($row['status_pengiriman']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id_pengiriman'] ?>" class="btn btn-info btn-sm">Detail</a>
                            <a href="edit.php?id=<?= $row['id_pengiriman'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <a href="../index.php" class="btn btn-secondary">Kembali</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>