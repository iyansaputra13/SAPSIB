<?php
require_once 'cek-session.php';

// Ambil data statistik
$id_siswa = $_SESSION['id_siswa'];

$query_total = "SELECT COUNT(*) as total FROM pengaduan WHERE id_siswa = '$id_siswa'";
$result_total = mysqli_query($conn, $query_total);
$total_pengaduan = mysqli_fetch_assoc($result_total)['total'];

$query_pending = "SELECT COUNT(*) as total FROM pengaduan WHERE id_siswa = '$id_siswa' AND status = 'Pending'";
$result_pending = mysqli_query($conn, $query_pending);
$total_pending = mysqli_fetch_assoc($result_pending)['total'];

$query_proses = "SELECT COUNT(*) as total FROM pengaduan WHERE id_siswa = '$id_siswa' AND status = 'Diproses'";
$result_proses = mysqli_query($conn, $query_proses);
$total_proses = mysqli_fetch_assoc($result_proses)['total'];

$query_selesai = "SELECT COUNT(*) as total FROM pengaduan WHERE id_siswa = '$id_siswa' AND status = 'Selesai'";
$result_selesai = mysqli_query($conn, $query_selesai);
$total_selesai = mysqli_fetch_assoc($result_selesai)['total'];

// Ambil pengaduan terbaru
$query_pengaduan = "SELECT p.*, k.nama_kategori 
                    FROM pengaduan p
                    JOIN kategori_sarana k ON p.id_kategori = k.id_kategori
                    WHERE p.id_siswa = '$id_siswa'
                    ORDER BY p.tanggal_pengaduan DESC
                    LIMIT 5";
$result_pengaduan = mysqli_query($conn, $query_pengaduan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Dashboard Siswa - SAPSI</title>
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
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .stat-card h3 {
            margin: 0;
            font-size: 32px;
        }
        
        .stat-card p {
            margin: 5px 0 0 0;
            color: #666;
        }
        
        .table-container {
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
            <a href="dashboard-siswa.php" class="nav-link active">
                <i class="fa-solid fa-dashboard"></i> Dashboard
            </a>
            <a href="tambah-pengaduan.php" class="nav-link">
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
            <h4>Dashboard</h4>
            <small>Selamat datang di SAPSI</small>
        </div>

        <!-- Statistik Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <p>Total Pengaduan</p>
                    <h3><?= $total_pengaduan ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card" style="border-color: #ffc107;">
                    <p>Pending</p>
                    <h3><?= $total_pending ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card" style="border-color: #17a2b8;">
                    <p>Diproses</p>
                    <h3><?= $total_proses ?></h3>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card" style="border-color: #28a745;">
                    <p>Selesai</p>
                    <h3><?= $total_selesai ?></h3>
                </div>
            </div>
        </div>

        <!-- Tabel Pengaduan Terbaru -->
        <div class="table-container">
            <h5>Pengaduan Terbaru</h5>
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
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_pengaduan) > 0): ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result_pengaduan)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_pengaduan'])) ?></td>
                                    <td><?= $row['judul_pengaduan'] ?></td>
                                    <td><?= $row['nama_kategori'] ?></td>
                                    <td><?= $row['lokasi'] ?></td>
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
                                        <a href="detail-pengaduan.php?id=<?= $row['id_pengaduan'] ?>" class="btn btn-sm btn-info">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
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