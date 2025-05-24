<?php
    include '../config.php';
    session_start();

    if (isset($_POST['submit'])) {
        $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);

        $query = "INSERT INTO pelanggan (nama_pelanggan, alamat, telepon, email, id_cabang) 
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $nama_pelanggan, $alamat, $telepon, $email, $id_cabang);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Data pelanggan berhasil ditambahkan";
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

    // Fetch kantor cabang data buat dropdown
    $query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
    $result_cabang = mysqli_query($conn, $query_cabang);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pelanggan</title>
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
                <h2 class="card-title">Tambah Pelanggan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama:</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat:</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telepon:</label>
                        <input type="text" name="telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_cabang" class="form-label">Kantor Cabang:</label>
                        <select id="id_cabang" name="id_cabang" class="form-select" required>
                            <option value="">Pilih Kantor Cabang</option>
                            <?php
                            while ($cabang = mysqli_fetch_assoc($result_cabang)) {
                                echo '<option value="' . $cabang['id_cabang'] . '">' . 
                                        htmlspecialchars($cabang['nama_cabang']) . '</option>';
                            }
                            ?>
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
