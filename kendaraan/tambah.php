<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

if (isset($_POST['submit'])) {
    $id_kendaraan = mysqli_real_escape_string($conn, $_POST['id_kendaraan']);
    $nama_kendaraan = mysqli_real_escape_string($conn, $_POST['nama_kendaraan']);
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    $id_cabang = $is_kantor_pusat ? 
        mysqli_real_escape_string($conn, $_POST['id_cabang']) : 
        $_SESSION['id_cabang'];

    $query = "INSERT INTO kendaraan (id_kendaraan, nama_kendaraan, jenis_kendaraan, kapasitas, id_cabang) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssds", $id_kendaraan, $nama_kendaraan, $jenis_kendaraan, $kapasitas, $id_cabang);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Kendaraan berhasil ditambahkan";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}

// Get cabang data for dropdown if kantor pusat
if ($is_kantor_pusat) {
    $query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
    $result_cabang = mysqli_query($conn, $query_cabang);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php"><i class="fa fa-truck"></i> Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-plus"></i> Tambah Kendaraan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-id-card"></i> ID Kendaraan:</label>
                        <input type="text" name="id_kendaraan" class="form-control" required
                               pattern="KN[0-9]{3}" title="Format: KN001, KN002, dst">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-truck"></i> Nama Kendaraan:</label>
                        <input type="text" name="nama_kendaraan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-tags"></i> Jenis Kendaraan:</label>
                        <select name="jenis_kendaraan" class="form-select" required>
                            <option value="">Pilih Jenis Kendaraan</option>
                            <option value="Motor">Motor</option>
                            <option value="Mobil">Mobil</option>
                            <option value="Truk">Truk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-weight-scale"></i> Kapasitas (kg):</label>
                        <input type="number" step="0.1" name="kapasitas" class="form-control" required>
                    </div>
                    <?php if ($is_kantor_pusat): ?>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-building"></i> Kantor Cabang:</label>
                        <select name="id_cabang" class="form-select" required>
                            <option value="">Pilih Kantor Cabang</option>
                            <?php while ($cabang = mysqli_fetch_assoc($result_cabang)): ?>
                                <option value="<?= $cabang['id_cabang'] ?>">
                                    <?= htmlspecialchars($cabang['nama_cabang']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
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
