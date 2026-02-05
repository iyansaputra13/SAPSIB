<?php
require_once '../config/koneksi.php';

// Cek jika sudah login
if (isset($_SESSION['admin_login'])) {
    header("Location: dashboard-admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = MD5($_POST['password']);
    
    $query = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_login'] = true;
        $_SESSION['id_admin'] = $admin['id_admin'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['nama_admin'] = $admin['nama_lengkap'];
        
        header("Location: dashboard-admin.php");
        exit();
    } else {
        $error = 'Username atau Password salah!';
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
    <title>Login Admin - SAPSI</title>
    <style>
        body {
            background-color: #2c3e50;
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
            color: #2c3e50;
        }
        
        .form-control {
            margin-bottom: 15px;
        }
        
        .btn-login {
            width: 100%;
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 10px;
        }
        
        .btn-login:hover {
            background-color: #34495e;
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
                <i class="fa-solid fa-user-shield"></i>
                <h3>Login Admin</h3>
                <p>SAPSI - Admin Panel</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-login">Login</button>
            </form>
            
            <div style="margin-top: 15px; text-align: center;">
                <small class="text-muted">Default: admin / admin123</small>
            </div>
        </div>
        
        <div class="back-link">
            <a href="../index.php">Kembali ke Beranda</a>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>