<?php
require_once 'config.php';

if (is_logged_in()) {
    if (is_admin())   redirect('admin/dashboard.php');
    if (is_anggota()) redirect('anggota/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']); // plain text
    $sql = "SELECT * FROM users 
            WHERE username='$username' 
              AND password='$password' 
              AND status='aktif'";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) == 1) {
        $u = mysqli_fetch_assoc($res);
        $_SESSION['user_id']      = $u['id'];
        $_SESSION['username']     = $u['username'];
        $_SESSION['nama_lengkap'] = $u['nama_lengkap'];
        $_SESSION['role']         = $u['role'];

        if ($u['role'] == 'admin') redirect('admin/dashboard.php');
        else redirect('anggota/dashboard.php');
    } else {
        $error = 'Username/password salah atau akun nonaktif.';
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Login Perpustakaan</title>
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

        .login-shell {
            max-width: 880px;
        }

        .card-login {
            border: none;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .45);
            transform: translateY(16px);
            opacity: 0;
            animation: fadeUp .6s ease-out forwards;
            transition: transform .22s ease, box-shadow .22s ease;
        }

        /* Efek ketika mouse diarahkan ke form (card) */
        .card-login:hover {
            transform: translateY(8px) scale(1.01);
            box-shadow: 0 22px 60px rgba(15, 23, 42, .55);
        }

        .side-left {
            background: linear-gradient(135deg, rgba(15, 23, 42, .8), rgba(79, 70, 229, .9));
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

        .tag-pill {
            border-radius: 999px;
            background: rgba(15, 23, 42, .6);
            color: #e5e7eb;
            font-size: .76rem;
            padding: .22rem .85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .side-right {
            padding: 32px 26px 26px;
            background: #f9fafb;
        }

        @media(min-width:992px) {
            .side-right {
                padding: 38px 34px 32px;
            }
        }

        .form-control,
        .input-group-text {
            border-radius: 999px;
        }

        .input-group-text {
            border-right: none;
            background: #eef2ff;
            color: #4f46e5;
        }

        .form-control {
            border-left: none;
            font-size: .9rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 .15rem rgba(79, 70, 229, .25);
            border-color: #4f46e5;
        }

        .btn-primary {
            border-radius: 999px;
            font-weight: 600;
            font-size: .9rem;
            background: linear-gradient(120deg, #4f46e5, #6366f1);
            border: none;
            box-shadow: 0 10px 25px rgba(79, 70, 229, .5);
            transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(79, 70, 229, .65);
            filter: brightness(1.03);
        }

        .switch-link {
            font-size: .85rem;
        }

        .switch-link a {
            font-weight: 600;
            text-decoration: none;
            color: #4f46e5;
        }

        .switch-link a:hover {
            text-decoration: underline;
        }

        .fade-input {
            opacity: 0;
            transform: translateY(8px);
            animation: fadeInField .5s ease-out forwards;
        }

        .fade-input.delay-1 {
            animation-delay: .15s;
        }

        .fade-input.delay-2 {
            animation-delay: .25s;
        }

        .fade-input.delay-3 {
            animation-delay: .35s;
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

    <div class="container login-shell px-3 px-md-2">
        <div class="card card-login">
            <div class="row g-0">
                <!-- Kiri -->
                <div class="col-lg-5">
                    <div class="side-left h-100">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="brand-circle">
                                    <i class="bi bi-book-fill"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-semibold">Perpustakaan Gabriel</h5>
                                    <small class="text-light">Sistem Informasi Perpustakaan</small>
                                </div>
                            </div>
                            <p class="small mb-3">
                                Kelola peminjaman buku dengan mudah. Admin dan anggota dapat mengakses
                                koleksi, transaksi, dan riwayat lebih cepat melalui satu portal.
                            </p>
                            <span class="tag-pill mb-1">
                                <i class="bi bi-shield-lock-fill"></i>
                                Akses aman &amp; terkontrol
                            </span>
                        </div>
                        <div class="mt-3">
                            <small class="text-light opacity-75">
                                Tip: Gunakan data akun yang sudah terdaftar. Jika belum punya akun,
                                silakan daftar terlebih dahulu.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Kanan -->
                <div class="col-lg-7">
                    <div class="side-right h-100 d-flex flex-column">
                        <div class="mb-3 text-center">
                            <h5 class="fw-semibold mb-1">Masuk ke Akun</h5>
                            <p class="text-muted small mb-0">
                                Masukkan username dan password untuk mengakses dashboard.
                            </p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 small">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="mt-2">
                            <div class="mb-3 fade-input delay-1">
                                <label class="form-label small mb-1">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="username" class="form-control" required autocomplete="username">
                                </div>
                            </div>
                            <div class="mb-3 fade-input delay-2">
                                <label class="form-label small mb-1">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control" required autocomplete="current-password">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 fade-input delay-3">
                                <div class="form-check small">
                                    <input class="form-check-input" type="checkbox" value="" id="ingatSaya">
                                    <label class="form-check-label" for="ingatSaya">
                                        Ingat saya
                                    </label>
                                </div>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100 mb-3 fade-input delay-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                            </button>
                        </form>

                        <div class="mt-auto pt-2 text-center switch-link">
                            Belum punya akun?
                            <a href="register.php">Daftar sebagai anggota</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>