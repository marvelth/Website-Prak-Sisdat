<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id_cabang']) || empty($_SESSION['id_cabang'])) {
    session_unset();
    session_destroy();

    header("Location: ../index.php");
    exit();
}


if (isset($_POST['submit'])) {
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);
    $nama_cabang = mysqli_real_escape_string($conn, $_POST['nama_cabang']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO kantor_cabang (id_cabang, nama_cabang, alamat, password) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $id_cabang, $nama_cabang, $alamat, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Data cabang berhasil ditambahkan";
            header("Location: list.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menambahkan data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kantor Cabang</title>
    <!--Bootstrap-->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="../assets/font-awesome/css/all.min.css">
    <!--CSS-->
    <link rel="stylesheet" href="../assets/style.css">
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
                <h2 class="card-title"><i class="fa fa-plus"></i> Tambah Kantor Cabang</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-id-card"></i> ID Cabang:</label>
                        <input type="text" name="id_cabang" class="form-control" required 
                               pattern="KC[0-9]{3}" title="Format: KC001, KC002, dst">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-building"></i> Nama Cabang:</label>
                        <input type="text" name="nama_cabang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-map-marker"></i> Alamat:</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-key"></i> Password:</label>
                        <input type="password" name="password" class="form-control" required>
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
