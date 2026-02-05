<?php
require_once 'cek-session.php';

$id_siswa = $_SESSION['id_siswa'];

// Hapus pengaduan jika ada request
if (isset($_GET['hapus'])) {
    $id_pengaduan = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    $query_cek = "SELECT * FROM pengaduan WHERE id_pengaduan = '$id_pengaduan' AND id_siswa = '$id_siswa'";
    $result_cek = mysqli_query($conn, $query_cek);
    
    if (mysqli_num_rows($result_cek) > 0) {
        $pengaduan = mysqli_fetch_assoc($result_cek);
        
        if (!empty($pengaduan['foto']) && file_exists("../uploads/" . $pengaduan['foto'])) {
            unlink("../uploads/" . $pengaduan['foto']);
        }
        
        $query_hapus = "DELETE FROM pengaduan WHERE id_pengaduan = '$id_pengaduan'";
        if (mysqli_query($conn, $query_hapus)) {
            header("Location: data-pengaduan.php?success=hapus");
            exit();
        }
    }
}

// Ambil data pengaduan
$query = "SELECT p.*, k.nama_kategori 
          FROM pengaduan p
          JOIN kategori_sarana k ON p.id_kategori = k.id_kategori
          WHERE p.id_siswa = '$id_siswa'
          ORDER BY p.tanggal_pengaduan DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Data Pengaduan - SAPSI</title>
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
        
        .table-box {
            background: white;
            padding: 20px;
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
            <a href="data-pengaduan.php" class="nav-link active">
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
            <h4>Data Pengaduan</h4>
            <small>Semua pengaduan yang telah Anda ajukan</small>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'hapus'): ?>
            <div class="alert alert-success alert-dismissible">
                Pengaduan berhasil dihapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-box">
            <div class="d-flex justify-content-between mb-3">
                <h5>Daftar Pengaduan (<?= mysqli_num_rows($result) ?>)</h5>
                <a href="tambah-pengaduan.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Pengaduan
                </a>
            </div>
            <hr>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><small><?= date('d/m/Y H:i', strtotime($row['tanggal_pengaduan'])) ?></small></td>
                                    <td><?= $row['judul_pengaduan'] ?></td>
                                    <td><?= $row['nama_kategori'] ?></td>
                                    <td><?= $row['lokasi'] ?></td>
                                    <td>
                                        <?php
                                        $priority_class = 'bg-secondary';
                                        if ($row['prioritas'] == 'Tinggi') $priority_class = 'bg-danger';
                                        elseif ($row['prioritas'] == 'Sedang') $priority_class = 'bg-warning';
                                        ?>
                                        <span class="badge <?= $priority_class ?>"><?= $row['prioritas'] ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = 'bg-secondary';
                                        if ($row['status'] == 'Pending') $badge_class = 'bg-warning';
                                        elseif ($row['status'] == 'Diproses') $badge_class = 'bg-info';
                                        elseif ($row['status'] == 'Selesai') $badge_class = 'bg-success';
                                        elseif ($row['status'] == 'Ditolak') $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $row['status'] ?></span>
                                    </td>
                                    <td>
                                        <a href="detail-pengaduan.php?id=<?= $row['id_pengaduan'] ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <?php if ($row['status'] == 'Pending'): ?>
                                            <a href="edit-pengaduan.php?id=<?= $row['id_pengaduan'] ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <a href="?hapus=<?= $row['id_pengaduan'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p>Belum ada pengaduan</p>
                                    <a href="tambah-pengaduan.php" class="btn btn-primary">
                                        Buat Pengaduan Pertama
                                    </a>
                                </td>
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