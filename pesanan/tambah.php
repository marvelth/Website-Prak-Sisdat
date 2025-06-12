<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

if (isset($_POST['submit'])) {
    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    $status_barang = 'Diproses'; // Status awal
    $tanggal_pemesanan = date('Y-m-d');

    $query = "INSERT INTO pesanan (id_pesanan, id_pelanggan, nama_barang, berat, status_barang, tanggal_pemesanan) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssdss", $id_pesanan, $id_pelanggan, $nama_barang, $berat, $status_barang, $tanggal_pemesanan);
        
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
                    <i class="fas fa-plus me-2"></i>Tambah Pesanan
                </h2>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-box me-2"></i>ID Pesanan:
                        </label>
                        <input type="text" name="id_pesanan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-user me-2"></i>Pelanggan:
                        </label>
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
                        <label class="form-label">
                            <i class="fa fa-box me-2"></i>Nama Barang:
                        </label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-weight-scale me-2"></i>Berat (kg):
                        </label>
                        <input type="number" step="0.1" name="berat" class="form-control" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="submit" class="btn btn-primary shadow-sm">
                            <i class="fa fa-save me-2"></i>Simpan
                        </button>
                        <a href="list.php" class="btn btn-secondary shadow-sm">
                            <i class="fa fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
