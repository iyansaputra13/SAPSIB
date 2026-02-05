<?php
require_once 'cek-session.php';

// Filter
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_prioritas = isset($_GET['prioritas']) ? $_GET['prioritas'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query dengan filter
$query = "SELECT p.*, s.nama_lengkap, s.kelas, s.nis, k.nama_kategori 
          FROM pengaduan p
          JOIN siswa s ON p.id_siswa = s.id_siswa
          JOIN kategori_sarana k ON p.id_kategori = k.id_kategori
          WHERE 1=1";

if (!empty($filter_status)) {
    $query .= " AND p.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

if (!empty($filter_prioritas)) {
    $query .= " AND p.prioritas = '" . mysqli_real_escape_string($conn, $filter_prioritas) . "'";
}

if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query .= " AND (p.judul_pengaduan LIKE '%$search_escaped%' 
                OR s.nama_lengkap LIKE '%$search_escaped%' 
                OR p.lokasi LIKE '%$search_escaped%')";
}

$query .= " ORDER BY 
            CASE p.status 
                WHEN 'Pending' THEN 1
                WHEN 'Diproses' THEN 2
                WHEN 'Selesai' THEN 3
                WHEN 'Ditolak' THEN 4
            END,
            CASE p.prioritas
                WHEN 'Tinggi' THEN 1
                WHEN 'Sedang' THEN 2
                WHEN 'Rendah' THEN 3
            END,
            p.tanggal_pengaduan DESC";

$result = mysqli_query($conn, $query);

// Hitung statistik
$stat_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak
    FROM pengaduan";
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
    <title>Kelola Pengaduan - SAPSI</title>
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
        
        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .stat-badge {
            display: inline-block;
            padding: 8px 12px;
            margin-right: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            font-size: 14px;
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
            <a href="kelola-pengaduan.php" class="nav-link active">
                <i class="fa-solid fa-tasks"></i> Kelola Pengaduan
            </a>
            <a href="kelola-siswa.php" class="nav-link">
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
            <h4>Kelola Pengaduan</h4>
            <small>Kelola dan proses semua pengaduan dari siswa</small>
        </div>

        <!-- Statistik Mini -->
        <div class="mb-3">
            <span class="stat-badge bg-primary text-white">Total: <?= $stats['total'] ?></span>
            <span class="stat-badge bg-warning text-dark">Pending: <?= $stats['pending'] ?></span>
            <span class="stat-badge bg-info text-white">Diproses: <?= $stats['diproses'] ?></span>
            <span class="stat-badge bg-success text-white">Selesai: <?= $stats['selesai'] ?></span>
            <span class="stat-badge bg-danger text-white">Ditolak: <?= $stats['ditolak'] ?></span>
        </div>

        <!-- Filter -->
        <div class="filter-box">
            <h6>Filter Pengaduan</h6>
            <hr>
            <form method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Diproses" <?= $filter_status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Ditolak" <?= $filter_status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Prioritas</label>
                        <select name="prioritas" class="form-select">
                            <option value="">Semua Prioritas</option>
                            <option value="Tinggi" <?= $filter_prioritas == 'Tinggi' ? 'selected' : '' ?>>Tinggi</option>
                            <option value="Sedang" <?= $filter_prioritas == 'Sedang' ? 'selected' : '' ?>>Sedang</option>
                            <option value="Rendah" <?= $filter_prioritas == 'Rendah' ? 'selected' : '' ?>>Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Pencarian</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari judul, nama, lokasi..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
            <?php if (!empty($filter_status) || !empty($filter_prioritas) || !empty($search)): ?>
                <a href="kelola-pengaduan.php" class="btn btn-sm btn-secondary">Reset Filter</a>
            <?php endif; ?>
        </div>

        <!-- Tabel Pengaduan -->
        <div class="table-container">
            <h5>Daftar Pengaduan (<?= mysqli_num_rows($result) ?>)</h5>
            <hr>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
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
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($row['tanggal_pengaduan'])) ?></small><br>
                                        <small class="text-muted"><?= date('H:i', strtotime($row['tanggal_pengaduan'])) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= $row['nama_lengkap'] ?></strong><br>
                                        <small><?= $row['nis'] ?> | <?= $row['kelas'] ?></small>
                                    </td>
                                    <td><?= $row['judul_pengaduan'] ?></td>
                                    <td><small><?= $row['nama_kategori'] ?></small></td>
                                    <td><small><?= $row['lokasi'] ?></small></td>
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
                                        <a href="detail-pengaduan-admin.php?id=<?= $row['id_pengaduan'] ?>" 
                                           class="btn btn-sm btn-primary">Proses</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    <?php if (!empty($search) || !empty($filter_status) || !empty($filter_prioritas)): ?>
                                        Tidak ada pengaduan yang sesuai
                                    <?php else: ?>
                                        Belum ada pengaduan
                                    <?php endif; ?>
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