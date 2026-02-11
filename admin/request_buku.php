<?php
require_once '../config.php';
if (!is_logged_in() || !is_admin()) redirect('../index.php');

$success = '';
$error   = '';

// proses update status
if (isset($_POST['update_status'])) {
    $id     = $_POST['id'];
    $status = $_POST['status'];
    if (in_array($status, ['menunggu', 'diproses', 'ditolak', 'ditambah'])) {
        mysqli_query($conn, "UPDATE book_requests SET status='$status' WHERE id='$id'");
        $success = 'Status request berhasil diupdate.';
    }
}

// ambil semua request
$requests = mysqli_query($conn, "
    SELECT r.*, u.nama_lengkap, u.username
    FROM book_requests r
    JOIN users u ON r.id_user = u.id
    ORDER BY r.tanggal_request DESC
");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Request Buku - Admin</title>
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelola_buku.php"><i class="bi bi-book me-1"></i>Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelola_anggota.php"><i class="bi bi-people me-1"></i>Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaksi.php"><i class="bi bi-arrow-left-right me-1"></i>Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link active" href="request_buku.php"><i class="bi bi-megaphone-fill me-1"></i>Request Buku</a></li>
                    <li class="nav-item d-none d-lg-block ms-2">
                        <a href="../logout.php" class="btn btn-outline-light btn-sm rounded-pill">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Request Buku dari Anggota</h4>
                <small class="text-muted">Gunakan halaman ini untuk meninjau dan memproses permintaan judul buku baru.</small>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show small">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show small">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card-shell">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern row-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Anggota</th>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>Keterangan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (mysqli_num_rows($requests) > 0):
                                while ($r = mysqli_fetch_assoc($requests)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($r['nama_lengkap']); ?></strong><br>
                                            <small class="text-muted">@<?php echo htmlspecialchars($r['username']); ?></small>
                                        </td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($r['judul_buku']); ?></td>
                                        <td><?php echo htmlspecialchars($r['penulis']); ?></td>
                                        <td style="max-width:260px;"><?php echo nl2br(htmlspecialchars($r['keterangan'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($r['tanggal_request'])); ?></td>
                                        <td>
                                            <span class="status-pill <?php echo $r['status']; ?>">
                                                <?php echo ucfirst($r['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="post" class="d-flex flex-column gap-1">
                                                <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="menunggu" <?php echo $r['status'] == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                                    <option value="diproses" <?php echo $r['status'] == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                                    <option value="ditolak" <?php echo $r['status'] == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                                    <option value="ditambah" <?php echo $r['status'] == 'ditambah' ? 'selected' : ''; ?>>Sudah Ditambahkan</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">
                                                    Update
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted small">
                                        Belum ada request buku dari anggota.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>