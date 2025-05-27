<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

if (isset($_POST['submit'])) {
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    $status_barang = 'Diproses'; // Status awal
    $tanggal_pemesanan = date('Y-m-d');

    $query = "INSERT INTO pesanan (id_pelanggan, nama_barang, berat, status_barang, tanggal_pemesanan) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssdss", $id_pelanggan, $nama_barang, $berat, $status_barang, $tanggal_pemesanan);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Pesanan berhasil ditambahkan";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menambahkan pesanan: " . mysqli_error($conn);
        }
    }
}

// Get customers for dropdown based on branch
if ($is_kantor_pusat) {
    $query_pelanggan = "SELECT p.id_pelanggan, p.nama_pelanggan, kc.nama_cabang 
                        FROM pelanggan p
                        JOIN kantor_cabang kc ON p.id_cabang = kc.id_cabang 
                        ORDER BY kc.nama_cabang, p.nama_pelanggan";
    $result_pelanggan = mysqli_query($conn, $query_pelanggan);
} else {
    $query_pelanggan = "SELECT id_pelanggan, nama_pelanggan 
                        FROM pelanggan 
                        WHERE id_cabang = ? 
                        ORDER BY nama_pelanggan";
    $stmt = mysqli_prepare($conn, $query_pelanggan);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result_pelanggan = mysqli_stmt_get_result($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pesanan</title>
    <!--Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php"><i class="fa fa-truck"></i> Padjadjaran Express</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['id_cabang']) ?></span>
                <a class="nav-item nav-link" href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-plus"></i> Tambah Pesanan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Pelanggan:</label>
                        <select name="id_pelanggan" class="form-select" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php while ($pelanggan = mysqli_fetch_assoc($result_pelanggan)) { ?>
                                <option value="<?= $pelanggan['id_pelanggan'] ?>">
                                    <?= htmlspecialchars($pelanggan['nama_pelanggan']) ?>
                                    <?= $is_kantor_pusat ? ' (' . htmlspecialchars($pelanggan['nama_cabang']) . ')' : '' ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-box"></i> Nama Barang:</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-weight-scale"></i> Berat (kg):</label>
                        <input type="number" step="0.1" name="berat" class="form-control" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa fa-floppy-disk"></i> Simpan
                    </button>
                    <a href="list.php" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
