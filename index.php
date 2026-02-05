<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fontawesome/css/all.css">
    <title>SAPSI - Sistem Pengaduan Sarana Sekolah</title>
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #198754;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .hero {
            min-height: calc(100vh - 76px);
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
            padding: 4rem 0;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
        }

        .btn-lg {
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-lg:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
        }

        .btn-outline-light {
            border: 2px solid white;
            color: white;
            background: transparent;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--primary-color);
        }

        .hero-image {
            position: relative;
            z-index: 1;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .features-section {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #666;
        }

        footer {
            background: #2c3e50;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .btn-lg {
                width: 100%;
                margin-bottom: 1rem;
            }

            .hero {
                min-height: auto;
                padding: 3rem 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class='navbar-brand fw-bold' href="#">
                <i class="fa-solid fa-school"></i> SAPSI
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center hero-content">
                <div class="col-md-6 mb-4">
                    <h1 class="fw-bold">Sistem Pengaduan Sarana Sekolah</h1>
                    <p class="mt-3">
                        Platform digital untuk memudahkan siswa melaporkan kerusakan atau kekurangan sarana dan prasarana sekolah secara cepat dan efisien.
                    </p>
                    <div class="mt-4">
                        <a href="siswa/login-siswa.php" class="btn btn-success btn-lg me-2">
                            <i class="fa-solid fa-graduation-cap"></i> Login Siswa
                        </a>
                        <a href="admin/login.php" class="btn btn-outline-light btn-lg">
                            <i class="fa-solid fa-user"></i> Login Admin
                        </a>
                    </div>
                </div>
                <div class="col-md-6 hero-image text-center">
                    <i class="fa-solid fa-chalkboard-user" style="font-size: 15rem; color: rgba(255,255,255,0.3);"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Fitur Unggulan</h2>
                <p>Kemudahan dalam melaporkan dan mengelola pengaduan sarana sekolah</p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Laporan Cepat</h4>
                        <p class="text-muted">Siswa dapat melaporkan kerusakan sarana dengan mudah dan cepat melalui sistem online.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Monitoring Real-time</h4>
                        <p class="text-muted">Admin dapat memantau status pengaduan secara real-time dan memberikan tindak lanjut.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Aman & Terpercaya</h4>
                        <p class="text-muted">Data pengaduan tersimpan dengan aman dan dapat dipertanggungjawabkan.</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Notifikasi</h4>
                        <p class="text-muted">Dapatkan notifikasi untuk setiap update status pengaduan yang diajukan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fa-solid fa-file-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Riwayat Lengkap</h4>
                        <p class="text-muted">Akses riwayat pengaduan secara lengkap dengan sistem arsip digital.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h4 class="fw-bold mb-3">User Friendly</h4>
                        <p class="text-muted">Interface yang mudah digunakan untuk semua kalangan pengguna.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> SAPSI - Sistem Pengaduan Sarana Sekolah. All Rights Reserved.</p>
            <p class="mb-0 mt-2">
                <small>Developed for LSP Certification</small>
            </p>
        </div>
    </footer>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>