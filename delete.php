<?php
$conn = new mysqli("localhost", "root", "", "anggota", 3306);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = intval($_GET['id']);

// Hapus relasi dulu
$conn->query("DELETE FROM mahasiswa_minat WHERE id_mhs = $id");

// Baru hapus mahasiswa
$conn->query("DELETE FROM mahasiswa WHERE id_mhs = $id");

header("Location: aksi.php");
exit;
?>
