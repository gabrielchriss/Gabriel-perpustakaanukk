<?php
require_once 'config.php';

if (is_logged_in()) {
    if (is_admin())   redirect('admin/dashboard.php');
    if (is_anggota()) redirect('anggota/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username      = clean_input($_POST['username']);
    $password      = clean_input($_POST['password']);  // plain text
    $nama_lengkap  = clean_input($_POST['nama_lengkap']);
    $email         = clean_input($_POST['email']);
    $no_telp       = clean_input($_POST['no_telp']);
    $alamat        = clean_input($_POST['alamat']);
    $role          = ($_POST['role'] == 'admin') ? 'admin' : 'anggota';

    // cek username
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        $error = 'Username sudah digunakan!';
    } else {
        $sql = "INSERT INTO users(username,password,nama_lengkap,email,no_telp,alamat,role,status)
                VALUES('$username','$password','$nama_lengkap','$email','$no_telp','$alamat','$role','aktif')";
        if (mysqli_query($conn, $sql)) {
            $new_id = mysqli_insert_id($conn);
            // auto login
            $_SESSION['user_id']      = $new_id;
            $_SESSION['username']     = $username;
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['role']         = $role;

            if ($role == 'admin') redirect('admin/dashboard.php');
            else redirect('anggota/dashboard.php');
        } else {
            $error = 'Gagal register: ' . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Register Perpustakaan</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top, #a5b4fc 0, #4f46e5 40%, #111827 100%);
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }

        .register-shell {
            max-width: 920px;
        }

        .card-register {
            border: none;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .45);
            transform: translateY(16px);
            opacity: 0;
            animation: fadeUp .6s ease-out forwards;
        }

        .side-left {
            background: linear-gradient(135deg, rgba(15, 23, 42, .9), rgba(59, 130, 246, .9));
            color: #e5e7eb;
            padding: 36px 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media(min-width:992px) {
            .side-left {
                padding: 40px 32px;
            }
        }

        .brand-circle {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            background: rgba(15, 23, 42, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #e5e7eb;
            box-shadow: 0 0 0 1px rgba(148, 163, 184, .5);
        }

        .side-right {
            padding: 28px 22px 24px;
            background: #f9fafb;
        }

        @media(min-width:992px) {
            .side-right {
                padding: 34px 32px 30px;
            }
        }

        .form-control,
        .form-select {
            border-radius: 999px;
            font-size: .9rem;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 .15rem rgba(59, 130, 246, .25);
            border-color: #3b82f6;
        }

        textarea.form-control {
            border-radius: 16px;
        }

        .btn-primary {
            border-radius: 999px;
            font-weight: 600;
            font-size: .9rem;
            background: linear-gradient(120deg, #2563eb, #4f46e5);
            border: none;
            box-shadow: 0 10px 25px rgba(37, 99, 235, .5);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .65);
            filter: brightness(1.04);
        }

        .fade-field {
            opacity: 0;
            transform: translateY(8px);
            animation: fadeInField .5s ease-out forwards;
        }

        .fade-field.d1 {
            animation-delay: .10s;
        }

        .fade-field.d2 {
            animation-delay: .18s;
        }

        .fade-field.d3 {
            animation-delay: .26s;
        }

        .fade-field.d4 {
            animation-delay: .34s;
        }

        .fade-field.d5 {
            animation-delay: .42s;
        }

        .fade-field.d6 {
            animation-delay: .50s;
        }

        .fade-field.d7 {
            animation-delay: .58s;
        }

        .switch-link {
            font-size: .85rem;
        }

        .switch-link a {
            font-weight: 600;
            text-decoration: none;
            color: #2563eb;
        }

        .switch-link a:hover {
            text-decoration: underline;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(22px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInField {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="container register-shell px-3 px-md-2">
        <div class="card card-register">
            <div class="row g-0">
                <!-- Kiri -->
                <div class="col-lg-5">
                    <div class="side-left h-100">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="brand-circle">
                                    <i class="bi bi-journal-plus"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-semibold">Daftar Anggota</h5>
                                    <small class="text-light">Perpustakaan Gabriel</small>
                                </div>
                            </div>
                            <p class="small mb-3">
                                Buat akun anggota untuk bisa meminjam buku, melihat riwayat peminjaman,
                                dan mengajukan request judul buku baru secara online.
                            </p>
                            <ul class="small ps-3 mb-0">
                                <li>Akses koleksi perpustakaan kapan saja.</li>
                                <li>Pantau masa peminjaman dan pengembalian.</li>
                                <li>Ikut aktif mengusulkan buku yang dibutuhkan.</li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <small class="text-light opacity-75">
                                Data yang kamu isi harus benar agar memudahkan petugas saat verifikasi keanggotaan.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Kanan -->
                <div class="col-lg-7">
                    <div class="side-right h-100 d-flex flex-column">
                        <div class="mb-3 text-center">
                            <h5 class="fw-semibold mb-1">Form Pendaftaran</h5>
                            <p class="text-muted small mb-0">
                                Isi data diri kamu dengan lengkap untuk membuat akun perpustakaan.
                            </p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="mt-2">
                            <div class="row g-2">
                                <div class="col-sm-6 fade-field d1">
                                    <label class="form-label small mb-1">Username</label>
                                    <input type="text" name="username" class="form-control" required autocomplete="off">
                                </div>
                                <div class="col-sm-6 fade-field d2">
                                    <label class="form-label small mb-1">Password</label>
                                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="mt-2 fade-field d3">
                                <label class="form-label small mb-1">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required>
                            </div>

                            <div class="row g-2 mt-1">
                                <div class="col-sm-6 fade-field d4">
                                    <label class="form-label small mb-1">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-sm-6 fade-field d5">
                                    <label class="form-label small mb-1">No. Telepon</label>
                                    <input type="text" name="no_telp" class="form-control">
                                </div>
                            </div>

                            <div class="mt-2 fade-field d6">
                                <label class="form-label small mb-1">Alamat</label>
                                <textarea name="alamat" rows="2" class="form-control" placeholder="Tuliskan alamat rumah/sekolah kamu"></textarea>
                            </div>

                            <div class="row g-2 mt-2 fade-field d7">
                                <div class="col-sm-6">
                                    <label class="form-label small mb-1">Daftar sebagai</label>
                                    <select name="role" class="form-select">
                                        <option value="anggota" selected>Anggota</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" name="register" class="btn btn-primary w-100 mt-3 fade-field d7">
                                <i class="bi bi-person-plus-fill me-1"></i>Daftar &amp; Masuk
                            </button>
                        </form>

                        <div class="mt-3 text-center switch-link">
                            Sudah punya akun?
                            <a href="index.php">Login di sini</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>