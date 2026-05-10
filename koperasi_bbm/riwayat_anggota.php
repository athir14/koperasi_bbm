<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "anggota") {
    header("location:login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

$query_cart_count = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM keranjang WHERE id_user = '$id_user'");
$data_cart = mysqli_fetch_assoc($query_cart_count);
$cart_count = $data_cart['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - BBM Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --blue-bbm: #3b71ca; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; padding-bottom: 90px; }
        
        .header-blue { 
            background: linear-gradient(180deg, var(--blue-bbm) 0%, #5a91e6 100%);
            padding: 20px;
            color: white;
            text-align: center;
            font-weight: bold;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .product-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            border-top: 1px solid #eee;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.05);
            z-index: 1000;
        }
        .nav-item { text-align: center; color: #bbb; text-decoration: none; font-size: 10px; flex: 1; position: relative; }
        .nav-item i { font-size: 20px; display: block; margin-bottom: 3px; }
        .nav-item.active { color: var(--blue-bbm); }
        
        .badge-cart {
            position: absolute;
            top: -5px;
            right: 25%;
            background: #ff4757;
            color: white;
            font-size: 9px;
            padding: 2px 5px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="header-blue">
    RIWAYAT PESANAN
</div>

<div class="container mt-3 px-3">
    <?php

    $q = mysqli_query($koneksi, "SELECT t.*, p.nama_produk, p.foto 
                                 FROM transaksi t 
                                 JOIN produk p ON t.id_produk = p.id_produk 
                                 WHERE t.id_user = '$id_user' 
                                 ORDER BY t.id_transaksi DESC");
    
    if(mysqli_num_rows($q) > 0) {
        while($row = mysqli_fetch_assoc($q)) { 
            $status = strtolower($row['status']);
    ?>
    <div class="card product-card">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="img/<?= $row['foto'] ?>" width="55" height="55" class="rounded-3 me-3" style="object-fit: cover;" onerror="this.src='https://via.placeholder.com/100'">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><?= $row['nama_produk'] ?></h6>
                        <small class="text-muted" style="font-size: 11px;">
                            <?= date('H:i • d M Y', strtotime($row['tgl_transaksi'])) ?>
                        </small>
                    </div>
                </div>
                <div class="text-end">
                    <h6 class="fw-bold text-primary mb-1">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></h6>
                    
                    <?php if ($status == 'pending'): ?>
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge bg-warning text-dark mb-2" style="font-size: 10px;">PENDING</span>
                            <a href="proses_batal.php?id=<?= $row['id_transaksi'] ?>" 
                               class="btn btn-sm btn-danger fw-bold shadow-sm" 
                               style="font-size: 9px; padding: 2px 8px; border-radius: 20px;"
                               onclick="return confirm('Batalkan pesanan?')">BATALKAN</a>
                        </div>
                    <?php else: ?>
                        <span class="badge bg-success" style="font-size: 10px;">SELESAI</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
        } 
    } else {
        echo '<div class="text-center py-5 text-muted">Belum ada riwayat pesanan.</div>';
    }
    ?>
</div>

<div class="bottom-nav">
    <a href="anggota_dashboard.php" class="nav-item">
        <i class="fas fa-home"></i> Beranda
    </a>
    <a href="keranjang_view.php" class="nav-item">
        <i class="fas fa-shopping-cart"></i> 
        <?php if($cart_count > 0): ?>
            <span class="badge-cart"><?= $cart_count ?></span>
        <?php endif; ?>
        Keranjang
    </a>
    <a href="riwayat_anggota.php" class="nav-item active">
        <i class="fas fa-file-invoice"></i> Riwayat
    </a>
    <a href="logout.php" class="nav-item text-danger">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

</body>
</html>