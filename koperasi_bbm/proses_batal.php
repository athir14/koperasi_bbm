<?php
session_start();
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
    $id_user = $_SESSION['id_user'];

    $cek = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi' AND status = 'pending'");
    $data = mysqli_fetch_assoc($cek);

    if ($data) {
        $harga = $data['total_harga'];
        $metode = $data['metode_pembayaran'];

        if (strpos($metode, 'Saldo') !== false) {
            mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $harga WHERE id_user = '$id_user'");
        }

        $hapus = mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi'");

        if ($hapus) {
            header("location:riwayat_anggota.php?pesan=batal_sukses");
        } else {
            header("location:riwayat_anggota.php?pesan=gagal");
        }
    } else {
        
        header("location:riwayat_anggota.php?pesan=tidak_bisa_batal");
    }
}
?>