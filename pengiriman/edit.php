<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php">Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Update Status Pengiriman</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Status Pengiriman:</label>
                        <select name="status_pengiriman" class="form-select" required>
                            <option value="Menunggu Kurir" <?= $pengiriman['status_pengiriman'] == 'Menunggu Kurir' ? 'selected' : '' ?>>Menunggu Kurir</option>
                            <option value="Dalam Perjalanan" <?= $pengiriman['status_pengiriman'] == 'Dalam Perjalanan' ? 'selected' : '' ?>>Dalam Perjalanan</option>
                            <option value="Terkirim" <?= $pengiriman['status_pengiriman'] == 'Terkirim' ? 'selected' : '' ?>>Terkirim</option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update
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
