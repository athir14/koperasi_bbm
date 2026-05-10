<?php
include 'koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_produk = '$id'");

$hapus = mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk = '$id'");

if ($hapus) {
    echo "<script>alert('Produk dan riwayat transaksi terkait berhasil dihapus!'); window.location='admin_dashboard.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>