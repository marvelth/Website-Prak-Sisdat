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
            $id_kurir = mysqli_real_escape_string($conn, $_POST['id_kurir']);
            $nama_kurir = mysqli_real_escape_string($conn, $_POST['nama_kurir']);
            $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
            $id_cabang = $is_kantor_pusat ? 
                mysqli_real_escape_string($conn, $_POST['id_cabang']) : 
                $_SESSION['id_cabang'];
            $id_kendaraan = !empty($_POST['id_kendaraan']) ? 
                mysqli_real_escape_string($conn, $_POST['id_kendaraan']) : null;
            $status_keaktifan = mysqli_real_escape_string($conn, $_POST['status_keaktifan']);

            // Validasi id_cabang exists
            $check_cabang_query = "SELECT id_cabang FROM kantor_cabang WHERE id_cabang = ?";
            $check_cabang_stmt = mysqli_prepare($conn, $check_cabang_query);
            if ($check_cabang_stmt) {
                mysqli_stmt_bind_param($check_cabang_stmt, "s", $id_cabang);
                mysqli_stmt_execute($check_cabang_stmt);
                mysqli_stmt_store_result($check_cabang_stmt);
                if (mysqli_stmt_num_rows($check_cabang_stmt) == 0) {
                    $_SESSION['error'] = "Kantor cabang tidak valid";
                    mysqli_stmt_close($check_cabang_stmt);
                    header("Location: tambah.php");
                    exit;
                }
                mysqli_stmt_close($check_cabang_stmt);
            }

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

            $query = "INSERT INTO kurir (id_kurir, nama_kurir, no_telepon, id_cabang, id_kendaraan, status_keaktifan) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssi", $id_kurir, $nama_kurir, $no_telepon, $id_cabang, $id_kendaraan, $status_keaktifan);
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
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="fa fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col">
                <h2 class="border-bottom pb-2">
                    <i class="fas fa-user-plus me-2"></i>Tambah Kurir
                </h2>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-id-card me-2"></i>ID Kurir:
                        </label>
                        <input type="text" name="id_kurir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-user me-2"></i>Nama Kurir:
                        </label>
                        <input type="text" name="nama_kurir" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-phone me-2"></i>No. Telepon:
                        </label>
                        <input type="text" name="no_telepon" class="form-control" required>
                    </div>
                    <?php if ($is_kantor_pusat): ?>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fa fa-building me-2"></i>Kantor Cabang:
                            </label>
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
                        <label class="form-label">
                            <i class="fa fa-truck me-2"></i>Kendaraan:
                        </label>
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
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-toggle-on me-2"></i>Status Keaktifan:
                        </label>
                        <select name="status_keaktifan" class="form-select" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
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
