<?php
require_once '../config/koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit();
}
?>