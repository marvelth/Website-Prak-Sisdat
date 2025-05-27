<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang'])) {
    header("Location: ../login.php");
    exit;
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id_kendaraan = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM kendaraan WHERE id_kendaraan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kendaraan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kendaraan = mysqli_fetch_assoc($result);

if (!$kendaraan) {
    $_SESSION['error'] = "Kendaraan tidak ditemukan";
    header("Location: list.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nama_kendaraan = mysqli_real_escape_string($conn, $_POST['nama_kendaraan']);
    $jenis_kendaraan = mysqli_real_escape_string($conn, $_POST['jenis_kendaraan']);
    $kapasitas = mysqli_real_escape_string($conn, $_POST['kapasitas']);
    $id_cabang = $is_kantor_pusat ? 
        mysqli_real_escape_string($conn, $_POST['id_cabang']) : 
        $_SESSION['id_cabang'];

    $query = "UPDATE kendaraan SET 
              nama_kendaraan = ?, 
              jenis_kendaraan = ?, 
              kapasitas = ?, 
              id_cabang = ? 
              WHERE id_kendaraan = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssdss", $nama_kendaraan, $jenis_kendaraan, $kapasitas, $id_cabang, $id_kendaraan);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Data kendaraan berhasil diupdate";
        header("Location: list.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate data: " . mysqli_error($conn);
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
    <title>Edit Kendaraan</title>
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
                <h2 class="card-title"><i class="fa fa-edit"></i> Edit Kendaraan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-truck"></i> Nama Kendaraan:</label>
                        <input type="text" name="nama_kendaraan" class="form-control" 
                               value="<?= htmlspecialchars($kendaraan['nama_kendaraan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-tags"></i> Jenis Kendaraan:</label>
                        <select name="jenis_kendaraan" class="form-select" required>
                            <option value="Motor" <?= $kendaraan['jenis_kendaraan'] == 'Motor' ? 'selected' : '' ?>>Motor</option>
                            <option value="Mobil" <?= $kendaraan['jenis_kendaraan'] == 'Mobil' ? 'selected' : '' ?>>Mobil</option>
                            <option value="Truk" <?= $kendaraan['jenis_kendaraan'] == 'Truk' ? 'selected' : '' ?>>Truk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-weight-scale"></i> Kapasitas (kg):</label>
                        <input type="number" step="0.1" name="kapasitas" class="form-control" 
                               value="<?= $kendaraan['kapasitas'] ?>" required>
                    </div>
                    <?php if ($is_kantor_pusat): ?>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-building"></i> Kantor Cabang:</label>
                        <select name="id_cabang" class="form-select" required>
                            <?php while ($cabang = mysqli_fetch_assoc($result_cabang)): 
                                $selected = ($cabang['id_cabang'] == $kendaraan['id_cabang']) ? 'selected' : '';
                            ?>
                                <option value="<?= $cabang['id_cabang'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($cabang['nama_cabang']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
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
