<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Padjadjaran Express</title>
    <!--Bootstrap-->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!--Fontawesome-->
    <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
    <!--CSS-->
    <link rel="stylesheet" href="assets/style.css">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="container mt-5">
        <h1 class="main-title text-center">PADJADJARAN EXPRESS</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Login Kantor Cabang
                    </div>
                    <div class="card-body">
                        <?php
                            if (isset($_SESSION['error'])) {
                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                                unset($_SESSION['error']); // Hapus pesan error dari sesi setelah ditampilkan
                            }
                        ?>
                        <form action="proses_login.php" method="POST">
                            <div class="mb-3">
                                <label for="id_cabang" class="form-label">ID Cabang</label>
                                <input type="text" class="form-control" id="id_cabang" name="id_cabang" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
                <footer class="text-center">
                    <p>&copy; 2025 Padjadjaran Express. All rights reserved.</p>
                </footer>    
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>