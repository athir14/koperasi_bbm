<?php
include 'koneksi.php';
header('Content-Type: application/json');

$kode = isset($_GET['kode']) ? trim(mysqli_real_escape_string($koneksi, $_GET['kode'])) : '';

if (!$kode) {
    echo json_encode(['status' => 'error', 'message' => 'Kode kosong']);
    exit;
}

$q_struk = mysqli_query($koneksi, "SELECT t.*, p.nama_produk, p.harga 
                                   FROM transaksi t 
                                   JOIN produk p ON t.id_produk = p.id_produk 
                                   WHERE t.kode_transaksi = '$kode' AND t.status = 'pending'");

if (mysqli_num_rows($q_struk) > 0) {
    $items = [];
    while($r = mysqli_fetch_assoc($q_struk)) {
        $items[] = [
            'id_produk' => $r['id_produk'],
            'nama_produk' => $r['nama_produk'],
            'harga' => (int)$r['harga'],
            'qty' => (int)$r['jumlah'],
            'total' => (int)$r['total_harga']
        ];
    }
    echo json_encode(['status' => 'struk', 'items' => $items]);
    exit;
}

$q_prod = mysqli_query($koneksi, "SELECT * FROM produk WHERE kode_barang = '$kode' OR id_produk = '$kode'");
$d = mysqli_fetch_assoc($q_prod);

if ($d) {
    echo json_encode([
        'status' => 'produk', 
        'id_produk' => $d['id_produk'], 
        'nama_produk' => $d['nama_produk'], 
        'harga' => (int)$d['harga']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Kode ' . $kode . ' tidak terdaftar']);
}
?>