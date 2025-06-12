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
$id_cabang_filter = isset($_GET['id_cabang']) ? $_GET['id_cabang'] : '';

if ($is_kantor_pusat) {
    // Query untuk fitur dropdown
    $query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
    $result_cabang = mysqli_query($conn, $query_cabang);

    $query = "SELECT k.id_kurir, k.nama_kurir, k.no_telepon, k.status_keaktifan, 
        kc.nama_cabang, kd.nama_kendaraan, kd.jenis_kendaraan
        FROM kurir k 
        LEFT JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
        LEFT JOIN kendaraan kd ON k.id_kendaraan = kd.id_kendaraan";
    
    if (!empty($id_cabang_filter)) {
        $query = "SELECT k.id_kurir, k.nama_kurir, k.no_telepon, k.status_keaktifan, 
            kc.nama_cabang, kd.nama_kendaraan, kd.jenis_kendaraan
            FROM kurir k 
            LEFT JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
            LEFT JOIN kendaraan kd ON k.id_kendaraan = kd.id_kendaraan
            WHERE k.id_cabang = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $id_cabang_filter);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, $query);
    }
} else {
    // Non-pusat hanya bisa lihat data cabangnya sendiri
    $query = "SELECT k.id_kurir, k.nama_kurir, k.no_telepon, k.status_keaktifan, 
          kc.nama_cabang, kd.nama_kendaraan, kd.jenis_kendaraan
          FROM kurir k 
          LEFT JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
          LEFT JOIN kendaraan kd ON k.id_kendaraan = kd.id_kendaraan
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

// Check for query errors
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kurir</title>
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
                    <i class="fas fa-users me-2"></i>Data Kurir
                    <?= $is_kantor_pusat ? '' : $nama_kantor ?>
                </h2>
            </div>
            <div class="col text-end">
                <div class="d-flex justify-content-end gap-2">
                    <?php if ($is_kantor_pusat): ?>
                        <form method="get" class="d-flex align-items-center">
                            <select name="id_cabang" class="form-select shadow-sm" onchange="this.form.submit()">
                                <option value="">Semua Cabang</option>
                                <?php
                                mysqli_data_seek($result_cabang, 0);
                                while ($cabang = mysqli_fetch_assoc($result_cabang)) {
                                    $selected = ($id_cabang_filter == $cabang['id_cabang']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $cabang['id_cabang'] ?>" <?= $selected ?>><?= htmlspecialchars($cabang['nama_cabang']) ?></option>
                                <?php } ?>
                            </select>
                        </form>
                    <?php endif; ?>
                    <a href="tambah.php" class="btn btn-primary shadow-sm">
                        <i class="fa fa-plus me-2"></i>Tambah Kurir
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
                                <th><i class="fa fa-id-card me-2"></i>ID Kurir</th>
                                <th><i class="fa fa-user me-2"></i>Nama</th>
                                <th><i class="fa fa-phone me-2"></i>No. Telepon</th>
                                <?php if ($is_kantor_pusat): ?>
                                <th><i class="fa fa-building me-2"></i>Kantor Cabang</th>
                                <?php endif; ?>
                                <th><i class="fa fa-truck me-2"></i>Kendaraan</th>
                                <th><i class="fa fa-toggle-on me-2"></i>Status Keaktifan</th>
                                <th><i class="fa fa-gears me-2"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['id_kurir'] ?></td>
                                <td><?= htmlspecialchars($row['nama_kurir']) ?></td>
                                <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                                <?php if ($is_kantor_pusat): ?>
                                <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                                <?php endif; ?>
                                <td><?= $row['nama_kendaraan'] ? htmlspecialchars($row['nama_kendaraan'] . ' (' . $row['jenis_kendaraan'] . ')') : 'Belum ditugaskan' ?></td>
                                <td><?= htmlspecialchars($row['status_keaktifan']) ?></td>
                                <td>
                                    <a href="detail.php?id=<?= $row['id_kurir'] ?>" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Detail</a>
                                    <a href="edit.php?id=<?= $row['id_kurir'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                    <a href="hapus.php?id=<?= $row['id_kurir'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')"><i class="fa fa-trash"></i> Hapus</a>
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
