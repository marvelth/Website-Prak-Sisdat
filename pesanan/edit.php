<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);

// Get order data
$query = "SELECT * FROM pesanan WHERE id_pesanan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_pesanan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pesanan = mysqli_fetch_assoc($result);

if (!$pesanan) {
    $_SESSION['error'] = "Pesanan tidak ditemukan";
    header("Location: list.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    $status_barang = mysqli_real_escape_string($conn, $_POST['status_barang']);

    $query = "UPDATE pesanan SET nama_barang = ?, berat = ?, status_barang = ? WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sdss", $nama_barang, $berat, $status_barang, $id_pesanan);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Pesanan berhasil diupdate";
        header("Location: list.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate pesanan: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan</title>
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
                <img src="../assets/img/logo.png" alt="Padjadjaran Express" height="60" class="me-2">
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
                    <i class="fas fa-edit me-2"></i>Edit Pesanan
                </h2>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-box me-2"></i>Nama Barang:
                        </label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($pesanan['nama_barang']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-weight-scale me-2"></i>Berat (kg):
                        </label>
                        <input type="number" step="0.1" name="berat" class="form-control" value="<?= $pesanan['berat'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-info-circle me-2"></i>Status:
                        </label>
                        <select name="status_barang" class="form-select" required>
                            <option value="Diproses" <?= $pesanan['status_barang'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Dikirim" <?= $pesanan['status_barang'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= $pesanan['status_barang'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="submit" class="btn btn-primary shadow-sm">
                            <i class="fa fa-save me-2"></i>Update
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
