<?php
require_once '../config.php';
if (!is_logged_in() || !is_anggota()) redirect('../index.php');
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Profil Perpustakaan - Perpustakaan SMK</title>
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

        .section-card {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-header {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .section-header i {
            font-size: 1.1rem;
        }

        .section-body {
            padding: 18px 20px 22px;
        }

        .headline-sub {
            font-size: .88rem;
            color: #6b7280;
        }

        /* Visi misi */
        .visi-text {
            font-size: .95rem;
            line-height: 1.6;
        }

        .misi-list li {
            margin-bottom: .25rem;
            font-size: .9rem;
        }

        /* Timeline sejarah */
        .timeline {
            position: relative;
            padding-left: 0;
            margin: 0;
            list-style: none;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 18px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            padding-left: 48px;
            margin-bottom: 18px;
        }

        .timeline-badge {
            position: absolute;
            left: 10px;
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .18);
        }

        .timeline-year {
            font-size: .8rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 2px;
        }

        .timeline-title {
            font-size: .95rem;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .timeline-text {
            font-size: .9rem;
            color: #4b5563;
        }

        /* Struktur organisasi */
        .org-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 14px;
        }

        @media(min-width:576px) {
            .org-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(min-width:992px) {
            .org-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .org-card {
            border-radius: 16px;
            border: none;
            background: #f9fafb;
            padding: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .org-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, .16);
            background: #ffffff;
        }

        .org-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            margin-bottom: 8px;
            font-size: 26px;
        }

        .org-role {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6b7280;
        }

        .org-name {
            font-weight: 600;
            font-size: .95rem;
        }

        .org-extra {
            font-size: .8rem;
            color: #9ca3af;
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
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-house-door-fill me-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php"><i class="bi bi-journal-bookmark-fill me-1"></i>Peminjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pengembalian.php"><i class="bi bi-arrow-return-left me-1"></i>Pengembalian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="request_buku.php"><i class="bi bi-megaphone-fill me-1"></i>Request Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profil.php"><i class="bi bi-building me-1"></i>Profil</a>
                    </li>
                    <li class="nav-item ms-lg-2 d-none d-lg-block">
                        <a class="btn btn-outline-light btn-sm rounded-pill" href="../logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">

        <!-- Header profil -->
        <div class="mb-3">
            <h4 class="fw-semibold mb-1">Profil Perpustakaan SMK</h4>
            <p class="headline-sub mb-0">
                Mengenal visi, misi, sejarah singkat, dan struktur pengurus perpustakaan sekolah.
            </p>
        </div>

        <!-- Visi Misi -->
        <div class="section-card">
            <div class="section-header" style="background:linear-gradient(120deg,#4f46e5,#6366f1);">
                <i class="bi bi-compass-fill"></i>
                <span>Visi &amp; Misi Perpustakaan</span>
            </div>
            <div class="section-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <span class="badge text-bg-primary rounded-pill mb-2">Visi</span>
                        <p class="visi-text mb-0">
                            Menjadi pusat sumber belajar yang modern, inklusif, dan inspiratif untuk mendukung
                            terwujudnya warga sekolah yang literat, kritis, dan berakhlak mulia.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <span class="badge text-bg-secondary rounded-pill mb-2">Misi</span>
                        <ul class="misi-list ps-3 mb-0">
                            <li>Menyediakan koleksi bahan pustaka yang mutakhir, relevan, dan berkualitas.</li>
                            <li>Mengembangkan layanan perpustakaan yang ramah, cepat, dan berbasis teknologi.</li>
                            <li>Menumbuhkan budaya membaca dan menulis di lingkungan sekolah.</li>
                            <li>Menjalin kerja sama dengan guru untuk mendukung proses pembelajaran.</li>
                            <li>Menciptakan ruang belajar yang nyaman, aman, dan kondusif bagi seluruh anggota.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sejarah Singkat -->
        <div class="section-card">
            <div class="section-header" style="background:linear-gradient(120deg,#0ea5e9,#0369a1);">
                <i class="bi bi-hourglass-split"></i>
                <span>Sejarah Singkat Perpustakaan</span>
            </div>
            <div class="section-body">
                <ul class="timeline">
                    <li class="timeline-item">
                        <span class="timeline-badge"></span>
                        <div class="timeline-year">2010</div>
                        <div class="timeline-title">Perpustakaan Diresmikan</div>
                        <div class="timeline-text">
                            Perpustakaan SMK mulai beroperasi dengan koleksi awal sekitar 500 eksemplar buku
                            pelajaran dan referensi dasar untuk mendukung proses belajar mengajar.
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-badge"></span>
                        <div class="timeline-year">2015</div>
                        <div class="timeline-title">Pengembangan Koleksi &amp; Ruang Baca</div>
                        <div class="timeline-text">
                            Penambahan ruang baca baru, penataan rak yang lebih sistematis, serta peningkatan
                            koleksi buku fiksi, nonfiksi, dan majalah pendidikan.
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-badge"></span>
                        <div class="timeline-year">2020</div>
                        <div class="timeline-title">Digitalisasi Layanan</div>
                        <div class="timeline-text">
                            Penerapan sistem informasi perpustakaan berbasis web untuk pengelolaan anggota,
                            peminjaman, dan pengembalian buku secara lebih terintegrasi.
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-badge"></span>
                        <div class="timeline-year">Sekarang</div>
                        <div class="timeline-title">Perpustakaan Aktif &amp; Adaptif</div>
                        <div class="timeline-text">
                            Perpustakaan terus bertransformasi mengikuti perkembangan teknologi dan kurikulum,
                            serta terbuka terhadap saran judul buku dari seluruh anggota.
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Struktur Organisasi -->
        <div class="section-card">
            <div class="section-header" style="background:linear-gradient(120deg,#16a34a,#15803d);">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Struktur Organisasi Perpustakaan</span>
            </div>
            <div class="section-body">
                <p class="headline-sub mb-3">
                    Berikut adalah susunan pengurus perpustakaan yang bertanggung jawab dalam pengelolaan layanan dan koleksi.
                </p>
                <div class="org-grid">
                    <!-- Kepala Perpustakaan -->
                    <div class="org-card">
                        <div class="org-avatar" style="background:#4f46e5;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div class="org-role">Kepala Perpustakaan</div>
                        <div class="org-name">GABRIEL CHRISTOPHER</div>
                        <div class="org-extra">Penanggung jawab utama pengelolaan perpustakaan.</div>
                    </div>
                    <!-- Wakil Kepala -->
                    <div class="org-card">
                        <div class="org-avatar" style="background:#0ea5e9;">
                            <i class="bi bi-person-fill-gear"></i>
                        </div>
                        <div class="org-role">Wakil Kepala</div>
                        <div class="org-name">ALEX ANDAR</div>
                        <div class="org-extra">Membantu kepala perpustakaan dalam perencanaan program.</div>
                    </div>
                    <!-- Staf Layanan Peminjaman -->
                    <div class="org-card">
                        <div class="org-avatar" style="background:#22c55e;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="org-role">Staf Layanan</div>
                        <div class="org-name">GABREL TOPER</div>
                        <div class="org-extra">Mengelola layanan peminjaman dan pengembalian buku.</div>
                    </div>
                    <!-- Staf Koleksi -->
                    <div class="org-card">
                        <div class="org-avatar" style="background:#f97316;">
                            <i class="bi bi-collection-fill"></i>
                        </div>
                        <div class="org-role">Staf Koleksi</div>
                        <div class="org-name">HUTAJULU</div>
                        <div class="org-extra">Bertanggung jawab atas pendataan dan penataan koleksi.</div>
                    </div>
                    <!-- Staf IT / Sistem -->
                    <div class="org-card">
                        <div class="org-avatar" style="background:#ec4899;">
                            <i class="bi bi-pc-display"></i>
                        </div>
                        <div class="org-role">Staf IT</div>
                        <div class="org-name">CHRISTOPHER</div>
                        <div class="org-extra">Mengelola sistem informasi dan perangkat teknologi perpustakaan.</div>
                    </div>
                    <!-- Petugas Tata Ruang -->
                    <div class="org-card">
                        <div class="org-avatar" style="background:#64748b;">
                            <i class="bi bi-brush-fill"></i>
                        </div>
                        <div class="org-role">Petugas Tata Ruang</div>
                        <div class="org-name">GABRIEL </div>
                        <div class="org-extra">Menjaga kerapian dan kenyamanan ruang perpustakaan.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>