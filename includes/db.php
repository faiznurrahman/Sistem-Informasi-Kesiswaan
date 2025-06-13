<?php
$host     = "localhost";
$username = "root";
$password = ""; // kosongkan jika belum diatur
$database = "kesiswaan"; // ganti dengan nama database kamu

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
