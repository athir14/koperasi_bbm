<?php
include 'koneksi.php';
$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM topup WHERE id_topup = '$id'"));
$id_user = $data['id_user'];
$jumlah = $data['jumlah'];

mysqli_query($koneksi, "UPDATE topup SET status='sukses' WHERE id_topup='$id'");

mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $jumlah WHERE id_user='$id_user'");

echo "<script>alert('Saldo berhasil ditambahkan ke Anggota!'); window.location='konfirmasi_topup.php';</script>";
?>