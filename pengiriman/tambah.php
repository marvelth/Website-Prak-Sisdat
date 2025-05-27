<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

if (isset($_POST['submit'])) {
    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $id_kurir = mysqli_real_escape_string($conn, $_POST['id_kurir']);
    $tanggal_kirim = date('Y-m-d');
    $status_pengiriman = 'Menunggu Kurir';

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
if ($is_kantor_pusat) {
    $query_pesanan = "SELECT p.id_pesanan, pl.nama_pelanggan, kc.nama_cabang 
                      FROM pesanan p 
                      JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      JOIN kantor_cabang kc ON pl.id_cabang = kc.id_cabang
                      LEFT JOIN pengiriman pg ON p.id_pesanan = pg.id_pesanan
                      WHERE pg.id_pengiriman IS NULL
                      ORDER BY kc.nama_cabang, p.id_pesanan";
} else {
    $query_pesanan = "SELECT p.id_pesanan, pl.nama_pelanggan 
                      FROM pesanan p 
                      JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
                      LEFT JOIN pengiriman pg ON p.id_pesanan = pg.id_pesanan
                      WHERE pg.id_pengiriman IS NULL 
                      AND pl.id_cabang = ?";
    $stmt = mysqli_prepare($conn, $query_pesanan);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result_pesanan = mysqli_stmt_get_result($stmt);
}

// Get active couriers based on branch
if ($is_kantor_pusat) {
    $query_kurir = "SELECT k.id_kurir, k.nama_kurir, kc.nama_cabang 
                    FROM kurir k
                    JOIN kantor_cabang kc ON k.id_cabang = kc.id_cabang 
                    WHERE k.status_keaktifan = 'Aktif'
                    ORDER BY kc.nama_cabang, k.nama_kurir";
    $result_kurir = mysqli_query($conn, $query_kurir);
} else {
    $query_kurir = "SELECT id_kurir, nama_kurir 
                    FROM kurir 
                    WHERE status_keaktifan = 'Aktif' 
                    AND id_cabang = ?";
    $stmt = mysqli_prepare($conn, $query_kurir);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['id_cabang']);
    mysqli_stmt_execute($stmt);
    $result_kurir = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengiriman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../index.php"><i class="fa fa-truck"></i> Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-plus"></i> Tambah Pengiriman</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-shopping-cart"></i> Pesanan:</label>
                        <?php if ($is_kantor_pusat) { ?>
                            <select name="id_pesanan" class="form-select" required>
                                <option value="">Pilih Pesanan</option>
                                <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan)) { ?>
                                    <option value="<?= $pesanan['id_pesanan'] ?>">
                                        <?= htmlspecialchars($pesanan['id_pesanan'] . ' - ' . $pesanan['nama_pelanggan'] . 
                                        ' (' . $pesanan['nama_cabang'] . ')') ?>
                                    </option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <select name="id_pesanan" class="form-select" required>
                                <option value="">Pilih Pesanan</option>
                                <?php while ($pesanan = mysqli_fetch_assoc($result_pesanan)) { ?>
                                    <option value="<?= $pesanan['id_pesanan'] ?>">
                                        <?= htmlspecialchars($pesanan['id_pesanan'] . ' - ' . $pesanan['nama_pelanggan']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Kurir:</label>
                        <select name="id_kurir" class="form-select" required>
                            <option value="">Pilih Kurir</option>
                            <?php while ($kurir = mysqli_fetch_assoc($result_kurir)) { ?>
                                <option value="<?= $kurir['id_kurir'] ?>">
                                    <?= htmlspecialchars($kurir['nama_kurir']) ?>
                                </option>
                            <?php } ?>
                        </select>
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
