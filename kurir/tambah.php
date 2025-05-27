<?php
    include '../config.php';
    session_start();

    // Cek apakah user sudah login
    if (!isset($_SESSION['id_cabang'])) {
        header("Location: ../login.php");
        exit;
    }

    $is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

    // Query untuk cabang - hanya untuk kantor pusat
    if ($is_kantor_pusat) {
        $query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
        $result_cabang = mysqli_query($conn, $query_cabang);
    } else {
        // Untuk non-kantor pusat, ambil data cabang sendiri
        $query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang WHERE id_cabang = ?";
        $stmt_cabang = mysqli_prepare($conn, $query_cabang);
        mysqli_stmt_bind_param($stmt_cabang, "s", $_SESSION['id_cabang']);
        mysqli_stmt_execute($stmt_cabang);
        $result_cabang = mysqli_stmt_get_result($stmt_cabang);
        mysqli_stmt_close($stmt_cabang);
    }

    // Query untuk kendaraan yang tersedia
    $query_kendaraan = "SELECT k.id_kendaraan, k.nama_kendaraan, k.jenis_kendaraan, k.kapasitas
                       FROM kendaraan k
                       LEFT JOIN kurir kr ON k.id_kendaraan = kr.id_kendaraan
                       WHERE kr.id_kendaraan IS NULL
                       AND k.id_cabang = ?";
    $stmt_kendaraan = mysqli_prepare($conn, $query_kendaraan);
    if ($stmt_kendaraan) {
        mysqli_stmt_bind_param($stmt_kendaraan, "s", $_SESSION['id_cabang']);
        mysqli_stmt_execute($stmt_kendaraan);
        $result_kendaraan = mysqli_stmt_get_result($stmt_kendaraan);
        mysqli_stmt_close($stmt_kendaraan);
    }

    if (isset($_POST['submit'])) {
        // Validasi input
        if (empty($_POST['nama_kurir']) || empty($_POST['no_telepon'])) {
            $_SESSION['error'] = "Semua field wajib diisi kecuali kendaraan";
        } else {
            $nama_kurir = mysqli_real_escape_string($conn, $_POST['nama_kurir']);
            $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
            $id_cabang = $is_kantor_pusat ? 
                mysqli_real_escape_string($conn, $_POST['id_cabang']) : 
                $_SESSION['id_cabang'];
            $id_kendaraan = !empty($_POST['id_kendaraan']) ? 
                mysqli_real_escape_string($conn, $_POST['id_kendaraan']) : null;
            $status_keaktifan = 'Aktif';

            // Validasi kendaraan belongs to correct branch
            if ($id_kendaraan) {
                $check_query = "SELECT id_kendaraan FROM kendaraan WHERE id_kendaraan = ? AND id_cabang = ?";
                $check_stmt = mysqli_prepare($conn, $check_query);
                if ($check_stmt) {
                    mysqli_stmt_bind_param($check_stmt, "ss", $id_kendaraan, $id_cabang);
                    mysqli_stmt_execute($check_stmt);
                    mysqli_stmt_store_result($check_stmt);
                    if (mysqli_stmt_num_rows($check_stmt) == 0) {
                        $_SESSION['error'] = "Kendaraan tidak tersedia untuk cabang ini";
                        mysqli_stmt_close($check_stmt);
                        header("Location: tambah.php");
                        exit;
                    }
                    mysqli_stmt_close($check_stmt);
                }
            }

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
            } else {
                $_SESSION['error'] = "Error dalam persiapan query: " . mysqli_error($conn);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kurir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-user-plus"></i> Tambah Kurir</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Nama Kurir:</label>
                        <input type="text" name="nama_kurir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-phone"></i> No. Telepon:</label>
                        <input type="text" name="no_telepon" class="form-control" required>
                    </div>
                    <?php if ($is_kantor_pusat): ?>
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-building"></i> Kantor Cabang:</label>
                            <select name="id_cabang" class="form-select" required>
                                <option value="">Pilih Kantor Cabang</option>
                                <?php while ($cabang = mysqli_fetch_assoc($result_cabang)) { ?>
                                    <option value="<?= $cabang['id_cabang'] ?>">
                                        <?= htmlspecialchars($cabang['nama_cabang']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-truck"></i> Kendaraan:</label>
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
