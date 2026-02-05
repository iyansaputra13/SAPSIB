<?php
require_once 'cek-session.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: data-pengaduan.php");
    exit();
}

$id_pengaduan = mysqli_real_escape_string($conn, $_GET['id']);
$id_siswa = $_SESSION['id_siswa'];

// Ambil data pengaduan
$query = "SELECT p.*, k.nama_kategori 
          FROM pengaduan p
          JOIN kategori_sarana k ON p.id_kategori = k.id_kategori
          WHERE p.id_pengaduan = '$id_pengaduan' AND p.id_siswa = '$id_siswa'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: data-pengaduan.php");
    exit();
}

$pengaduan = mysqli_fetch_assoc($result);

// Ambil tanggapan
$query_tanggapan = "SELECT t.*, a.nama_lengkap as nama_admin 
                    FROM tanggapan t
                    JOIN admin a ON t.id_admin = a.id_admin
                    WHERE t.id_pengaduan = '$id_pengaduan'
                    ORDER BY t.tanggal_tanggapan DESC";
$result_tanggapan = mysqli_query($conn, $query_tanggapan);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Detail Pengaduan - SAPSI</title>
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
        
        .detail-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .info-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .tanggapan-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 3px solid #667eea;
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
            <a href="data-pengaduan.php" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h4 class="mt-2">Detail Pengaduan</h4>
        </div>

        <!-- Detail Pengaduan -->
        <div class="detail-box">
            <h5>Informasi Pengaduan</h5>
            <hr>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Status</div>
                    <div class="col-md-9">
                        <?php
                        $badge_class = 'bg-secondary';
                        if ($pengaduan['status'] == 'Pending') $badge_class = 'bg-warning';
                        elseif ($pengaduan['status'] == 'Diproses') $badge_class = 'bg-info';
                        elseif ($pengaduan['status'] == 'Selesai') $badge_class = 'bg-success';
                        elseif ($pengaduan['status'] == 'Ditolak') $badge_class = 'bg-danger';
                        ?>
                        <span class="badge <?= $badge_class ?>"><?= $pengaduan['status'] ?></span>
                    </div>
                </div>
            </div>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Prioritas</div>
                    <div class="col-md-9">
                        <?php
                        $priority_class = 'bg-secondary';
                        if ($pengaduan['prioritas'] == 'Tinggi') $priority_class = 'bg-danger';
                        elseif ($pengaduan['prioritas'] == 'Sedang') $priority_class = 'bg-warning';
                        ?>
                        <span class="badge <?= $priority_class ?>"><?= $pengaduan['prioritas'] ?></span>
                    </div>
                </div>
            </div>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Tanggal Pengaduan</div>
                    <div class="col-md-9"><?= date('d F Y H:i', strtotime($pengaduan['tanggal_pengaduan'])) ?></div>
                </div>
            </div>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Kategori</div>
                    <div class="col-md-9"><?= $pengaduan['nama_kategori'] ?></div>
                </div>
            </div>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Lokasi</div>
                    <div class="col-md-9"><?= $pengaduan['lokasi'] ?></div>
                </div>
            </div>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Judul</div>
                    <div class="col-md-9"><strong><?= $pengaduan['judul_pengaduan'] ?></strong></div>
                </div>
            </div>

            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Deskripsi</div>
                    <div class="col-md-9"><?= nl2br($pengaduan['deskripsi']) ?></div>
                </div>
            </div>

            <?php if (!empty($pengaduan['foto'])): ?>
            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Foto</div>
                    <div class="col-md-9">
                        <img src="../uploads/<?= $pengaduan['foto'] ?>" class="img-fluid" style="max-width: 400px;">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($pengaduan['tanggal_selesai'])): ?>
            <div class="info-row">
                <div class="row">
                    <div class="col-md-3 info-label">Tanggal Selesai</div>
                    <div class="col-md-9"><?= date('d F Y H:i', strtotime($pengaduan['tanggal_selesai'])) ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Riwayat Tanggapan -->
        <?php if (mysqli_num_rows($result_tanggapan) > 0): ?>
        <div class="detail-box">
            <h5>Riwayat Tanggapan</h5>
            <hr>

            <?php while ($tanggapan = mysqli_fetch_assoc($result_tanggapan)): ?>
                <div class="tanggapan-item">
                    <div class="d-flex justify-content-between mb-2">
                        <strong><?= $tanggapan['nama_admin'] ?></strong>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($tanggapan['tanggal_tanggapan'])) ?></small>
                    </div>
                    <p class="mb-0"><?= nl2br($tanggapan['tanggapan']) ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <!-- Tombol Aksi -->
        <?php if ($pengaduan['status'] == 'Pending'): ?>
        <div class="detail-box">
            <a href="edit-pengaduan.php?id=<?= $pengaduan['id_pengaduan'] ?>" class="btn btn-warning">
                <i class="fa-solid fa-edit"></i> Edit Pengaduan
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>