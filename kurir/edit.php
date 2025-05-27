<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id_kurir = mysqli_real_escape_string($conn, $_GET['id']);

// Get courier data
$query = "SELECT * FROM kurir WHERE id_kurir = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_kurir);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$kurir = mysqli_fetch_assoc($result);

if (!$kurir) {
    $_SESSION['error'] = "Kurir tidak ditemukan";
    header("Location: list.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nama_kurir = mysqli_real_escape_string($conn, $_POST['nama_kurir']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);
    $status_keaktifan = mysqli_real_escape_string($conn, $_POST['status_keaktifan']);
    $id_kendaraan = mysqli_real_escape_string($conn, $_POST['id_kendaraan']);

    $query = "UPDATE kurir SET nama_kurir = ?, no_telepon = ?, id_cabang = ?, status_keaktifan = ?, id_kendaraan = ? WHERE id_kurir = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $nama_kurir, $no_telepon, $id_cabang, $status_keaktifan, $id_kendaraan, $id_kurir);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data kurir berhasil diupdate";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}

// Fetch kantor cabang data for dropdown
$query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
$result_cabang = mysqli_query($conn, $query_cabang);

// Get available vehicles that are not assigned to other couriers
$query_kendaraan = "SELECT k.id_kendaraan, k.nama_kendaraan, k.jenis_kendaraan, k.kapasitas
                    FROM kendaraan k
                    LEFT JOIN kurir kr ON k.id_kendaraan = kr.id_kendaraan
                    WHERE kr.id_kendaraan IS NULL OR kr.id_kendaraan = ?";
$stmt_kendaraan = mysqli_prepare($conn, $query_kendaraan);
mysqli_stmt_bind_param($stmt_kendaraan, "s", $kurir['id_kendaraan']);
mysqli_stmt_execute($stmt_kendaraan);
$result_kendaraan = mysqli_stmt_get_result($stmt_kendaraan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Kurir</title>
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
                <h2 class="card-title"><i class="fa fa-edit"></i> Edit Kurir</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Nama Kurir:</label>
                        <input type="text" name="nama_kurir" class="form-control" value="<?= htmlspecialchars($kurir['nama_kurir']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-phone"></i> No. Telepon:</label>
                        <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($kurir['no_telepon']) ?>" required>
                    </div>
                    <?php if ($_SESSION['id_cabang'] == 'KC001'): ?>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-building"></i> Kantor Cabang:</label>
                        <select name="id_cabang" class="form-select" required>
                            <?php while ($cabang = mysqli_fetch_assoc($result_cabang)) { 
                                $selected = ($cabang['id_cabang'] === $kurir['id_cabang']) ? 'selected' : '';
                            ?>
                                <option value="<?= $cabang['id_cabang'] ?>" <?= $selected ?>><?= htmlspecialchars($cabang['nama_cabang']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="id_cabang" value="<?= $_SESSION['id_cabang'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-toggle-on"></i> Status:</label>
                        <select name="status_keaktifan" class="form-select" required>
                            <option value="Aktif" <?= $kurir['status_keaktifan'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Tidak Aktif" <?= $kurir['status_keaktifan'] == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-truck"></i> Kendaraan:</label>
                        <select name="id_kendaraan" class="form-select">
                            <option value="">Tidak Ada Kendaraan</option>
                            <?php while ($kendaraan = mysqli_fetch_assoc($result_kendaraan)) { 
                                $selected = ($kendaraan['id_kendaraan'] == $kurir['id_kendaraan']) ? 'selected' : '';
                            ?>
                                <option value="<?= $kendaraan['id_kendaraan'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($kendaraan['nama_kendaraan']) ?> - 
                                    <?= htmlspecialchars($kendaraan['jenis_kendaraan']) ?>
                                    (Kapasitas: <?= $kendaraan['kapasitas'] ?>)
                                </option>
                            <?php } ?>
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
