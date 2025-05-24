<?php
include '../config.php';
session_start();

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

// Get customers for dropdown
$query_pelanggan = "SELECT id_pelanggan, nama_pelanggan FROM pelanggan ORDER BY nama_pelanggan";
$result_pelanggan = mysqli_query($conn, $query_pelanggan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Tambah Pesanan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Pelanggan:</label>
                        <select name="id_pelanggan" class="form-select" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php while ($pelanggan = mysqli_fetch_assoc($result_pelanggan)) { ?>
                                <option value="<?= $pelanggan['id_pelanggan'] ?>">
                                    <?= htmlspecialchars($pelanggan['nama_pelanggan']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Barang:</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Berat (kg):</label>
                        <input type="number" step="0.1" name="berat" class="form-control" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    <a href="list.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
