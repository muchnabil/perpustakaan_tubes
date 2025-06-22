<?php
$host = "localhost";
$user = "root";
$password = ""; // default kosong untuk XAMPP
$database = "perpustakaan_tubes";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>