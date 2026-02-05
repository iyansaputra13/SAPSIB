<?php
require_once '../config/koneksi.php';

// Cek jika sudah login
if (isset($_SESSION['siswa_login'])) {
    header("Location: dashboard-siswa.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $password = MD5($_POST['password']);
    
    $query = "SELECT * FROM siswa WHERE nis = '$nis' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $siswa = mysqli_fetch_assoc($result);
        $_SESSION['siswa_login'] = true;
        $_SESSION['id_siswa'] = $siswa['id_siswa'];
        $_SESSION['nis'] = $siswa['nis'];
        $_SESSION['nama_siswa'] = $siswa['nama_lengkap'];
        $_SESSION['kelas'] = $siswa['kelas'];
        
        header("Location: dashboard-siswa.php");
        exit();
    } else {
        $error = 'NIS atau Password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Login Siswa - SAPSI</title>
    <style>
        body {
            background-color: #667eea;
            padding-top: 50px;
        }
        
        .login-box {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header i {
            font-size: 50px;
            color: #667eea;
        }
        
        .form-control {
            margin-bottom: 15px;
        }
        
        .btn-login {
            width: 100%;
            background-color: #667eea;
            color: white;
            border: none;
            padding: 10px;
        }
        
        .btn-login:hover {
            background-color: #5568d3;
            color: white;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="login-header">
                <i class="fa-solid fa-graduation-cap"></i>
                <h3>Login Siswa</h3>
                <p>SAPSI</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>NIS</label>
                    <input type="text" class="form-control" name="nis" required>
                </div>
                
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-login">Login</button>
            </form>
            
            <div style="margin-top: 15px; text-align: center;">
                <small class="text-muted">Gunakan NIS dan Password yang terdaftar</small>
            </div>
        </div>
        
        <div class="back-link">
            <a href="../index.php">Kembali ke Beranda</a>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>