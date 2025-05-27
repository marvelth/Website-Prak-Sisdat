<?php
    include '../config.php';
    session_start();

    $is_kantor_pusat = ($_SESSION['id_cabang'] == 'KC001');

    if (isset($_POST['submit'])) {
        $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
        $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $id_cabang = $is_kantor_pusat ? 
                mysqli_real_escape_string($conn, $_POST['id_cabang']) : 
                $_SESSION['id_cabang'];

        $query = "INSERT INTO pelanggan (id_pelanggan, nama_pelanggan, alamat, telepon, email, id_cabang) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $id_pelanggan, $nama_pelanggan, $alamat, $telepon, $email, $id_cabang);
            
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
    if ($is_kantor_pusat) {
        $query_cabang = "SELECT id_cabang, nama_cabang FROM kantor_cabang ORDER BY nama_cabang";
        $result_cabang = mysqli_query($conn, $query_cabang);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pelanggan</title>
    <!--Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="../dashboard.php"><i class="fa fa-truck"></i> Padjadjaran Express</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa fa-user-plus"></i> Tambah Pelanggan</h2>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-id-card"></i> ID Pelanggan:</label>
                        <input type="text" name="id_pelanggan" class="form-control" required 
                               pattern="PL[0-9]{3}" title="Format: PL001, PL002, dst">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Nama:</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-location-dot"></i> Alamat:</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-phone"></i> No. Telepon:</label>
                        <input type="text" name="telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-envelope"></i> Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <?php
                    if ($is_kantor_pusat) {
                    echo '<div class="mb-3">
                        <label for="id_cabang" class="form-label"><i class="fa fa-building"></i> Kantor Cabang:</label>
                        <select id="id_cabang" name="id_cabang" class="form-select" required>
                            <option value="">Pilih Kantor Cabang</option>';
                            while ($cabang = mysqli_fetch_assoc($result_cabang)) {
                                echo '<option value="' . $cabang['id_cabang'] . '">' . 
                                        htmlspecialchars($cabang['nama_cabang']) . '</option>';
                            }
                        echo'</select>
                    </div>';
                    }
                    ?>
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
