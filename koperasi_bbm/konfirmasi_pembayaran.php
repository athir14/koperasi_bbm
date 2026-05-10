<?php
include 'koneksi.php';

if (isset($_GET['id_transaksi'])) {
    $id_t = mysqli_real_escape_string($koneksi, $_GET['id_transaksi']);

    $query_ambil = mysqli_query($koneksi, "SELECT id_produk, jumlah_beli FROM transaksi WHERE id_transaksi = '$id_t'");
    $data = mysqli_fetch_assoc($query_ambil);

    if ($data) {
        $id_p = $data['id_produk'];
        $qty  = $data['jumlah_beli'];

        if (empty($qty) || !is_numeric($qty)) { $qty = 0; }

        $update_status = mysqli_query($koneksi, "UPDATE transaksi SET status = 'selesai' WHERE id_transaksi = '$id_t'");

        if ($update_status) {
    
            $sql_stok = "UPDATE produk SET stok = stok - $qty WHERE id_produk = '$id_p'";
            
            if (mysqli_query($koneksi, $sql_stok)) {
                echo "<script>alert('Transaksi Berhasil Diselesaikan!'); window.location='kasir_dashboard.php';</script>";
            } else {
               
                echo "Gagal update stok: " . mysqli_error($koneksi);
            }
        } else {
            echo "Gagal update status transaksi: " . mysqli_error($koneksi);
        }
    } else {
        echo "<script>alert('Data transaksi tidak ditemukan!'); window.location='kasir_dashboard.php';</script>";
    }
} else {
    header("location:kasir_dashboard.php");
}
?>