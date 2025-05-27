<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
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
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-edit"></i> Edit Pesanan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-box"></i> Nama Barang:</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($pesanan['nama_barang']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-weight-scale"></i> Berat (kg):</label>
                        <input type="number" step="0.1" name="berat" class="form-control" value="<?= $pesanan['berat'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-info-circle"></i> Status:</label>
                        <select name="status_barang" class="form-select" required>
                            <option value="Diproses" <?= $pesanan['status_barang'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Dikirim" <?= $pesanan['status_barang'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= $pesanan['status_barang'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa fa-floppy-disk"></i> Update
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
