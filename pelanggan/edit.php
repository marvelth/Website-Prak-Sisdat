<?php
include '../config.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

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
    <!--Booststrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Font Amazing-->
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
                <h2 class="card-title">Edit Pelanggan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama:</label>
                        <input type="text" name="nama_pelanggan" class="form-control" value="<?= htmlspecialchars($data['nama_pelanggan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat:</label>
                        <textarea name="alamat" class="form-control" required rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon:</label>
                        <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($data['telepon']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kantor Cabang:</label>
                        <select name="id_cabang" class="form-select" required>
                            <?php while ($cabang = mysqli_fetch_assoc($result_cabang)) { 
                                $selected = ($cabang['id_cabang'] == $data['id_cabang']) ? 'selected' : '';
                            ?>
                                <option value="<?= $cabang['id_cabang'] ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($cabang['nama_cabang']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Update</button>
                    <a href="list.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
