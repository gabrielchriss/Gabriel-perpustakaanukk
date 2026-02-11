<?php
require_once '../config.php';

// Cek login dan role admin
if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}

// Statistik Dashboard
$total_buku       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM buku"))['total'];
$total_anggota    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='anggota'"))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE status='dipinjam'"))['total'];
$total_terlambat  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE status='terlambat'"))['total'];

// Data peminjaman terbaru
$peminjaman_terbaru = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, b.judul_buku, b.kode_buku 
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id
    JOIN buku b ON p.id_buku = b.id
    ORDER BY p.tanggal_pinjam DESC
    LIMIT 5
");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Admin - Perpustakaan SMK</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #eef2ff;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .navbar-app {
            background: linear-gradient(120deg, #1f2937, #111827);
        }

        .navbar-app .navbar-brand,
        .navbar-app .nav-link {
            color: #e5e7eb !important;
            font-weight: 500;
        }

        .navbar-app .nav-link.active,
        .navbar-app .nav-link:hover {
            color: #ffffff !important;
        }

        .page-wrapper {
            padding: 24px 16px 32px;
        }

        @media(min-width:992px) {
            .page-wrapper {
                padding: 28px 40px 40px;
            }
        }

        .stat-card {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
            padding: 16px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .15);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #ffffff;
        }

        .card-section {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
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
            font-size: .9rem;
            vertical-align: middle;
            border-color: #e5e7eb;
        }

        .row-hover tbody tr:hover {
            background: #f9fafb;
        }

        /* PERINGATAN BERGERAK */
        .alert-ticker {
            border: none;
            border-radius: 14px;
            background: #fee2e2;
            color: #991b1b;
            box-shadow: 0 10px 26px rgba(185, 28, 28, .25);
            overflow: hidden;
        }

        .alert-ticker .blink-icon {
            animation: blink 1.2s step-start infinite;
        }

        .alert-ticker-text {
            white-space: nowrap;
            animation: ticker 25s linear infinite;
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
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-app shadow-sm">
        <div class="container-fluid px-3 px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <i class="bi bi-journal-code fs-4"></i>
                <span class="fw-semibold">Perpustakaan SMK - Admin</span>
            </a>
            <button class="navbar-toggler border-0 text-light" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kelola_buku.php"><i class="bi bi-book me-1"></i>Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kelola_anggota.php"><i class="bi bi-people me-1"></i>Anggota</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transaksi.php"><i class="bi bi-arrow-left-right me-1"></i>Transaksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="request_buku.php">
                            <i class="bi bi-megaphone-fill me-1"></i>Request Buku
                        </a>
                    </li>

                    <li class="nav-item d-none d-lg-block ms-2">
                        <span class="text-light small me-2">
                            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
                        </span>
                        <a href="../logout.php" class="btn btn-outline-light btn-sm rounded-pill">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Dashboard Admin</h4>
                <small class="text-muted">Ringkasan cepat aktivitas perpustakaan hari ini.</small>
            </div>
            <span class="badge rounded-pill text-bg-light">
                <i class="bi bi-calendar-week me-1 text-primary"></i><?php echo date('d M Y'); ?>
            </span>
        </div>

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div>
                        <div class="text-muted small mb-1">Total Buku</div>
                        <div class="fs-3 fw-semibold"><?php echo $total_buku; ?></div>
                    </div>
                    <div class="stat-icon" style="background:#3b82f6;">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div>
                        <div class="text-muted small mb-1">Total Anggota</div>
                        <div class="fs-3 fw-semibold"><?php echo $total_anggota; ?></div>
                    </div>
                    <div class="stat-icon" style="background:#22c55e;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div>
                        <div class="text-muted small mb-1">Sedang Dipinjam</div>
                        <div class="fs-3 fw-semibold"><?php echo $total_peminjaman; ?></div>
                    </div>
                    <div class="stat-icon" style="background:#facc15;">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div>
                        <div class="text-muted small mb-1">Terlambat</div>
                        <div class="fs-3 fw-semibold"><?php echo $total_terlambat; ?></div>
                    </div>
                    <div class="stat-icon" style="background:#ef4444;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peminjaman Terbaru -->
        <div class="card-section mb-4">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-clock-history me-1 text-primary"></i>Peminjaman Terbaru
                </h6>
                <small class="text-muted">5 transaksi terakhir</small>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-modern row-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Judul Buku</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($peminjaman_terbaru) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($peminjaman_terbaru)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['kode_buku']); ?></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['judul_buku']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                        <td>
                                            <?php
                                            $badge = 'success';
                                            if ($row['status'] == 'terlambat') $badge = 'danger';
                                            elseif ($row['status'] == 'dipinjam') $badge = 'warning';
                                            ?>
                                            <span class="badge text-bg-<?php echo $badge; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted small">
                                        Belum ada data peminjaman.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Peringatan Admin -->
        <div class="mt-3">
            <div class="alert-ticker d-flex align-items-center px-3 py-2">
                <i class="bi bi-exclamation-triangle-fill me-2 blink-icon"></i>
                <div class="overflow-hidden flex-grow-1">
                    <div class="small alert-ticker-text">
                        PERINGATAN SISTEM: Admin wajib memantau peminjaman yang mendekati jatuh tempo,
                        menindaklanjuti keterlambatan, serta memastikan stok buku dan data anggota selalu
                        ter-update. Segera laporkan jika ditemukan aktivitas yang mencurigakan atau data
                        transaksi yang tidak sesuai.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>