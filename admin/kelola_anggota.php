<?php
require_once '../config.php';
if (!is_logged_in() || !is_admin()) redirect('../index.php');

$success = '';
$error   = '';

// TAMBAH ANGGOTA / ADMIN
if (isset($_POST['tambah_anggota'])) {
    $username      = clean_input($_POST['username']);
    $password      = clean_input($_POST['password']); // masih plain text seperti yang lain
    $nama_lengkap  = clean_input($_POST['nama_lengkap']);
    $email         = clean_input($_POST['email']);
    $no_telp       = clean_input($_POST['no_telp']);
    $alamat        = clean_input($_POST['alamat']);
    $role          = $_POST['role'] === 'admin' ? 'admin' : 'anggota';
    $status        = $_POST['status'] === 'nonaktif' ? 'nonaktif' : 'aktif';

    // cek username
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $error = 'Username sudah digunakan.';
    } else {
        $q = "INSERT INTO users (username,password,nama_lengkap,email,no_telp,alamat,role,status)
              VALUES ('$username','$password','$nama_lengkap','$email','$no_telp','$alamat','$role','$status')";
        if (mysqli_query($conn, $q)) {
            $success = 'Akun ' . $role . ' baru berhasil dibuat.';
        } else {
            $error = 'Gagal menambahkan anggota.';
        }
    }
}

// HAPUS ANGGOTA
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id' AND role='anggota'");
}

// UPDATE STATUS
if (isset($_POST['update_status'])) {
    $id     = $_POST['id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE users SET status='$status' WHERE id='$id'");
}

$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$where  = $search
    ? "AND (username LIKE '%$search%' OR nama_lengkap LIKE '%$search%' OR email LIKE '%$search%')"
    : '';

$anggota = mysqli_query($conn, "SELECT * FROM users WHERE role='anggota' $where ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Anggota - Admin</title>
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
            padding: .22rem .7rem;
            font-size: .75rem;
            font-weight: 600;
        }

        .status-pill.aktif {
            background: #dcfce7;
            color: #166534;
        }

        .status-pill.nonaktif {
            background: #fee2e2;
            color: #b91c1c;
        }

        .search-small {
            max-width: 240px;
        }

        .search-small input {
            font-size: .82rem;
            border-radius: 999px;
            padding-left: 2rem;
        }

        .search-small .icon {
            position: absolute;
            left: .7rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: .85rem;
            color: #9ca3af;
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
                    <li class="nav-item"><a class="nav-link active" href="kelola_anggota.php"><i class="bi bi-people me-1"></i>Anggota</a></li>
                    <li class="nav-item"><a class="nav-link" href="transaksi.php"><i class="bi bi-arrow-left-right me-1"></i>Transaksi</a></li>
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
        <!-- Header + Toolbar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Kelola Data Anggota</h4>
                <small class="text-muted">Tambah akun anggota/admin dan atur status keaktifannya.</small>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#tambahModal">
                    <i class="bi bi-person-plus me-1"></i>Tambah Anggota
                </button>
                <form method="get" class="position-relative search-small">
                    <i class="bi bi-search icon"></i>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari username / nama / email"
                        value="<?php echo htmlspecialchars($search); ?>">
                </form>
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

        <!-- Tabel Anggota -->
        <div class="card-shell">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern row-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Status</th>
                                <th>Tgl Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($anggota)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['no_telp']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="status-pill <?php echo $row['status']; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                    <option value="aktif" <?php echo $row['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                                    <option value="nonaktif" <?php echo $row['status'] == 'nonaktif' ? 'selected' : ''; ?>>Non-Aktif</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                        </div>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_daftar'])); ?></td>
                                    <td>
                                        <a href="?hapus=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus anggota ini?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Anggota -->
    <div class="modal fade" id="tambahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Anggota/Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="anggota">Anggota</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Akun</label>
                                <select name="status" class="form-select">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_anggota" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>