<?php
include 'koneksi.php';
if (isset($_GET['kode'])) {
    $kode = $_GET['kode'];
    
    if (strpos($kode, 'TRX-') !== false) {
        $id = str_replace('TRX-', '', $kode);
        $q = mysqli_query($koneksi, "SELECT t.*, u.username, p.nama_produk FROM transaksi t 
            JOIN users u ON t.id_user = u.id_user JOIN produk p ON t.id_produk = p.id_produk 
            WHERE t.id_transaksi = '$id'");
        $d = mysqli_fetch_assoc($q);
        if($d) { $d['tipe'] = 'online'; echo json_encode($d); } else { echo json_encode(null); }
    } else {
        
        $q = mysqli_query($koneksi, "SELECT *, 'toko' as tipe FROM produk WHERE kode_barang = '$kode'");
        $d = mysqli_fetch_assoc($q);
        echo json_encode($d);
    }
}
?>