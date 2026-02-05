<?php
require_once 'cek-session.php';

$success = '';
$error = '';

// Hapus siswa
if (isset($_GET['hapus'])) {
    $id_siswa = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    $cek_pengaduan = "SELECT COUNT(*) as total FROM pengaduan WHERE id_siswa = '$id_siswa'";
    $result_cek = mysqli_query($conn, $cek_pengaduan);
    $cek = mysqli_fetch_assoc($result_cek);
    
    if ($cek['total'] > 0) {
        $error = 'Tidak dapat menghapus siswa yang memiliki pengaduan!';
    } else {
        $query_hapus = "DELETE FROM siswa WHERE id_siswa = '$id_siswa'";
        if (mysqli_query($conn, $query_hapus)) {
            $success = 'Data siswa berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus data siswa!';
        }
    }
}

// Tambah/Edit siswa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $password = !empty($_POST['password']) ? MD5($_POST['password']) : '';
    
    if (isset($_POST['id_siswa']) && !empty($_POST['id_siswa'])) {
        $id_siswa = mysqli_real_escape_string($conn, $_POST['id_siswa']);
        $password_query = !empty($password) ? ", password = '$password'" : "";
        
        $query = "UPDATE siswa SET 
                  nis = '$nis',
                  nama_lengkap = '$nama_lengkap',
                  kelas = '$kelas',
                  jurusan = '$jurusan',
                  no_telp = '$no_telp'
                  $password_query
                  WHERE id_siswa = '$id_siswa'";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Data siswa berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate data siswa!';
        }
    } else {
        if (empty($password)) {
            $error = 'Password harus diisi untuk siswa baru!';
        } else {
            $query = "INSERT INTO siswa (nis, nama_lengkap, kelas, jurusan, no_telp, password) 
                      VALUES ('$nis', '$nama_lengkap', '$kelas', '$jurusan', '$no_telp', '$password')";
            
            if (mysqli_query($conn, $query)) {
                $success = 'Data siswa berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan data siswa! NIS mungkin sudah terdaftar.';
            }
        }
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($conn, $_GET['edit']);
    $query_edit = "SELECT * FROM siswa WHERE id_siswa = '$id_edit'";
    $result_edit = mysqli_query($conn, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// Ambil data siswa
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT s.*, 
          (SELECT COUNT(*) FROM pengaduan WHERE id_siswa = s.id_siswa) as total_pengaduan
          FROM siswa s WHERE 1=1";

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (s.nis LIKE '%$search_escaped%' 
                OR s.nama_lengkap LIKE '%$search_escaped%' 
                OR s.kelas LIKE '%$search_escaped%')";
}

$query .= " ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $query);
$total_siswa = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Kelola Siswa - SAPSI</title>
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
        
        .form-box, .table-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <a href="kelola-siswa.php" class="nav-link active">
                <i class="fa-solid fa-users"></i> Kelola Siswa
            </a>
            <a href="kelola-kategori.php" class="nav-link">
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
            <h4>Kelola Siswa</h4>
            <small>Kelola data siswa yang terdaftar</small>
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
            <h5><?= $edit_data ? 'Edit Siswa' : 'Tambah Siswa Baru' ?></h5>
            <hr>

            <form method="POST">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id_siswa" value="<?= $edit_data['id_siswa'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>NIS *</label>
                        <input type="text" name="nis" class="form-control" 
                               value="<?= $edit_data ? $edit_data['nis'] : '' ?>" required>
                    </div>

                    <div class="col-md-5 mb-3">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" class="form-control" 
                               value="<?= $edit_data ? $edit_data['nama_lengkap'] : '' ?>" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Kelas *</label>
                        <input type="text" name="kelas" class="form-control" 
                               value="<?= $edit_data ? $edit_data['kelas'] : '' ?>" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Jurusan *</label>
                        <input type="text" name="jurusan" class="form-control" 
                               value="<?= $edit_data ? $edit_data['jurusan'] : '' ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control" 
                               value="<?= $edit_data ? $edit_data['no_telp'] : '' ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Password <?= !$edit_data ? '*' : '(Kosongkan jika tidak diubah)' ?></label>
                        <input type="password" name="password" class="form-control" 
                               <?= !$edit_data ? 'required' : '' ?>>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <?= $edit_data ? 'Update' : 'Tambah' ?>
                        </button>
                        <?php if ($edit_data): ?>
                            <a href="kelola-siswa.php" class="btn btn-secondary">Batal</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pencarian -->
        <div class="form-box">
            <form method="GET" class="row">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari NIS, nama, atau kelas..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>
        </div>

        <!-- Tabel Siswa -->
        <div class="table-box">
            <h5>Daftar Siswa (<?= $total_siswa ?>)</h5>
            <hr>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>No. Telepon</th>
                            <th>Pengaduan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nis'] ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= $row['kelas'] ?></td>
                                    <td><?= $row['jurusan'] ?></td>
                                    <td><?= $row['no_telp'] ?: '-' ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $row['total_pengaduan'] ?></span>
                                    </td>
                                    <td>
                                        <a href="?edit=<?= $row['id_siswa'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="?hapus=<?= $row['id_siswa'] ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin hapus?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data siswa</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>