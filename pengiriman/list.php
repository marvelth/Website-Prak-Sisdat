<?php
session_start();

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    header("Location: ../index.php");
    exit();
}

include("../config.php");

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Base query for deliveries with related info
$query = "SELECT p.*, ps.id_pesanan, k.nama_kurir, kc.nama_cabang
          FROM pengiriman p 
          JOIN pesanan ps ON p.id_pesanan = ps.id_pesanan
          JOIN kurir k ON p.id_kurir = k.id_kurir
          JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang";

// Add WHERE clause based on user role and filters
$where_conditions = [];

if (!$is_kantor_pusat) {
    $where_conditions[] = "k.id_cabang = '" . mysqli_real_escape_string($conn, $_SESSION['id_cabang']) . "'";
}

if (!empty($status_filter)) {
    $where_conditions[] = "p.status_pengiriman = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">Padjadjaran Express</a>
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
            <h2><i class="fa fa-truck"></i> Data Pengiriman</h2>
            <div class="d-flex gap-2">
                <form method="get" class="d-flex align-items-center gap-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Kurir" <?= $status_filter == 'Menunggu Kurir' ? 'selected' : '' ?>>
                            <i class="fa fa-clock"></i> Menunggu Kurir
                        </option>
                        <option value="Dalam Perjalanan" <?= $status_filter == 'Dalam Perjalanan' ? 'selected' : '' ?>>
                            <i class="fa fa-truck-fast"></i> Dalam Perjalanan
                        </option>
                        <option value="Terkirim" <?= $status_filter == 'Terkirim' ? 'selected' : '' ?>>
                            <i class="fa fa-check-circle"></i> Terkirim
                        </option>
                    </select>
                </form>
                <a href="tambah.php" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Pengiriman</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th><i class="fa fa-hashtag"></i> ID Pengiriman</th>
                        <th><i class="fa fa-shopping-cart"></i> ID Pesanan</th>
                        <th><i class="fa fa-user"></i> Kurir</th>
                        <?php if ($is_kantor_pusat): ?>
                        <th><i class="fa fa-building"></i> Cabang</th>
                        <?php endif; ?>
                        <th><i class="fa fa-calendar"></i> Tanggal Kirim</th>
                        <th><i class="fa fa-calendar-check"></i> Tanggal Sampai</th>
                        <th><i class="fa fa-info-circle"></i> Status</th>
                        <th><i class="fa fa-gears"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id_pengiriman'] ?></td>
                        <td><?= $row['id_pesanan'] ?></td>
                        <td><?= htmlspecialchars($row['nama_kurir']) ?></td>
                        <?php if ($is_kantor_pusat): ?>
                        <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                        <?php endif; ?>
                        <td><?= $row['tanggal_kirim'] ?></td>
                        <td><?= $row['tanggal_sampai'] ?? 'Belum sampai' ?></td>
                        <td><?= htmlspecialchars($row['status_pengiriman']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id_pengiriman'] ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-info-circle"></i> Detail
                            </a>
                            <a href="edit.php?id=<?= $row['id_pengiriman'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa fa-pencil"></i> Edit
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