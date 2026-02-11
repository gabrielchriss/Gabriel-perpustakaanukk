<?php
require_once '../config.php';
if (!is_logged_in() || !is_admin()) redirect('../index.php');

$success = '';
$error   = '';

// TAMBAH PEMINJAMAN
if (isset($_POST['tambah_peminjaman'])) {
    $id_user         = $_POST['id_user'];
    $id_buku         = $_POST['id_buku'];
    $tanggal_pinjam  = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    $buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id='$id_buku'"));
    if ($buku && $buku['stok_tersedia'] > 0) {
        mysqli_query($conn, "INSERT INTO peminjaman (id_user,id_buku,tanggal_pinjam,tanggal_kembali)
                            VALUES ('$id_user','$id_buku','$tanggal_pinjam','$tanggal_kembali')");
        mysqli_query($conn, "UPDATE buku SET stok_tersedia = stok_tersedia - 1 WHERE id='$id_buku'");
        $success = 'Peminjaman berhasil ditambahkan!';
    } else {
        $error = 'Stok buku tidak tersedia!';
    }
}

// PROSES PENGEMBALIAN
if (isset($_POST['kembalikan'])) {
    $id     = $_POST['id'];
    $id_buku = $_POST['id_buku'];
    $tanggal_dikembalikan = date('Y-m-d');

    mysqli_query($conn, "UPDATE peminjaman 
                        SET status='dikembalikan', tanggal_dikembalikan='$tanggal_dikembalikan' 
                        WHERE id='$id'");
    mysqli_query($conn, "UPDATE buku SET stok_tersedia = stok_tersedia + 1 WHERE id='$id_buku'");
    $success = 'Pengembalian berhasil diproses.';
}

// DATA PEMINJAMAN
$peminjaman = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, u.username, b.judul_buku, b.kode_buku 
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id
    JOIN buku b ON p.id_buku = b.id
    ORDER BY p.id DESC
");

// DROPDOWN
$anggota = mysqli_query($conn, "SELECT * FROM users WHERE role='anggota' AND status='aktif'");
$buku    = mysqli_query($conn, "SELECT * FROM buku WHERE stok_tersedia > 0");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Transaksi - Admin</title>
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
                    <li class="nav-item"><a class="nav-link active" href="transaksi.php"><i class="bi bi-arrow-left-right me-1"></i>Transaksi</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="request_buku.php">
                            <i class="bi bi-megaphone-fill me-1"></i>Request Buku
                        </a>
                    </li>

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
                <h4 class="fw-semibold mb-1">Transaksi Peminjaman & Pengembalian</h4>
                <small class="text-muted">Kelola peminjaman buku dan proses pengembalian dari anggota.</small>
            </div>
            <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="bi bi-plus-circle me-1"></i>Tambah Peminjaman
            </button>
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
                                <th>Kode Buku</th>
                                <th>Judul Buku</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Tgl Dikembalikan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($peminjaman)):
                                $badge = 'warning';
                                if ($row['status'] == 'dikembalikan') $badge = 'success';
                                elseif ($row['status'] == 'terlambat') $badge = 'danger';
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['kode_buku']); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['judul_buku']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                    <td>
                                        <?php echo $row['tanggal_dikembalikan']
                                            ? date('d/m/Y', strtotime($row['tanggal_dikembalikan']))
                                            : '-'; ?>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-<?php echo $badge; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'dipinjam'): ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="id_buku" value="<?php echo $row['id_buku']; ?>">
                                                <button type="submit" name="kembalikan"
                                                    class="btn btn-sm btn-success"
                                                    onclick="return confirm('Proses pengembalian buku ini?');">
                                                    <i class="bi bi-check-circle me-1"></i>Kembalikan
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Peminjaman -->
    <div class="modal fade" id="tambahModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Peminjaman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Anggota *</label>
                            <select name="id_user" class="form-select" required>
                                <option value="">Pilih anggota</option>
                                <?php
                                mysqli_data_seek($anggota, 0);
                                while ($row = mysqli_fetch_assoc($anggota)): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['nama_lengkap']; ?> (<?php echo $row['username']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Buku *</label>
                            <select name="id_buku" class="form-select" required>
                                <option value="">Pilih buku</option>
                                <?php
                                mysqli_data_seek($buku, 0);
                                while ($row = mysqli_fetch_assoc($buku)): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['judul_buku']; ?> (Stok: <?php echo $row['stok_tersedia']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pinjam *</label>
                                <input type="date" name="tanggal_pinjam" class="form-control"
                                    value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Kembali *</label>
                                <input type="date" name="tanggal_kembali" class="form-control"
                                    value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_peminjaman" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>