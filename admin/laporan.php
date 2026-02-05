<?php
require_once 'cek-session.php';

// Filter
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Build query dengan filter
$where = "WHERE YEAR(p.tanggal_pengaduan) = '$filter_tahun'";

if (!empty($filter_bulan)) {
    $where .= " AND MONTH(p.tanggal_pengaduan) = '$filter_bulan'";
}

if (!empty($filter_status)) {
    $where .= " AND p.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

if (!empty($filter_kategori)) {
    $where .= " AND p.id_kategori = '" . mysqli_real_escape_string($conn, $filter_kategori) . "'";
}

// Ambil data pengaduan
$query_laporan = "SELECT p.*, s.nama_lengkap, s.nis, s.kelas, k.nama_kategori 
                  FROM pengaduan p
                  JOIN siswa s ON p.id_siswa = s.id_siswa
                  JOIN kategori_sarana k ON p.id_kategori = k.id_kategori
                  $where
                  ORDER BY p.tanggal_pengaduan DESC";
$result_laporan = mysqli_query($conn, $query_laporan);

// Hitung statistik
$stat_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Diproses' THEN 1 ELSE 0 END) as diproses,
    SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as selesai,
    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak
    FROM pengaduan p $where";
$stat_result = mysqli_query($conn, $stat_query);
$stats = mysqli_fetch_assoc($stat_result);

// Ambil data kategori
$query_kat = "SELECT * FROM kategori_sarana ORDER BY nama_kategori ASC";
$result_kat = mysqli_query($conn, $query_kat);

$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <title>Laporan - SAPSI</title>
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
        
        .filter-box, .stat-box, .table-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        /* CSS untuk Print */
        @media print {
            body * {
                visibility: hidden;
            }
            .print-section, .print-section * {
                visibility: visible;
            }
            .print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .no-print {
                display: none !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .badge {
                border: 1px solid #000 !important;
                color: #000 !important;
                background-color: transparent !important;
                padding: 4px 8px;
            }
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .print-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar no-print">
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
            <a href="kelola-kategori.php" class="nav-link">
                <i class="fa-solid fa-list"></i> Kelola Kategori
            </a>
            <a href="laporan.php" class="nav-link active">
                <i class="fa-solid fa-chart-bar"></i> Laporan
            </a>
            <a href="logout.php" class="nav-link" onclick="return confirm('Yakin ingin logout?')">
                <i class="fa-solid fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar no-print">
            <h4>Laporan Pengaduan</h4>
            <small>Laporan dan statistik pengaduan sarana</small>
        </div>

        <!-- Filter -->
        <div class="filter-box no-print">
            <h6>Filter Laporan</h6>
            <hr>
            <form method="GET">
                <div class="row">
                    <div class="col-md-2">
                        <label>Bulan</label>
                        <select name="bulan" class="form-select">
                            <option value="">Semua</option>
                            <?php foreach ($bulan_indo as $num => $nama): ?>
                                <option value="<?= $num ?>" <?= $filter_bulan == $num ? 'selected' : '' ?>><?= $nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Tahun</label>
                        <select name="tahun" class="form-select">
                            <?php for ($i = 2020; $i <= date('Y'); $i++): ?>
                                <option value="<?= $i ?>" <?= $filter_tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Diproses" <?= $filter_status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Ditolak" <?= $filter_status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="">Semua</option>
                            <?php mysqli_data_seek($result_kat, 0); while ($kat = mysqli_fetch_assoc($result_kat)): ?>
                                <option value="<?= $kat['id_kategori'] ?>" <?= $filter_kategori == $kat['id_kategori'] ? 'selected' : '' ?>>
                                    <?= $kat['nama_kategori'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Print Section -->
        <div class="print-section" style="display: none;">
            <div class="print-header">
                <h2>LAPORAN PENGADUAN SARANA</h2>
                <h5>SAPSI - Sistem Aduan Pengaduan Sarana Sekolah</h5>
                <hr>
                <p>Periode: 
                    <?= 
                        (!empty($filter_bulan) ? $bulan_indo[$filter_bulan] . ' ' : '') . 
                        $filter_tahun . 
                        ($filter_status ? ' | Status: ' . $filter_status : '') .
                        ($filter_kategori ? ' | Kategori: ' . mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_kategori FROM kategori_sarana WHERE id_kategori = '$filter_kategori'"))['nama_kategori'] : '')
                    ?>
                </p>
                <p>Tanggal Cetak: <?= date('d/m/Y H:i:s') ?></p>
            </div>
            
            <div class="print-info">
                <p><strong>Statistik:</strong></p>
                <p>Total: <?= $stats['total'] ?> | Pending: <?= $stats['pending'] ?> | 
                   Diproses: <?= $stats['diproses'] ?> | Selesai: <?= $stats['selesai'] ?> | 
                   Ditolak: <?= $stats['ditolak'] ?></p>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($result_laporan, 0);
                        $no = 1; 
                        while ($row = mysqli_fetch_assoc($result_laporan)): 
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_pengaduan'])) ?></td>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td><?= $row['kelas'] ?></td>
                                <td><?= $row['judul_pengaduan'] ?></td>
                                <td><?= $row['nama_kategori'] ?></td>
                                <td>
                                    <span class="badge">
                                        <?= $row['prioritas'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 pt-4 border-top">
                <div class="row">
                    <div class="col-6 text-center">
                        <p>Yang Membuat Laporan,</p>
                        <br><br><br>
                        <p><strong><?= $_SESSION['nama_admin'] ?></strong><br>
                        Administrator</p>
                    </div>
                    <div class="col-6 text-center">
                        <p>Mengetahui,</p>
                        <br><br><br>
                        <p><strong>Kepala Sekolah</strong><br>
                        SMP Negeri 1 Contoh</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="stat-box no-print">
            <h6>Statistik</h6>
            <hr>
            <div class="row">
                <div class="col-md-2">
                    <div class="stat-item">
                        <h4><?= $stats['total'] ?></h4>
                        <small>Total</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-item">
                        <h4><?= $stats['pending'] ?></h4>
                        <small>Pending</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-item">
                        <h4><?= $stats['diproses'] ?></h4>
                        <small>Diproses</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-item">
                        <h4><?= $stats['selesai'] ?></h4>
                        <small>Selesai</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stat-item">
                        <h4><?= $stats['ditolak'] ?></h4>
                        <small>Ditolak</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger w-100" style="margin-top: 20px;" onclick="printLaporan()">
                        <i class="fa-solid fa-print"></i> Cetak Laporan
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="table-box no-print">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Data Pengaduan</h6>
                <button class="btn btn-sm btn-outline-secondary" onclick="printLaporan()">
                    <i class="fa-solid fa-print"></i> Cetak
                </button>
            </div>
            <hr>

            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Prioritas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result_laporan) > 0): ?>
                            <?php 
                            mysqli_data_seek($result_laporan, 0);
                            $no = 1; 
                            while ($row = mysqli_fetch_assoc($result_laporan)): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><small><?= date('d/m/Y', strtotime($row['tanggal_pengaduan'])) ?></small></td>
                                    <td>
                                        <small><strong><?= $row['nama_lengkap'] ?></strong><br>
                                        <?= $row['kelas'] ?></small>
                                    </td>
                                    <td><small><?= $row['judul_pengaduan'] ?></small></td>
                                    <td><small><?= $row['nama_kategori'] ?></small></td>
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
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function printLaporan() {
            // Tampilkan section print
            const printSection = document.querySelector('.print-section');
            printSection.style.display = 'block';
            
            // Cetak
            window.print();
            
            // Sembunyikan kembali setelah cetak
            setTimeout(() => {
                printSection.style.display = 'none';
            }, 500);
        }
        
        // Deteksi saat pencetakan selesai
        window.addEventListener('afterprint', function() {
            const printSection = document.querySelector('.print-section');
            printSection.style.display = 'none';
        });
        
        // Keyboard shortcut Ctrl+P
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printLaporan();
            }
        });
    </script>
</body>
</html>