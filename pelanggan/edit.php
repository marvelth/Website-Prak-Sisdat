<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}

$is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = "Data pelanggan tidak ditemukan";
    header("Location: list.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);

    $query = "UPDATE pelanggan SET 
              nama_pelanggan = ?, 
              alamat = ?, 
              telepon = ?, 
              email = ?,
              id_cabang = ?
              WHERE id_pelanggan = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssss", $nama_pelanggan, $alamat, $telepon, $email, $id_cabang, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Data pelanggan berhasil diupdate";
        header("Location: list.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate data: " . mysqli_error($conn);
    }
}

// Fetch kantor cabang data for dropdown
$query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
$result_cabang = mysqli_query($conn, $query_cabang);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pelanggan</title>
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
                    <i class="fas fa-edit me-2"></i>Edit Pelanggan
                </h2>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user me-2"></i>Nama:</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="<?= htmlspecialchars($data['nama_pelanggan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-location-dot me-2"></i>Alamat:</label>
                        <textarea name="alamat" class="form-control" required rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-phone me-2"></i>No. Telepon:</label>
                        <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($data['telepon']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-envelope me-2"></i>Email:</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                    </div>
                    <?php if ($is_kantor_pusat): ?>
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-building me-2"></i>Kantor Cabang:</label>
                            <select name="id_cabang" class="form-select" required>
                                <?php while ($cabang = mysqli_fetch_assoc($result_cabang)): 
                                    $selected = ($cabang['id_cabang'] == $data['id_cabang']) ? 'selected' : '';
                                ?>
                                    <option value="<?= $cabang['id_cabang'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($cabang['nama_cabang']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="id_cabang" value="<?= $_SESSION['id_cabang'] ?>">
                    <?php endif; ?>
                    <div class="d-flex gap-2">
                        <button type="submit" name="submit" class="btn btn-primary shadow-sm">
                            <i class="fa fa-save me-2"></i>Update
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
