<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {
    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $id_kurir = mysqli_real_escape_string($conn, $_POST['id_kurir']);
    $tanggal_kirim = date('Y-m-d');
    $status_pengiriman = 'Menunggu Kurir';  // Status awal sesuai tabel

    $query = "INSERT INTO pengiriman (id_pesanan, id_kurir, tanggal_kirim, status_pengiriman) 
              VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $id_pesanan, $id_kurir, $tanggal_kirim, $status_pengiriman);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data pengiriman berhasil ditambahkan";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

// Get pending orders (without delivery)
$query_pesanan = "SELECT p.id_pesanan, pl.nama_pelanggan 
                  FROM pesanan p 
                  JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                  LEFT JOIN pengiriman pg ON p.id_pesanan = pg.id_pesanan
                  WHERE pg.id_pengiriman IS NULL";
$result_pesanan = mysqli_query($conn, $query_pesanan);

// Get active couriers
$query_kurir = "SELECT id_kurir, nama_kurir FROM kurir WHERE status_keaktifan = 'Aktif'";
$result_kurir = mysqli_query($conn, $query_kurir);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengiriman</title>
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
                <h2 class="card-title">Tambah Pengiriman</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Pesanan:</label>
                        <select name="id_pesanan" class="form-select" required>
                            <option value="">Pilih Pesanan</option>
                            <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan)) { ?>
                                <option value="<?= $pesanan['id_pesanan'] ?>">
                                    <?= htmlspecialchars($pesanan['id_pesanan'] . ' - ' . $pesanan['nama_pelanggan']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kurir:</label>
                        <select name="id_kurir" class="form-select" required>
                            <option value="">Pilih Kurir</option>
                            <?php while ($kurir = mysqli_fetch_assoc($result_kurir)) { ?>
                                <option value="<?= $kurir['id_kurir'] ?>">
                                    <?= htmlspecialchars($kurir['nama_kurir']) ?>
                                </option>
                            <?php } ?>
                        </select>
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
