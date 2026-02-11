<?php
require_once '../config.php';


if (!is_logged_in() || !is_admin()) {
    redirect('../index.php');
}


$success = '';
$error   = '';


// TAMBAH BUKU
if (isset($_POST['tambah_buku'])) {
    $kode_buku    = clean_input($_POST['kode_buku']);
    $judul_buku   = clean_input($_POST['judul_buku']);
    $pengarang    = clean_input($_POST['pengarang']);
    $penerbit     = clean_input($_POST['penerbit']);
    $tahun_terbit = clean_input($_POST['tahun_terbit']);
    $kategori     = clean_input($_POST['kategori']);
    $jumlah_buku  = clean_input($_POST['jumlah_buku']);
    $lokasi_rak   = clean_input($_POST['lokasi_rak']);


    $query = "INSERT INTO buku (kode_buku, judul_buku, pengarang, penerbit, tahun_terbit, kategori, jumlah_buku, stok_tersedia, lokasi_rak)
              VALUES ('$kode_buku','$judul_buku','$pengarang','$penerbit','$tahun_terbit','$kategori','$jumlah_buku','$jumlah_buku','$lokasi_rak')";
    if (mysqli_query($conn, $query)) {
        $success = 'Buku berhasil ditambahkan!';
    } else {
        $error = 'Gagal menambahkan buku!';
    }
}


// EDIT BUKU
if (isset($_POST['edit_buku'])) {
    $id           = $_POST['id'];
    $kode_buku    = clean_input($_POST['kode_buku']);
    $judul_buku   = clean_input($_POST['judul_buku']);
    $pengarang    = clean_input($_POST['pengarang']);
    $penerbit     = clean_input($_POST['penerbit']);
    $tahun_terbit = clean_input($_POST['tahun_terbit']);
    $kategori     = clean_input($_POST['kategori']);
    $jumlah_buku  = clean_input($_POST['jumlah_buku']);
    $stok_tersedia = clean_input($_POST['stok_tersedia']);
    $lokasi_rak   = clean_input($_POST['lokasi_rak']);


    $query = "UPDATE buku SET
                kode_buku='$kode_buku',
                judul_buku='$judul_buku',
                pengarang='$pengarang',
                penerbit='$penerbit',
                tahun_terbit='$tahun_terbit',
                kategori='$kategori',
                jumlah_buku='$jumlah_buku',
                stok_tersedia='$stok_tersedia',
                lokasi_rak='$lokasi_rak'
              WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $success = 'Buku berhasil diupdate!';
    } else {
        $error = 'Gagal mengupdate buku!';
    }
}


// HAPUS BUKU
if (isset($_GET['hapus'])) {
    $id    = $_GET['hapus'];
    $query = "DELETE FROM buku WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $success = 'Buku berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus buku!';
    }
}


// DATA BUKU
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$where  = $search
    ? "WHERE judul_buku LIKE '%$search%' OR kode_buku LIKE '%$search%' OR pengarang LIKE '%$search%'"
    : '';
$buku = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">


<head>
    <meta charset="utf-8">
    <title>Kelola Buku - Admin</title>
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


        .search-small {
            max-width: 220px;
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
                    <li class="nav-item"><a class="nav-link active" href="kelola_buku.php"><i class="bi bi-book me-1"></i>Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelola_anggota.php"><i class="bi bi-people me-1"></i>Anggota</a></li>
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
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <div>
                <h4 class="fw-semibold mb-1">Kelola Data Buku</h4>
                <small class="text-muted">Tambah, ubah, dan hapus data buku perpustakaan.</small>
            </div>
        </div>


        <!-- Toolbar -->
        <div class="card-shell mb-3">
            <div class="card-body d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
                <div>
                    <button class="btn btn-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Buku
                    </button>
                </div>
                <form method="get" class="position-relative search-small">
                    <i class="bi bi-search icon"></i>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari judul / kode / pengarang"
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


        <!-- Tabel Buku -->
        <div class="card-shell">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern row-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Penerbit</th>
                                <th>Tahun</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Rak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($buku)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><span class="badge text-bg-secondary rounded-pill"><?php echo $row['kode_buku']; ?></span></td>
                                    <td class="fw-semibold"><?php echo $row['judul_buku']; ?></td>
                                    <td><?php echo $row['pengarang']; ?></td>
                                    <td><?php echo $row['penerbit']; ?></td>
                                    <td><?php echo $row['tahun_terbit']; ?></td>
                                    <td><span class="badge text-bg-info"><?php echo $row['kategori']; ?></span></td>
                                    <td><?php echo $row['stok_tersedia']; ?> / <?php echo $row['jumlah_buku']; ?></td>
                                    <td><?php echo $row['lokasi_rak']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"
                                            onclick="editBuku(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin hapus buku ini?');">
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


    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Buku Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Buku *</label>
                                <input type="text" name="kode_buku" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Buku *</label>
                                <input type="text" name="judul_buku" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pengarang *</label>
                                <input type="text" name="pengarang" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penerbit</label>
                                <input type="text" name="penerbit" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" class="form-control" min="1900" max="2100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option value="Teknologi">Teknologi</option>
                                    <option value="Pelajaran">Pelajaran</option>
                                    <option value="Novel">Novel</option>
                                    <option value="Komik">Komik</option>
                                    <option value="Majalah">Majalah</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Buku *</label>
                                <input type="number" name="jumlah_buku" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Lokasi Rak</label>
                                <input type="text" name="lokasi_rak" class="form-control" placeholder="Contoh: A1, B2">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_buku" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Buku</label>
                                <input type="text" name="kode_buku" id="edit_kode_buku" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul Buku</label>
                                <input type="text" name="judul_buku" id="edit_judul_buku" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pengarang</label>
                                <input type="text" name="pengarang" id="edit_pengarang" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penerbit</label>
                                <input type="text" name="penerbit" id="edit_penerbit" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" id="edit_tahun_terbit" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kategori</label>
                                <input type="text" name="kategori" id="edit_kategori" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jumlah Total</label>
                                <input type="number" name="jumlah_buku" id="edit_jumlah_buku" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Tersedia</label>
                                <input type="number" name="stok_tersedia" id="edit_stok_tersedia" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi Rak</label>
                                <input type="text" name="lokasi_rak" id="edit_lokasi_rak" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_buku" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editBuku(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_kode_buku').value = data.kode_buku;
            document.getElementById('edit_judul_buku').value = data.judul_buku;
            document.getElementById('edit_pengarang').value = data.pengarang;
            document.getElementById('edit_penerbit').value = data.penerbit;
            document.getElementById('edit_tahun_terbit').value = data.tahun_terbit;
            document.getElementById('edit_kategori').value = data.kategori;
            document.getElementById('edit_jumlah_buku').value = data.jumlah_buku;
            document.getElementById('edit_stok_tersedia').value = data.stok_tersedia;
            document.getElementById('edit_lokasi_rak').value = data.lokasi_rak;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
</body>


</html>