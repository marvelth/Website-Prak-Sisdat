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
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

//Query untuk menampilkan nama sesuai cabang
$sql_nama_kantor = "SELECT nama_cabang FROM kantor_cabang WHERE id_cabang = ?";
$stmt = mysqli_prepare($conn, $sql_nama_kantor);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
mysqli_stmt_execute($stmt);
$result_nama_kantor = mysqli_stmt_get_result($stmt);
$nama_kantor = mysqli_fetch_assoc($result_nama_kantor)['nama_cabang'];

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
                    <i class="fas fa-shipping-fast me-2"></i>Data Pengiriman
                    <?= $is_kantor_pusat ? '' : $nama_kantor ?>
                </h2>
            </div>
            <div class="col text-end">
                <div class="d-flex justify-content-end gap-2">
                    <form method="get" class="d-flex align-items-center">
                        <select name="status" class="form-select shadow-sm" onchange="this.form.submit()">
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
                    <a href="tambah.php" class="btn btn-primary shadow-sm">
                        <i class="fa fa-plus me-2"></i>Tambah Pengiriman
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-hashtag me-2"></i>ID Pengiriman</th>
                                <th><i class="fa fa-shopping-cart me-2"></i>ID Pesanan</th>
                                <th><i class="fa fa-user me-2"></i>Kurir</th>
                                <?php if ($is_kantor_pusat): ?>
                                <th><i class="fa fa-building me-2"></i>Cabang</th>
                                <?php endif; ?>
                                <th><i class="fa fa-calendar me-2"></i>Tanggal Kirim</th>
                                <th><i class="fa fa-calendar-check me-2"></i>Tanggal Sampai</th>
                                <th><i class="fa fa-info-circle me-2"></i>Status</th>
                                <th><i class="fa fa-gears me-2"></i>Aksi</th>
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
                                <td>
                                    <span class="badge <?php
                                        echo match($row['status_pengiriman']) {
                                            'Terkirim' => 'bg-success',
                                            'Dalam Perjalanan' => 'bg-primary',
                                            default => 'bg-warning'
                                        };
                                    ?>">
                                        <?= htmlspecialchars($row['status_pengiriman']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail.php?id=<?= $row['id_pengiriman'] ?>" class="btn btn-info btn-sm shadow-sm">
                                        <i class="fa fa-info-circle me-1"></i>Detail
                                    </a>
                                    <a href="edit.php?id=<?= $row['id_pengiriman'] ?>" class="btn btn-warning btn-sm shadow-sm">
                                        <i class="fa fa-pencil me-1"></i>Edit
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