<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("location:login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$metode = isset($_POST['metode']) ? $_POST['metode'] : 'Cash'; 
$kode_struk = "BBM-" . strtoupper(substr(md5(time()), 0, 8)); 

$query_keranjang = mysqli_query($koneksi, "SELECT k.*, p.harga, p.stok FROM keranjang k 
                                           JOIN produk p ON k.id_produk = p.id_produk 
                                           WHERE k.id_user = '$id_user'");

if (mysqli_num_rows($query_keranjang) == 0) {
    echo "<script>alert('Keranjang kosong!'); window.location='anggota_dashboard.php';</script>";
    exit();
}

$total_seluruhnya = 0;
$items = [];
while ($row = mysqli_fetch_assoc($query_keranjang)) {
    
    $jumlah_beli = isset($row['qty']) ? $row['qty'] : $row['jumlah']; 

    if ($row['stok'] < $jumlah_beli) {
        echo "<script>alert('Stok tidak mencukupi!'); window.location='keranjang_view.php';</script>";
        exit();
    }
    $total_seluruhnya += ($row['harga'] * $jumlah_beli);
    $row['jumlah_fix'] = $jumlah_beli; // Simpan untuk insert
    $items[] = $row;
}

if ($metode == "Saldo") {
    $u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user = '$id_user'"));
    if ($u['saldo'] < $total_seluruhnya) {
        echo "<script>alert('Saldo tidak mencukupi!'); window.location='keranjang_view.php';</script>";
        exit();
    }
    mysqli_query($koneksi, "UPDATE users SET saldo = saldo - $total_seluruhnya WHERE id_user = '$id_user'");
}

$success = true;
foreach ($items as $item) {
    $id_p = $item['id_produk'];
    $qty = $item['jumlah_fix'];
    $subtotal = $item['harga'] * $qty;

    $query_ins = "INSERT INTO transaksi (id_user, kode_transaksi, id_produk, jumlah, total_harga, metode_pembayaran, status, tgl_transaksi) 
                  VALUES ('$id_user', '$kode_struk', '$id_p', '$qty', '$subtotal', '$metode', 'pending', NOW())";
    
    if (!mysqli_query($koneksi, $query_ins)) {
        $success = false;
        
        die("Error: " . mysqli_error($koneksi));
    } else {
        mysqli_query($koneksi, "UPDATE produk SET stok = stok - $qty WHERE id_produk = '$id_p'");
    }
}

if ($success) {
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_user = '$id_user'");
    echo "<script>alert('Berhasil! Kode: $kode_struk'); window.location='anggota_dashboard.php';</script>";
}
?>