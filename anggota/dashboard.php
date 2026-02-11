<?php
require_once '../config.php';
if (!is_logged_in() || !is_anggota()) redirect('../index.php');

$user_id = $_SESSION['user_id'];

// peminjaman aktif
$peminjaman_aktif = mysqli_query($conn, "
    SELECT p.*, b.judul_buku, b.kode_buku, b.pengarang 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.id_user = '$user_id' AND p.status = 'dipinjam'
    ORDER BY p.tanggal_pinjam DESC
");

// riwayat
$riwayat = mysqli_query($conn, "
    SELECT p.*, b.judul_buku, b.kode_buku 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.id_user = '$user_id' AND p.status = 'dikembalikan'
    ORDER BY p.tanggal_dikembalikan DESC
    LIMIT 5
");

$jml_aktif   = mysqli_num_rows($peminjaman_aktif);
$jml_riwayat = mysqli_num_rows($riwayat);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Anggota - Perpustakaan SMK</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #eef2ff;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .navbar-school {
            background: linear-gradient(120deg, #4f46e5, #7c3aed);
        }

        .navbar-school .navbar-brand,
        .navbar-school .nav-link {
            color: #e5e7eb !important;
            font-weight: 500;
        }

        .navbar-school .nav-link.active {
            color: #ffffff !important;
        }

        .navbar-school .nav-link:hover {
            color: #ffffff !important;
            opacity: .9;
        }

        .navbar-school .avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #eef2ff;
            color: #4f46e5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, .2);
        }

        .dropdown-menu {
            border: none;
            border-radius: 14px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .35);
        }

        .page-wrapper {
            padding: 24px 16px 32px;
        }

        @media (min-width:992px) {
            .page-wrapper {
                padding: 28px 40px 40px;
            }
        }

        .card-soft {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            transform: translateY(12px);
            opacity: 0;
            animation: floatIn .6s ease-out forwards;
        }

        .card-soft.delay-1 {
            animation-delay: .12s;
        }

        .card-soft.delay-2 {
            animation-delay: .22s;
        }

        .card-soft-header {
            border-radius: 18px 18px 0 0;
            padding: 14px 20px;
            color: #ffffff;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-card {
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            box-shadow: 0 18px 40px rgba(79, 70, 229, .45);
            position: relative;
            overflow: hidden;
            transform: translateY(10px);
            opacity: 0;
            animation: floatIn .55s ease-out .05s forwards;
        }

        .stat-card:before {
            content: "";
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            top: -40px;
            right: -35px;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, .18);
            font-size: 26px;
        }

        .table-modern thead {
            background: #0f172a;
            color: #e5e7eb;
        }

        .table-modern thead th {
            border: none;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .table-modern tbody td {
            vertical-align: middle;
            font-size: .9rem;
            border-color: #e5e7eb;
        }

        .pill-status {
            border-radius: 999px;
            padding: .22rem .85rem;
            font-size: .75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pill-status.active {
            background: #dcfce7;
            color: #166534;
        }

        .pill-status.late {
            background: #fee2e2;
            color: #b91c1c;
        }

        .pill-status.info {
            background: #e0f2fe;
            color: #0369a1;
        }

        .empty-state {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .empty-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-size: 26px;
        }

        .hover-lift {
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 22px 40px rgba(15, 23, 42, .18);
        }

        .headline-sub {
            font-size: .88rem;
            color: #6b7280;
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* PERINGATAN BERGERAK UNTUK ANGGOTA */
        .alert-ticker {
            border: none;
            border-radius: 14px;
            background: #fee2e2;
            color: #991b1b;
            box-shadow: 0 10px 26px rgba(185, 28, 28, .18);
            overflow: hidden;
        }

        .alert-ticker .blink-icon {
            animation: blink 1.2s step-start infinite;
        }

        .alert-ticker-text {
            white-space: nowrap;
            animation: ticker 25s linear infinite;
            font-size: .82rem;
        }

        @keyframes blink {
            50% {
                opacity: 0.25;
            }
        }

        @keyframes ticker {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* SOSIAL MEDIA PERPUSTAKAAN */
        .social-card {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
        }

        .social-icon-wrap {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
        }

        .social-icon-wrap.facebook {
            background: #1877f2;
        }

        .social-icon-wrap.instagram {
            background: radial-gradient(circle at 30% 30%, #fdf497 0, #fd5949 45%, #d6249f 60%, #285AEB 90%);
        }

        .social-icon-wrap.youtube {
            background: #ff0000;
        }

        .social-icon-wrap.whatsapp {
            background: #22c55e;
        }

        .social-link {
            text-decoration: none;
            color: inherit;
        }

        .social-link:hover .social-icon-wrap {
            transform: translateY(-3px) rotate(8deg);
            box-shadow: 0 16px 32px rgba(15, 23, 42, .25);
            filter: brightness(1.1);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-school shadow-sm">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <i class="bi bi-mortarboard-fill fs-4"></i>
                <span class="fw-semibold">Perpustakaan SMK</span>
            </a>
            <button class="navbar-toggler border-0 text-light" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-house-door-fill me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php">
                            <i class="bi bi-journal-bookmark-fill me-1"></i>Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pengembalian.php">
                            <i class="bi bi-arrow-return-left me-1"></i>Pengembalian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="request_buku.php">
                            <i class="bi bi-megaphone-fill me-1"></i>Request Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">
                            <i class="bi bi-building me-1"></i>Profil
                        </a>
                    </li>


                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <div class="avatar-circle">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="d-none d-sm-inline">
                                <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-2">
                            <li class="px-3 pt-2 pb-1 small text-muted">
                                Login sebagai <strong>Anggota</strong>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger d-flex align-items-center gap-2" href="../logout.php">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">

        <!-- HEADER + STAT -->
        <div class="row g-3 mb-3 align-items-stretch">
            <div class="col-lg-8">
                <div class="card-soft hover-lift p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="headline-sub">Selamat datang kembali,</div>
                            <h3 class="fw-semibold mb-1">
                                <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!
                            </h3>
                            <p class="headline-sub mb-0">
                                Pantau semua aktivitas peminjaman bukumu di dashboard ini.
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill text-bg-light">
                                <i class="bi bi-calendar-week me-1 text-primary"></i>
                                <?php echo date('d M Y'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="stat-card p-3 d-flex align-items-center hover-lift">
                    <div class="stat-icon me-3">
                        <i class="bi bi-book-half text-white"></i>
                    </div>
                    <div>
                        <div class="small text-white-50">Buku yang sedang dipinjam</div>
                        <div class="fs-3 fw-semibold"><?php echo $jml_aktif; ?></div>
                        <div class="small text-white-50">
                            <?php echo $jml_riwayat; ?> buku sudah kamu kembalikan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="row g-4">
            <!-- BUKU SEDANG DIPINJAM -->
            <div class="col-12">
                <div class="card-soft hover-lift delay-1">
                    <div class="card-soft-header" style="background:linear-gradient(120deg,#2563eb,#1d4ed8);">
                        <i class="bi bi-bookmark-star-fill"></i>
                        <span>Buku Yang Sedang Dipinjam</span>
                    </div>
                    <div class="card-body">
                        <?php if ($jml_aktif > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Judul Buku</th>
                                            <th>Pengarang</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Harus Kembali</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        mysqli_data_seek($peminjaman_aktif, 0);
                                        while ($row = mysqli_fetch_assoc($peminjaman_aktif)):
                                            $today = strtotime(date('Y-m-d'));
                                            $due   = strtotime($row['tanggal_kembali']);
                                            $diff  = ($due - $today) / (60 * 60 * 24);
                                            if ($diff < 0) {
                                                $pillClass = 'late';
                                                $label     = 'Terlambat ' . abs($diff) . ' hari';
                                                $icon      = 'bi-exclamation-triangle-fill';
                                            } elseif ($diff <= 2) {
                                                $pillClass = 'info';
                                                $label     = 'Segera dikembalikan';
                                                $icon      = 'bi-alarm-fill';
                                            } else {
                                                $pillClass = 'active';
                                                $label     = 'Aktif';
                                                $icon      = 'bi-check-circle-fill';
                                            }
                                        ?>
                                            <tr>
                                                <td><span class="badge bg-secondary rounded-pill"><?php echo $row['kode_buku']; ?></span></td>
                                                <td class="fw-semibold"><?php echo $row['judul_buku']; ?></td>
                                                <td><?php echo $row['pengarang']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                                <td>
                                                    <span class="pill-status <?php echo $pillClass; ?>">
                                                        <i class="bi <?php echo $icon; ?>"></i>
                                                        <?php echo $label; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-emoji-smile"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Belum ada buku yang kamu pinjam.</div>
                                    <div class="headline-sub mb-1">
                                        Mulai eksplor koleksi perpustakaan di menu <strong>Peminjaman</strong>.
                                    </div>
                                    <a href="peminjaman.php" class="btn btn-sm btn-primary rounded-pill">
                                        <i class="bi bi-search me-1"></i>Cari Buku
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT PENGEMBALIAN -->
            <div class="col-12">
                <div class="card-soft hover-lift delay-2">
                    <div class="card-soft-header" style="background:linear-gradient(120deg,#16a34a,#15803d);">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Pengembalian</span>
                    </div>
                    <div class="card-body">
                        <?php if ($jml_riwayat > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Judul Buku</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Dikembalikan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        mysqli_data_seek($riwayat, 0);
                                        while ($row = mysqli_fetch_assoc($riwayat)): ?>
                                            <tr>
                                                <td><span class="badge bg-secondary rounded-pill"><?php echo $row['kode_buku']; ?></span></td>
                                                <td class="fw-semibold"><?php echo $row['judul_buku']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_dikembalikan'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="headline-sub">
                                Belum ada riwayat pengembalian. Setelah kamu mengembalikan buku,
                                daftar pengembalian akan muncul di sini.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- SOSIAL MEDIA PERPUSTAKAAN -->
            <div class="col-12 mt-3">
                <div class="social-card p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h6 class="fw-semibold mb-1">
                                <i class="bi bi-share-fill text-primary me-1"></i>
                                Ikuti Sosial Media Perpustakaan
                            </h6>
                            <p class="headline-sub mb-0">
                                Dapatkan info buku baru, jadwal layanan, dan pengumuman penting melalui akun resmi perpustakaan.
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="https://www.facebook.com" class="social-link" target="_blank" rel="noopener noreferrer">
                                <div class="social-icon-wrap facebook">
                                    <i class="bi bi-facebook"></i>
                                </div>
                            </a>
                            <a href="https://www.instagram.com/gabrielchriss.k/?hl=en" class="social-link" target="_blank" rel="noopener noreferrer">
                                <div class="social-icon-wrap instagram">
                                    <i class="bi bi-instagram"></i>
                                </div>
                            </a>
                            <a href="https://youtube.com/@gabrielchriss?si=T5-wq456x-yK8ew1" class="social-link" target="_blank" rel="noopener noreferrer">
                                <div class="social-icon-wrap youtube">
                                    <i class="bi bi-youtube"></i>
                                </div>
                            </a>
                            <a href="https://wa.me/6281387447874" class="social-link" target="_blank" rel="noopener noreferrer">
                                <div class="social-icon-wrap whatsapp">
                                    <i class="bi bi-whatsapp"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERINGATAN UNTUK ANGGOTA -->
            <div class="col-12 mt-3">
                <div class="alert-ticker d-flex align-items-center px-3 py-2">
                    <i class="bi bi-exclamation-triangle-fill me-2 blink-icon"></i>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="alert-ticker-text">
                            PERINGATAN: Selalu periksa kembali masa peminjaman bukumu. Usahakan mengembalikan buku
                            sebelum tanggal jatuh tempo agar terhindar dari keterlambatan dan membantu anggota lain
                            mendapat giliran meminjam buku yang sama.
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- end row/content -->

    </div> <!-- end .page-wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>