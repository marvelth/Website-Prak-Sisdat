<?php
session_start();
include("../config.php");

// Get selected branch filter
$id_cabang_filter = isset($_GET['id_cabang']) ? $_GET['id_cabang'] : '';

// Query for branch dropdown
$query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
$result_cabang = mysqli_query($conn, $query_cabang);

// Base query for couriers - sesuaikan dengan struktur tabel
$query = "SELECT k.id_kurir, k.nama_kurir, k.no_telepon, k.status_keaktifan, 
          kc.nama_cabang, kd.nama_kendaraan, kd.jenis_kendaraan
          FROM kurir k 
          LEFT JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang
          LEFT JOIN kendaraan kd ON k.id_kendaraan = kd.id_kendaraan";

// Add filter if branch is selected
if (!empty($id_cabang_filter)) {
    $query .= " WHERE k.id_cabang = " . mysqli_real_escape_string($conn, $id_cabang_filter);
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kurir</title>
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
            <h2>Data Kurir</h2>
            <div class="d-flex gap-2">
                <form method="get" class="d-flex align-items-center gap-2">
                    <select name="id_cabang" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        <?php while ($cabang = mysqli_fetch_assoc($result_cabang)) { 
                            $selected = ($id_cabang_filter == $cabang['id_cabang']) ? 'selected' : '';
                        ?>
                            <option value="<?= $cabang['id_cabang'] ?>" <?= $selected ?>><?= htmlspecialchars($cabang['nama_cabang']) ?></option>
                        <?php } ?>
                    </select>
                </form>
                <a href="tambah.php" class="btn btn-primary">Tambah Kurir</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID Kurir</th>
                        <th>Nama</th>
                        <th>No. Telepon</th>
                        <th>Kantor Cabang</th>
                        <th>Kendaraan</th>
                        <th>Status Keaktifan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id_kurir'] ?></td>
                        <td><?= htmlspecialchars($row['nama_kurir']) ?></td>
                        <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                        <td><?= htmlspecialchars($row['nama_cabang']) ?></td>
                        <td><?= $row['nama_kendaraan'] ? htmlspecialchars($row['nama_kendaraan'] . ' (' . $row['jenis_kendaraan'] . ')') : 'Belum ditugaskan' ?></td>
                        <td><?= htmlspecialchars($row['status_keaktifan']) ?></td>
                        <td>
                            <a href="detail.php?id=<?= $row['id_kurir'] ?>" class="btn btn-info btn-sm">Detail</a>
                            <a href="edit.php?id=<?= $row['id_kurir'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= $row['id_kurir'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
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
