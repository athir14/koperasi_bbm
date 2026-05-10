<?php
include 'koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_user = '$id'");

$query = mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id'");

if ($query) {
    echo "<script>alert('Anggota Berhasil Dihapus!'); window.location='data_anggota.php';</script>";
}
?>