<?php
session_start();
include 'koneksi.php';

$id_produk = $_GET['id'];
$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
$id_user = $_SESSION['id_user'];
$tujuan = isset($_GET['tujuan']) ? $_GET['tujuan'] : '';

// Cek apakah barang sudah ada di keranjang
$cek = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_user='$id_user' AND id_produk='$id_produk'");

if(mysqli_num_rows($cek) > 0) {
    // Jika sudah ada, update jumlahnya
    mysqli_query($koneksi, "UPDATE keranjang SET jumlah = jumlah + $qty WHERE id_user='$id_user' AND id_produk='$id_produk'");
} else {
    // Jika belum ada, masukkan data baru
    mysqli_query($koneksi, "INSERT INTO keranjang (id_user, id_produk, jumlah) VALUES ('$id_user', '$id_produk', '$qty')");
}

// REDIRECT: Jika tujuan adalah keranjang, arahkan ke keranjang_view.php
if ($tujuan == 'keranjang') {
    header("location:keranjang_view.php");
} else {
    header("location:anggota_dashboard.php?status=sukses");
}
exit();
?>