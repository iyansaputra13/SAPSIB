<?php
session_start();
session_unset();
session_destroy();
header("Location: login-siswa.php");
exit();
?>