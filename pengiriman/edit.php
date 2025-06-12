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


$id_pengiriman = mysqli_real_escape_string($conn, $_GET['id']);

// Get delivery data
$query = "SELECT * FROM pengiriman WHERE id_pengiriman = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id_pengiriman);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pengiriman = mysqli_fetch_assoc($result);

if (!$pengiriman) {
    $_SESSION['error'] = "Data pengiriman tidak ditemukan";
    header("Location: list.php");
    exit;
}

if (isset($_POST['submit'])) {
    $status_pengiriman = mysqli_real_escape_string($conn, $_POST['status_pengiriman']);
    $tanggal_sampai = $status_pengiriman == 'Terkirim' ? date('Y-m-d') : null;

    $query = "UPDATE pengiriman SET status_pengiriman = ?, tanggal_sampai = ? WHERE id_pengiriman = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $status_pengiriman, $tanggal_sampai, $id_pengiriman);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Status pengiriman berhasil diupdate";
        header("Location: list.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate status: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Status Pengiriman</title>
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
                    <i class="fas fa-edit me-2"></i>Update Status Pengiriman
                </h2>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-info-circle me-2"></i>Status Pengiriman:
                        </label>
                        <select name="status_pengiriman" class="form-select" required>
                            <option value="Menunggu Kurir" <?= $pengiriman['status_pengiriman'] == 'Menunggu Kurir' ? 'selected' : '' ?>>
                                Menunggu Kurir
                            </option>
                            <option value="Dalam Perjalanan" <?= $pengiriman['status_pengiriman'] == 'Dalam Pengiriman' ? 'selected' : '' ?>>
                                Dalam Pengiriman
                            </option>
                            <option value="Terkirim" <?= $pengiriman['status_pengiriman'] == 'Terkirim' ? 'selected' : '' ?>>
                                Terkirim
                            </option>
                        </select>
                    </div>
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
