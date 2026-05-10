<?php
session_start();
include 'koneksi.php';

$id_user = $_SESSION['id_user'];
$id_produk = $_POST['id_produk'];
$harga = $_POST['harga'];
$metode = $_POST['metode'];

if ($metode == "Saldo") {
   
    $u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user='$id_user'"));
    if ($u['saldo'] < $harga) {
        echo "<script>alert('Maaf, Saldo Anda Tidak Cukup!'); window.location='anggota_dashboard.php';</script>";
        exit();
    }

    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $harga WHERE id_user='$id_user'");
}


mysqli_query($koneksi, "INSERT INTO transaksi (id_user, id_produk, jumlah_beli, total_harga, status, metode_pembayaran) 
VALUES ('$id_user', '$id_produk', 1, '$harga', 'pending', '$metode')");

header("location:anggota_dashboard.php");
?>