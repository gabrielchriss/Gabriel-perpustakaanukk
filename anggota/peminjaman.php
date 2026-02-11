<?php
require_once '../config.php';

if (!is_logged_in() || !is_anggota()) {
    redirect('../index.php');
}

$user_id = $_SESSION['user_id'];
$success = '';
$error   = '';

// PROSES PEMINJAMAN
if (isset($_POST['pinjam_buku'])) {
    $id_buku         = $_POST['id_buku'];
    $tanggal_pinjam  = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));

    $buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id='$id_buku'"));
    if ($buku && $buku['stok_tersedia'] > 0) {
        mysqli_query($conn, "INSERT INTO peminjaman (id_user,id_buku,tanggal_pinjam,tanggal_kembali)
                            VALUES ('$user_id','$id_buku','$tanggal_pinjam','$tanggal_kembali')");
        mysqli_query($conn, "UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id='$id_buku'");
        $success = 'Buku berhasil dipinjam! Dikembalikan sebelum ' . date('d/m/Y', strtotime($tanggal_kembali));
    } else {
        $error = 'Maaf, stok buku tidak tersedia.';
    }
}

// DATA BUKU
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$where  = $search
    ? "WHERE (judul_buku LIKE '%$search%' OR pengarang LIKE '%$search%' OR kategori LIKE '%$search%') AND stok_tersedia > 0"
    : "WHERE stok_tersedia > 0";

$buku = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY judul_buku ASC");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Peminjaman Buku - Perpustakaan SMK</title>
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

        .card-header-main {
            border-radius: 12px 12px 0 0;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 20px;
        }

        .card-main {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        /* SEARCH KECIL */
        .search-small {
            max-width: 220px;
        }

        .search-small input {
            font-size: .82rem;
            padding-left: 2.0rem;
            border-radius: 999px;
        }

        .search-small .icon {
            position: absolute;
            left: .65rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: .85rem;
        }

        /* GRID BUKU PERSEGI */
        .book-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 14px 14px 12px;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .book-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 26px rgba(15, 23, 42, .16);
            border-color: #4f46e5;
        }

        .book-title {
            font-size: .95rem;
            font-weight: 600;
            margin-bottom: 3px;
            min-height: 2.4em;
            /* supaya tinggi judul konsisten */
        }

        .book-meta {
            font-size: .8rem;
            color: #6b7280;
            line-height: 1.3;
        }

        .book-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .badge-code {
            font-size: .72rem;
            border-radius: 999px;
        }

        @media (max-width:575.98px) {
            .book-title {
                min-height: auto;
            }
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
                        <a class="nav-link active" href="peminjaman.php"><i class="bi bi-journal-bookmark-fill me-1"></i>Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pengembalian.php"><i class="bi bi-arrow-return-left me-1"></i>Pengembalian</a>
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

        <div class="card card-main mb-3">
            <div class="card-header-main d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        <i class="bi bi-book-half text-primary me-1"></i> Peminjaman Buku
                    </h5>
                    <small class="text-muted">
                        Pilih buku yang tersedia, lalu klik tombol <strong>Pinjam</strong>.
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-arrow-left-short"></i> Dashboard
                    </a>
                    <form method="get" class="position-relative search-small">
                        <i class="bi bi-search icon"></i>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Cari buku..."
                            value="<?php echo htmlspecialchars($search); ?>">
                    </form>
                </div>
            </div>

            <div class="card-body pt-3">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show small">
                        <i class="bi bi-check-circle-fill me-1"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-3 g-md-4">
                    <?php if (mysqli_num_rows($buku) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($buku)): ?>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="book-card">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-secondary badge-code">
                                            <i class="bi bi-hash me-1"></i><?php echo htmlspecialchars($row['kode_buku']); ?>
                                        </span>
                                        <span class="badge bg-info text-dark">
                                            <?php echo htmlspecialchars($row['kategori']); ?>
                                        </span>
                                    </div>

                                    <div class="book-title">
                                        <?php echo htmlspecialchars($row['judul_buku']); ?>
                                    </div>

                                    <div class="book-meta mb-2">
                                        <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['pengarang']); ?><br>
                                        <i class="bi bi-building me-1"></i><?php echo htmlspecialchars($row['penerbit']); ?><br>
                                        <i class="bi bi-calendar3 me-1"></i><?php echo htmlspecialchars($row['tahun_terbit']); ?><br>
                                        <i class="bi bi-geo-alt me-1"></i>Rak <?php echo htmlspecialchars($row['lokasi_rak']); ?>
                                    </div>

                                    <div class="book-footer">
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-check-circle me-1"></i><?php echo (int)$row['stok_tersedia']; ?> tersedia
                                        </span>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="id_buku" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="pinjam_buku"
                                                class="btn btn-primary btn-sm rounded-pill"
                                                onclick="return confirm('Pinjam buku ini?');">
                                                <i class="bi bi-bookmark-plus me-1"></i>Pinjam
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-warning small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Tidak ada buku yang tersedia untuk dipinjam saat ini.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>