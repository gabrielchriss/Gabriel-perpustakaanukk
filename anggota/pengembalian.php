<?php
require_once '../config.php';

if (!is_logged_in() || !is_anggota()) {
    redirect('../index.php');
}

$user_id = $_SESSION['user_id'];

// data buku yang sedang dipinjam
$peminjaman_aktif = mysqli_query($conn, "
    SELECT p.*, b.judul_buku, b.kode_buku, b.pengarang, b.kategori 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.id_user = '$user_id' AND p.status = 'dipinjam'
    ORDER BY p.tanggal_pinjam DESC
");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Pengembalian Buku - Perpustakaan SMK</title>
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

        .navbar-school .nav-link.active,
        .navbar-school .nav-link:hover {
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

        .card-main {
            border: none;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .1);
            overflow: hidden;
            background: #ffffff;
        }

        .card-main-header {
            padding: 14px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        @media(min-width:768px) {
            .card-main-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
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

        .pill-status {
            border-radius: 999px;
            padding: .22rem .85rem;
            font-size: .75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pill-status.safe {
            background: #dcfce7;
            color: #166534;
        }

        .pill-status.soon {
            background: #fef9c3;
            color: #854d0e;
        }

        .pill-status.late {
            background: #fee2e2;
            color: #b91c1c;
        }

        .step-card {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
        }

        .step-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 18px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-school shadow-sm sticky-top">
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
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill me-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php"><i class="bi bi-journal-bookmark-fill me-1"></i>Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pengembalian.php"><i class="bi bi-arrow-return-left me-1"></i>Pengembalian</a>
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


                    <li class="nav-item d-none d-lg-block ms-2">
                        <a class="btn btn-outline-light btn-sm rounded-pill" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">

        <!-- CARD BUKU DIPINJAM -->
        <div class="card-main mb-4">
            <div class="card-main-header">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        <i class="bi bi-journal-arrow-up text-primary me-1"></i> Buku Yang Sedang Dipinjam
                    </h5>
                    <small class="text-muted">
                        Daftar buku yang masih berada di tangan kamu beserta status jatuh temponya.
                    </small>
                </div>
                <div class="mt-2 mt-md-0">
                    <a href="peminjaman.php" class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> Pinjam Buku Lagi
                    </a>
                </div>
            </div>

            <div class="card-body">
                <?php if (mysqli_num_rows($peminjaman_aktif) > 0): ?>
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Pengembalian dilakukan langsung ke petugas perpustakaan. Status di sistem akan di-update oleh petugas.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern row-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th>Kategori</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tgl Harus Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($peminjaman_aktif)):
                                    $today     = strtotime(date('Y-m-d'));
                                    $due_date  = strtotime($row['tanggal_kembali']);
                                    $diff_days = ($due_date - $today) / (60 * 60 * 24);

                                    if ($diff_days < 0) {
                                        $cls  = 'late';
                                        $text = 'Terlambat ' . abs($diff_days) . ' hari';
                                        $icon = 'bi-exclamation-triangle-fill';
                                    } elseif ($diff_days <= 2) {
                                        $cls  = 'soon';
                                        $text = 'Segera dikembalikan';
                                        $icon = 'bi-alarm-fill';
                                    } else {
                                        $cls  = 'safe';
                                        $text = 'Masih ' . $diff_days . ' hari';
                                        $icon = 'bi-check-circle-fill';
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><span class="badge bg-secondary rounded-pill"><?php echo $row['kode_buku']; ?></span></td>
                                        <td class="fw-semibold"><?php echo $row['judul_buku']; ?></td>
                                        <td><?php echo $row['pengarang']; ?></td>
                                        <td><span class="badge bg-info text-dark"><?php echo $row['kategori']; ?></span></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                        <td>
                                            <span class="pill-status <?php echo $cls; ?>">
                                                <i class="bi <?php echo $icon; ?>"></i><?php echo $text; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success small mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Saat ini kamu tidak memiliki buku yang sedang dipinjam.
                        <a href="peminjaman.php" class="alert-link">Pinjam buku sekarang</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- CARD TATA CARA PENGEMBALIAN -->
        <div class="step-card mt-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill"></i>
                    Panduan Pengembalian Buku
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    Ikuti langkah berikut agar proses pengembalian buku berjalan cepat dan tercatat dengan benar di sistem.
                </p>

                <div class="row g-3 g-md-4">
                    <!-- Step 1 -->
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="step-icon flex-shrink-0">
                                <span>1</span>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Siapkan buku yang akan dikembalikan</div>
                                <p class="mb-0 small text-muted">
                                    Periksa kembali jumlah dan kondisi buku. Pastikan tidak ada halaman yang hilang atau rusak berat.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="step-icon flex-shrink-0">
                                <span>2</span>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Datang ke ruang perpustakaan</div>
                                <p class="mb-0 small text-muted">
                                    Bawa buku ke perpustakaan pada jam layanan. Antri dengan tertib jika ada siswa lain yang dilayani.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="step-icon flex-shrink-0">
                                <span>3</span>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Serahkan buku ke petugas</div>
                                <p class="mb-0 small text-muted">
                                    Sampaikan nama dan NIS/NIM kamu. Petugas akan mengecek data peminjaman dan kondisi buku.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="col-md-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="step-icon flex-shrink-0">
                                <span>4</span>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1">Konfirmasi status pengembalian</div>
                                <p class="mb-0 small text-muted">
                                    Setelah diproses, status peminjaman di sistem akan menjadi <strong>Dikembalikan</strong>.
                                    Jika ada keterlambatan, ikuti arahan petugas terkait sanksi atau denda.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div class="small text-muted">
                        <i class="bi bi-exclamation-circle text-warning me-1"></i>
                        Pengembalian tepat waktu membantu siswa lain mendapat giliran meminjam buku yang sama.
                    </div>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-arrow-left-short"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>


</html>