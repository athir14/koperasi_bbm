<?php
include 'koneksi.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi='$id'");
    $t = mysqli_fetch_assoc($query);
    
    $id_user = $t['id_user'];
    $id_produk = $t['id_produk'];
    $total = $t['total_harga'];

    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $total WHERE id_user='$id_user'");

    mysqli_query($koneksi, "UPDATE produk SET stok = stok - 1 WHERE id_produk='$id_produk'");

    $update_status = mysqli_query($koneksi, "UPDATE transaksi SET status='selesai' WHERE id_transaksi='$id'");

    if($update_status) {
        header("location:kasir_dashboard.php?pesan=sukses");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>