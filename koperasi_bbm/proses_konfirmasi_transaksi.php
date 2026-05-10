<?php
include 'koneksi.php';
$kode = $_GET['kode'];

$sql = mysqli_query($koneksi, "UPDATE pemesanan SET status = 'selesai' WHERE kode_struk = '$kode'");

if($sql) {
    echo "<script>alert('Berhasil!'); window.location='cetak_struk.php?kode=$kode';</script>";
} else {
    echo "<script>alert('Gagal!'); window.history.back();</script>";
}
?>