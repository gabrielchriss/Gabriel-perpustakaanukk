<?php
// ===========================
// Konfigurasi & Koneksi DB
// ===========================
session_start();

// KONFIGURASI DATABASE
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'perpustakaangab'); // ganti jika nama DB beda

// KONEKSI DATABASE
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// CEK KONEKSI
if (!$conn) {
    die('Koneksi Database Gagal: ' . mysqli_connect_error());
}

// SET CHARSET
mysqli_set_charset($conn, 'utf8mb4');

// ===========================
// FUNGSI UTAMA
// ===========================

// Membersihkan input user
function clean_input($data)
{
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Redirect ke halaman lain
function redirect($url)
{
    header("Location: $url");
    exit;
}

// Cek apakah sudah login
function is_logged_in()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Cek role admin
function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Cek role anggota
function is_anggota()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'anggota';
}
