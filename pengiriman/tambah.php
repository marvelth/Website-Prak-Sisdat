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
    $id_pengiriman = mysqli_real_escape_string($conn, $_POST['id_pengiriman']);
    $id_pesanan = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $id_kurir = mysqli_real_escape_string($conn, $_POST['id_kurir']);
    $tanggal_kirim = date('Y-m-d');
    $status_pengiriman = 'Dalam Perjalanan'; // Change initial status

    // Validasi kurir tidak sedang mengirim
    $check_kurir = "SELECT id_pengiriman FROM pengiriman WHERE id_kurir = ? AND status_pengiriman = 'Dalam Perjalanan'";
    $stmt_check = mysqli_prepare($conn, $check_kurir);
    mysqli_stmt_bind_param($stmt_check, "s", $id_kurir);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['error'] = "Kurir sedang dalam pengiriman lain";
        header("Location: tambah.php");
        exit;
    }
    mysqli_stmt_close($stmt_check);
    
    $query = "INSERT INTO pengiriman (id_pengiriman, id_pesanan, id_kurir, tanggal_kirim, status_pengiriman) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $id_pengiriman, $id_pesanan, $id_kurir, $tanggal_kirim, $status_pengiriman);
        
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
    $result_pesanan = mysqli_query($conn, $query_pesanan);
    if (!$result_pesanan) {
        die("Error: " . mysqli_error($conn));
    }
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
                    AND NOT EXISTS (
                        SELECT 1 FROM pengiriman p 
                        WHERE p.id_kurir = k.id_kurir 
                        AND p.status_pengiriman = 'Dalam Perjalanan'
                    )
                    ORDER BY kc.nama_cabang, k.nama_kurir";
    $result_kurir = mysqli_query($conn, $query_kurir);
} else {
    $query_kurir = "SELECT k.id_kurir, k.nama_kurir 
                    FROM kurir k
                    WHERE k.status_keaktifan = 'Aktif' 
                    AND k.id_cabang = ?
                    AND NOT EXISTS (
                        SELECT 1 FROM pengiriman p 
                        WHERE p.id_kurir = k.id_kurir 
                        AND p.status_pengiriman = 'Dalam Perjalanan'
                    )";
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
        <div class="row mb-4">
            <div class="col">
                <h2 class="border-bottom pb-2">
                    <i class="fas fa-plus me-2"></i>Tambah Pengiriman
                </h2>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-id-card me-2"></i>ID Pengiriman:
                        </label>
                        <input type="text" name="id_pengiriman" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-shopping-cart me-2"></i>Pesanan:
                        </label>
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
                        <label class="form-label">
                            <i class="fa fa-user me-2"></i>Kurir:
                        </label>
                        <select name="id_kurir" class="form-select" required>
                            <option value="">Pilih Kurir</option>
                            <?php while ($kurir = mysqli_fetch_assoc($result_kurir)) { ?>
                                <option value="<?= $kurir['id_kurir'] ?>">
                                    <?= htmlspecialchars($kurir['nama_kurir']) ?>
                                </option>
                            <?php } ?>
                        </select>
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
