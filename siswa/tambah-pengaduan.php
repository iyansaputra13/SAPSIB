<?php
require_once 'cek-session.php';

$success = '';
$error = '';

// Ambil data kategori
$query_kategori = "SELECT * FROM kategori_sarana ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_siswa = $_SESSION['id_siswa'];
    $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul_pengaduan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $prioritas = mysqli_real_escape_string($conn, $_POST['prioritas']);
    
    // Upload foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $foto = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $foto;
            
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto = '';
            }
        }
    }
    
    $query = "INSERT INTO pengaduan (id_siswa, id_kategori, judul_pengaduan, deskripsi, lokasi, foto, prioritas, status) 
              VALUES ('$id_siswa', '$id_kategori', '$judul', '$deskripsi', '$lokasi', '$foto', '$prioritas', 'Pending')";
    
    if (mysqli_query($conn, $query)) {
        $success = 'Pengaduan berhasil diajukan!';
        echo "<script>setTimeout(function(){ window.location.href='data-pengaduan.php'; }, 2000);</script>";
    } else {
        $error = 'Gagal mengajukan pengaduan!';
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
    <title>Tambah Pengaduan - SAPSI</title>
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
        
        .form-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
        
        .preview-image {
            max-width: 200px;
            margin-top: 10px;
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
            <a href="tambah-pengaduan.php" class="nav-link active">
                <i class="fa-solid fa-plus-circle"></i> Tambah Pengaduan
            </a>
            <a href="data-pengaduan.php" class="nav-link">
                <i class="fa-solid fa-file-alt"></i> Data Pengaduan
            </a>
            <a href="profil.php" class="nav-link">
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
            <h4>Tambah Pengaduan Baru</h4>
            <small>Laporkan kerusakan atau kekurangan sarana sekolah</small>
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

        <div class="form-box">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Kategori Sarana *</label>
                        <select name="id_kategori" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                <option value="<?= $kategori['id_kategori'] ?>">
                                    <?= $kategori['nama_kategori'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Prioritas *</label>
                        <select name="prioritas" class="form-select" required>
                            <option value="">-- Pilih Prioritas --</option>
                            <option value="Rendah">Rendah</option>
                            <option value="Sedang" selected>Sedang</option>
                            <option value="Tinggi">Tinggi</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Judul Pengaduan *</label>
                        <input type="text" name="judul_pengaduan" class="form-control" 
                               placeholder="Contoh: Kursi rusak di ruang kelas" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Lokasi *</label>
                        <input type="text" name="lokasi" class="form-control" 
                               placeholder="Contoh: Ruang Kelas XII RPL 1" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Deskripsi Pengaduan *</label>
                        <textarea name="deskripsi" class="form-control" rows="5" 
                                  placeholder="Jelaskan secara detail kondisi kerusakan..." required></textarea>
                    </div>

                    <div class="col-md-12 mb-4">
                        <label>Foto (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" 
                               onchange="previewImage(event)">
                        <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Max: 2MB</small>
                        <div id="preview"></div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            Kirim Pengaduan
                        </button>
                        <a href="dashboard-siswa.php" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="preview-image">';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>