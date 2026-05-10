<?php
include 'koneksi.php';

$kode = $_GET['kode'];

$query = mysqli_query($koneksi, "SELECT t.*, p.nama_produk, p.harga, u.username 
                                 FROM transaksi t 
                                 JOIN produk p ON t.id_produk = p.id_produk 
                                 JOIN users u ON t.id_user = u.id_user 
                                 WHERE t.kode_struk = '$kode'");
$data_awal = mysqli_fetch_assoc($query);
mysqli_data_seek($query, 0); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran - <?= $kode ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; font-size: 12px; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; }
        .text-right { text-align: right; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.history.back()">Kembali</button>
        <hr>
    </div>

    <div class="text-center">
        <h3 style="margin-bottom: 0;">BBM PAY MOBILE</h3>
        <p style="margin-top: 5px;">Terminal Kasir Pintar</p>
    </div>

    <div class="divider"></div>
    
    <table>
        <tr><td>No. Struk</td><td>: <?= $kode ?></td></tr>
        <tr><td>Kasir</td><td>: <?= $_SESSION['username'] ?? 'Admin' ?></td></tr>
        <tr><td>Pelanggan</td><td>: <?= $data_awal['username'] ?></td></tr>
        <tr><td>Tanggal</td><td>: <?= date('d/m/Y H:i') ?></td></tr>
    </table>

    <div class="divider"></div>

    <table>
        <?php 
        $grand_total = 0;
        while($r = mysqli_fetch_assoc($query)): 
            $grand_total += $r['total_harga'];
        ?>
        <tr>
            <td colspan="2"><?= $r['nama_produk'] ?></td>
        </tr>
        <tr>
            <td><?= $r['jumlah'] ?> x <?= number_format($r['harga']) ?></td>
            <td class="text-right"><?= number_format($r['total_harga']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="divider"></div>

    <table>
        <tr style="font-weight: bold;">
            <td>TOTAL</td>
            <td class="text-right">Rp <?= number_format($grand_total) ?></td>
        </tr>
        <tr>
            <td>Metode</td>
            <td class="text-right"><?= $data_awal['metode_pembayaran'] ?></td>
        </tr>
    </table>

    <div class="divider"></div>
    <div class="text-center">
        <p>Terima Kasih Atas Kunjungan Anda<br>Simpan struk ini sebagai bukti pembayaran</p>
    </div>
</body>
</html>