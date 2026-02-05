<?php
require_once 'cek-session.php';

$success = '';
$error = '';

$id_siswa = $_SESSION['id_siswa'];

// Ambil data siswa
$query = "SELECT * FROM siswa WHERE id_siswa = '$id_siswa'";
$result = mysqli_query($conn, $query);
$siswa = mysqli_fetch_assoc($result);

// Update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    
    $query_update = "UPDATE siswa SET 
                     nama_lengkap = '$nama_lengkap',
                     kelas = '$kelas',
                     jurusan = '$jurusan',
                     no_telp = '$no_telp'
                     WHERE id_siswa = '$id_siswa'";
    
    if (mysqli_query($conn, $query_update)) {
        $_SESSION['nama_siswa'] = $nama_lengkap;
        $_SESSION['kelas'] = $kelas;
        $success = 'Profil berhasil diupdate!';
        
        $result = mysqli_query($conn, $query);
        $siswa = mysqli_fetch_assoc($result);
    } else {
        $error = 'Gagal mengupdate profil!';
    }
}

// Ganti password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ganti_password'])) {
    $password_lama = MD5($_POST['password_lama']);
    $password_baru = MD5($_POST['password_baru']);
    $konfirmasi_password = MD5($_POST['konfirmasi_password']);
    
    if ($password_lama != $siswa['password']) {
        $error = 'Password lama tidak sesuai!';
    } elseif ($_POST['password_baru'] != $_POST['konfirmasi_password']) {
        $error = 'Konfirmasi password tidak sesuai!';
    } elseif (strlen($_POST['password_baru']) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } else {
        $query_password = "UPDATE siswa SET password = '$password_baru' WHERE id_siswa = '$id_siswa'";
        
        if (mysqli_query($conn, $query_password)) {
            $success = 'Password berhasil diubah!';
        } else {
            $error = 'Gagal mengubah password!';
        }
    }
}

// Statistik pengaduan
$stat_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai
    FROM pengaduan WHERE id_siswa = '$id_siswa'";
$stat_result = mysqli_query($conn, $stat_query);
$stats = mysqli_fetch_assoc($stat_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Profil - SAPSI</title>
    <style>
        body {
            background-color: #f4f4f4;
        }
        
        .sidebar {
            background-color: #667eea;
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
        }
        
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .sidebar .user-info {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.2);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .top-bar {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .profile-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            padding: 15px;
            text-align: center;
            background: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-school fa-3x"></i>
            <h4>SAPSI</h4>
            <small>Sistem Pengaduan Sarana</small>
        </div>
        
        <div class="user-info">
            <i class="fa-solid fa-user-circle fa-2x mb-2"></i>
            <h6><?= $_SESSION['nama_siswa'] ?></h6>
            <small><?= $_SESSION['nis'] ?> | <?= $_SESSION['kelas'] ?></small>
        </div>

        <div style="padding: 20px 0;">
            <a href="dashboard-siswa.php" class="nav-link">
                <i class="fa-solid fa-dashboard"></i> Dashboard
            </a>
            <a href="tambah-pengaduan.php" class="nav-link">
                <i class="fa-solid fa-plus-circle"></i> Tambah Pengaduan
            </a>
            <a href="data-pengaduan.php" class="nav-link">
                <i class="fa-solid fa-file-alt"></i> Data Pengaduan
            </a>
            <a href="profil.php" class="nav-link active">
                <i class="fa-solid fa-user"></i> Profil
            </a>
            <a href="logout.php" class="nav-link" onclick="return confirm('Yakin ingin logout?')">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h4>Profil</h4>
            <small>Kelola informasi akun Anda</small>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistik Ringkas -->
        <div class="profile-box">
            <h6>Statistik Pengaduan Saya</h6>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <h4><?= $stats['total'] ?></h4>
                        <small>Total</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <h4><?= $stats['pending'] ?></h4>
                        <small>Pending</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <h4><?= $stats['diproses'] ?></h4>
                        <small>Diproses</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <h4><?= $stats['selesai'] ?></h4>
                        <small>Selesai</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Update Profil -->
            <div class="col-md-6">
                <div class="profile-box">
                    <h6>Update Profil</h6>
                    <hr>

                    <form method="POST">
                        <div class="mb-3">
                            <label>NIS</label>
                            <input type="text" class="form-control" value="<?= $siswa['nis'] ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label>Nama Lengkap *</label>
                            <input type="text" name="nama_lengkap" class="form-control" 
                                   value="<?= $siswa['nama_lengkap'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label>Kelas *</label>
                            <input type="text" name="kelas" class="form-control" 
                                   value="<?= $siswa['kelas'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label>Jurusan *</label>
                            <input type="text" name="jurusan" class="form-control" 
                                   value="<?= $siswa['jurusan'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label>No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" 
                                   value="<?= $siswa['no_telp'] ?>">
                        </div>

                        <button type="submit" name="update_profil" class="btn btn-primary">
                            Update Profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Ganti Password -->
            <div class="col-md-6">
                <div class="profile-box">
                    <h6>Ganti Password</h6>
                    <hr>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Password Lama *</label>
                            <input type="password" name="password_lama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password Baru *</label>
                            <input type="password" name="password_baru" class="form-control" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label>Konfirmasi Password Baru *</label>
                            <input type="password" name="konfirmasi_password" class="form-control" required>
                        </div>

                        <button type="submit" name="ganti_password" class="btn btn-warning">
                            Ganti Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>