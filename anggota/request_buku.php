<?php
require_once '../config.php';
if (!is_logged_in() || !is_anggota()) redirect('../index.php');

$user_id = $_SESSION['user_id'];
$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_request'])) {
    $judul      = clean_input($_POST['judul_buku']);
    $penulis    = clean_input($_POST['penulis']);
    $keterangan = clean_input($_POST['keterangan']);

    if ($judul == '') {
        $error = 'Judul buku wajib diisi.';
    } else {
        $q = "INSERT INTO book_requests (id_user,judul_buku,penulis,keterangan)
              VALUES ('$user_id','$judul','$penulis','$keterangan')";
        if (mysqli_query($conn, $q)) {
            $success = 'Request buku berhasil dikirim. Admin akan meninjau permintaan kamu.';
        } else {
            $error = 'Gagal mengirim request buku.';
        }
    }
}

// ambil riwayat request user ini
$req = mysqli_query($conn, "
    SELECT * FROM book_requests
    WHERE id_user='$user_id'
    ORDER BY tanggal_request DESC
");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Request Buku - Perpustakaan SMK</title>
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

        .card-shell {
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

        .status-pill {
            border-radius: 999px;
            padding: .18rem .7rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .status-pill.menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .status-pill.diproses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-pill.ditolak {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-pill.ditambah {
            background: #dcfce7;
            color: #166534;
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="peminjaman.php"><i class="bi bi-journal-bookmark-fill me-1"></i>Peminjaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengembalian.php"><i class="bi bi-arrow-return-left me-1"></i>Pengembalian</a></li>
                    <li class="nav-item"><a class="nav-link active" href="request_buku.php"><i class="bi bi-megaphone-fill me-1"></i>Request Buku</a></li>
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
        <div class="row g-3">
            <!-- FORM REQUEST -->
            <div class="col-lg-5">
                <div class="card-shell mb-3">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <span class="badge rounded-pill text-bg-primary">
                                <i class="bi bi-megaphone-fill"></i>
                            </span>
                            Request Buku Baru
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success small alert-dismissible fade show mb-3">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger small alert-dismissible fade show mb-3">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <p class="small text-muted mb-3">
                            Gunakan form ini untuk mengusulkan judul buku yang belum tersedia di perpustakaan.
                        </p>

                        <form method="post">
                            <div class="mb-2">
                                <label class="form-label">Judul Buku *</label>
                                <input type="text" name="judul_buku" class="form-control form-control-sm"
                                    placeholder="Contoh: Pemrograman Web Dasar" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Penulis</label>
                                <input type="text" name="penulis" class="form-control form-control-sm"
                                    placeholder="Nama penulis (jika diketahui)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alasan / Keterangan</label>
                                <textarea name="keterangan" rows="3" class="form-control form-control-sm"
                                    placeholder="Misal: untuk referensi pelajaran X, edisi terbaru, dsb."></textarea>
                            </div>
                            <button type="submit" name="kirim_request"
                                class="btn btn-primary w-100 btn-sm">
                                <i class="bi bi-send-fill me-1"></i>Kirim Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT REQUEST -->
            <div class="col-lg-7">
                <div class="card-shell">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-list-check me-1 text-primary"></i>Riwayat Request Kamu
                        </h6>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-modern row-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Penulis</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($req) > 0): ?>
                                        <?php while ($r = mysqli_fetch_assoc($req)): ?>
                                            <tr>
                                                <td class="fw-semibold"><?php echo htmlspecialchars($r['judul_buku']); ?></td>
                                                <td><?php echo htmlspecialchars($r['penulis']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($r['tanggal_request'])); ?></td>
                                                <td>
                                                    <span class="status-pill <?php echo $r['status']; ?>">
                                                        <?php echo ucfirst($r['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted small">
                                                Belum ada request buku yang kamu kirim.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>