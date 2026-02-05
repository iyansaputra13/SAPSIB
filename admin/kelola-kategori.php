<?php
require_once 'cek-session.php';

$success = '';
$error = '';

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id_kategori = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    // Cek apakah kategori punya pengaduan
    $cek_pengaduan = "SELECT COUNT(*) as total FROM pengaduan WHERE id_kategori = '$id_kategori'";
    $result_cek = mysqli_query($conn, $cek_pengaduan);
    $cek = mysqli_fetch_assoc($result_cek);
    
    if ($cek['total'] > 0) {
        $error = 'Tidak dapat menghapus kategori yang memiliki pengaduan!';
    } else {
        $query_hapus = "DELETE FROM kategori_sarana WHERE id_kategori = '$id_kategori'";
        if (mysqli_query($conn, $query_hapus)) {
            $success = 'Kategori berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus kategori!';
        }
    }
}

// Tambah/Edit kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    if (isset($_POST['id_kategori']) && !empty($_POST['id_kategori'])) {
        // Update
        $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
        
        $query = "UPDATE kategori_sarana SET 
                  nama_kategori = '$nama_kategori',
                  deskripsi = '$deskripsi'
                  WHERE id_kategori = '$id_kategori'";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Kategori berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate kategori!';
        }
    } else {
        // Insert
        $query = "INSERT INTO kategori_sarana (nama_kategori, deskripsi) 
                  VALUES ('$nama_kategori', '$deskripsi')";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Kategori berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan kategori!';
        }
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $query_edit = "SELECT * FROM kategori_sarana WHERE id_kategori = '$id_edit'";
    $result_edit = mysqli_query($conn, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// Ambil data kategori
$query = "SELECT k.*, 
          (SELECT COUNT(*) FROM pengaduan WHERE id_kategori = k.id_kategori) as total_pengaduan,
          (SELECT COUNT(*) FROM pengaduan WHERE id_kategori = k.id_kategori AND status = 'Pending') as pending_pengaduan
          FROM kategori_sarana k
          ORDER BY k.nama_kategori ASC";
$result = mysqli_query($conn, $query);

$total_kategori = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Kelola Kategori - SAPSI</title>
    <style>
        body {
            background-color: #f4f4f4;
        }
        
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
        }
        
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar .user-info {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #34495e;
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
            margin-bottom: 20px;
        }
        
        .kategori-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #2c3e50;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fa-solid fa-school fa-3x"></i>
            <h4>SAPSI</h4>
            <small>Admin Panel</small>
        </div>
        
        <div class="user-info">
            <i class="fa-solid fa-user-shield fa-2x mb-2"></i>
            <h6><?= $_SESSION['nama_admin'] ?></h6>
            <small>Administrator</small>
        </div>

        <div style="padding: 20px 0;">
            <a href="dashboard-admin.php" class="nav-link">
                <i class="fa-solid fa-dashboard"></i> Dashboard
            </a>
            <a href="kelola-pengaduan.php" class="nav-link">
                <i class="fa-solid fa-tasks"></i> Kelola Pengaduan
            </a>
            <a href="kelola-siswa.php" class="nav-link">
                <i class="fa-solid fa-users"></i> Kelola Siswa
            </a>
            <a href="kelola-kategori.php" class="nav-link active">
                <i class="fa-solid fa-list"></i> Kelola Kategori
            </a>
            <a href="laporan.php" class="nav-link">
                <i class="fa-solid fa-file-pdf"></i> Laporan
            </a>
            <a href="logout.php" class="nav-link" onclick="return confirm('Yakin ingin logout?')">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h4>Kelola Kategori Sarana</h4>
            <small>Kelola kategori fasilitas sekolah</small>
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

        <!-- Form Tambah/Edit -->
        <div class="form-box">
            <h5><?= $edit_data ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h5>
            <hr>

            <form method="POST">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id_kategori" value="<?= $edit_data['id_kategori'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Nama Kategori *</label>
                        <input type="text" name="nama_kategori" class="form-control" 
                               value="<?= $edit_data ? $edit_data['nama_kategori'] : '' ?>" required>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label>Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control" 
                               value="<?= $edit_data ? $edit_data['deskripsi'] : '' ?>">
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <?= $edit_data ? 'Update' : 'Tambah' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="kelola-kategori.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Daftar Kategori -->
        <h5>Daftar Kategori (<?= $total_kategori ?>)</h5>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="kategori-card">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6><?= $row['nama_kategori'] ?></h6>
                            <small class="text-muted"><?= $row['deskripsi'] ?: 'Tidak ada deskripsi' ?></small>
                        </div>
                        <div class="col-md-4">
                            <span class="badge bg-primary"><?= $row['total_pengaduan'] ?> Total</span>
                            <?php if ($row['pending_pengaduan'] > 0): ?>
                                <span class="badge bg-warning"><?= $row['pending_pengaduan'] ?> Pending</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="?edit=<?= $row['id_kategori'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?hapus=<?= $row['id_kategori'] ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">Belum ada kategori</div>
        <?php endif; ?>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>