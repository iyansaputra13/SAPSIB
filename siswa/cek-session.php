<?php
require_once '../config/koneksi.php';

// Cek apakah siswa sudah login
if (!isset($_SESSION['siswa_login']) || $_SESSION['siswa_login'] !== true) {
    header("Location: login-siswa.php");
    exit();
}
?>