<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}


if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM kantor_cabang WHERE id_cabang = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = "Data cabang tidak ditemukan";
    header("Location: list.php");
    exit;
}

if (isset($_POST['submit'])) {
    $nama_cabang = mysqli_real_escape_string($conn, $_POST['nama_cabang']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $query = "UPDATE kantor_cabang SET nama_cabang = ?, alamat = ? WHERE id_cabang = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $nama_cabang, $alamat, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Data cabang berhasil diupdate";
        header("Location: list.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Kantor Cabang</title>
    <!--Bootstrap-->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="../assets/font-awesome/css/all.min.css">
    <!--CSS-->
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="shortcut icon" href="../assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
                <img src="../assets/img/logo.png" alt="Padjadjaran Express" height="30" class="me-2">
                <span>Padjadjaran Express</span>
            </a>
        </div>
    </nav>


    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-edit"></i> Edit Kantor Cabang</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-building"></i> Nama Cabang:</label>
                        <input type="text" name="nama_cabang" class="form-control" 
                               value="<?= htmlspecialchars($data['nama_cabang']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-map-marker"></i> Alamat:</label>
                        <textarea name="alamat" class="form-control" required><?= htmlspecialchars($data['alamat']) ?></textarea>
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
