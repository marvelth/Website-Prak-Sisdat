<?php
include '../config.php';
session_start();

if (isset($_POST['submit'])) {
    $nama_kurir = mysqli_real_escape_string($conn, $_POST['nama_kurir']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);
    $id_kendaraan = !empty($_POST['id_kendaraan']) ? mysqli_real_escape_string($conn, $_POST['id_kendaraan']) : null;
    $status_keaktifan = 'Aktif'; // Default status for new courier

    $query = "INSERT INTO kurir (nama_kurir, no_telepon, id_cabang, id_kendaraan, status_keaktifan) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssiss", $nama_kurir, $no_telepon, $id_cabang, $id_kendaraan, $status_keaktifan);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data kurir berhasil ditambahkan";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch kantor cabang data for dropdown
$query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
$result_cabang = mysqli_query($conn, $query_cabang);

// Fetch available vehicles based on selected branch
$query_kendaraan = "SELECT k.id_kendaraan, k.nama_kendaraan, k.jenis_kendaraan, k.kapasitas
                    FROM kendaraan k
                    LEFT JOIN kurir kr ON k.id_kendaraan = kr.id_kendaraan
                    WHERE kr.id_kendaraan IS NULL";
$result_kendaraan = mysqli_query($conn, $query_kendaraan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kurir</title>
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
                <h2 class="card-title">Tambah Kurir</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama Kurir:</label>
                        <input type="text" name="nama_kurir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon:</label>
                        <input type="text" name="no_telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kantor Cabang:</label>
                        <select name="id_cabang" class="form-select" required>
                            <option value="">Pilih Kantor Cabang</option>
                            <?php while ($cabang = mysqli_fetch_assoc($result_cabang)) { ?>
                                <option value="<?= $cabang['id_cabang'] ?>">
                                    <?= htmlspecialchars($cabang['nama_cabang']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kendaraan:</label>
                        <select name="id_kendaraan" class="form-select">
                            <option value="">Pilih Kendaraan (Opsional)</option>
                            <?php while ($kendaraan = mysqli_fetch_assoc($result_kendaraan)) { ?>
                                <option value="<?= $kendaraan['id_kendaraan'] ?>">
                                    <?= htmlspecialchars($kendaraan['nama_kendaraan']) ?> - 
                                    <?= htmlspecialchars($kendaraan['jenis_kendaraan']) ?> 
                                    (Kapasitas: <?= $kendaraan['kapasitas'] ?>)
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
