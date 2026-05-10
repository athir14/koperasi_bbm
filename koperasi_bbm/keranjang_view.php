<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "anggota") {
    header("location:login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];

$u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user = '$id_user'"));
$saldo = $u['saldo'];

$query_cart = mysqli_query($koneksi, "SELECT k.*, p.nama_produk, p.harga, p.foto FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.id_user = '$id_user'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - BBM Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --blue-bbm: #3b71ca; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; padding-bottom: 100px; }
        
        .header-blue { 
            background: linear-gradient(180deg, var(--blue-bbm) 0%, #5a91e6 100%);
            height: 160px; border-bottom-left-radius: 35px; border-bottom-right-radius: 35px; 
            padding: 30px 20px; color: white;
        }

        .card-saldo {
            background: white; border-radius: 20px; padding: 20px; margin-top: -60px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex;
            justify-content: space-between; align-items: center;
        }

        .cart-card {
            background: white; border-radius: 15px; border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 15px;
        }

        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: white; display: flex; justify-content: space-around;
            padding: 12px 0; border-top: 1px solid #eee; z-index: 1000;
        }
        .nav-item { text-align: center; color: #bbb; text-decoration: none; font-size: 10px; flex: 1; }
        .nav-item i { font-size: 20px; display: block; margin-bottom: 3px; }
        .nav-item.active { color: var(--blue-bbm); }
    </style>
</head>
<body>

<div class="header-blue text-center">
    <small class="opacity-75 text-uppercase fw-bold">Keranjang Belanja</small>
    <h4 class="fw-bold mt-1">Konfirmasi Pesanan 🛒</h4>
</div>

<div class="container px-3">
    <div class="card-saldo mb-4">
        <div>
            <small class="text-muted fw-bold d-block mb-1">Saldo BBM Pay</small>
            <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($saldo, 0, ',', '.') ?></h4>
        </div>
        <button class="btn btn-primary rounded-pill px-4 fw-bold">CHECKOUT</button>
    </div>

    <div class="mt-4">
        <?php 
        $total_bayar = 0;
        if(mysqli_num_rows($query_cart) > 0):
            while($c = mysqli_fetch_assoc($query_cart)): 
                $sub = $c['harga'] * $c['jumlah'];
                $total_bayar += $sub;
        ?>
        <div class="card cart-card p-3">
            <div class="d-flex align-items-center">
                <img src="img/<?= $c['foto'] ?>" class="rounded" style="width: 65px; height: 65px; object-fit: cover;">
                <div class="ms-3 flex-grow-1">
                    <h6 class="fw-bold mb-0" style="font-size: 14px;"><?= $c['nama_produk'] ?></h6>
                    <small class="text-muted"><?= $c['jumlah'] ?> x Rp <?= number_format($c['harga']) ?></small>
                    <div class="text-primary fw-bold">Rp <?= number_format($sub) ?></div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>

        <div class="card p-3 border-0 shadow-sm mb-5">
            <form action="proses_checkout.php" method="POST">
                <label class="small fw-bold text-muted mb-2">Pilih Metode Pembayaran:</label>
                <select name="metode" class="form-select mb-3 fw-bold text-primary" required>
                    <option value="Cash">💵 Bayar Cash di Kasir</option>
                    <option value="Saldo">💳 Potong Saldo BBM Pay</option>
                </select>
                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 rounded-pill">
                    BAYAR (Rp <?= number_format($total_bayar) ?>)
                </button>
            </form>
        </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">Keranjang Kosong</div>
        <?php endif; ?>
    </div>
</div>

<div class="bottom-nav">
    <a href="anggota_dashboard.php" class="nav-item"><i class="fas fa-home"></i> Beranda</a>
    <a href="keranjang_view.php" class="nav-item active"><i class="fas fa-shopping-cart"></i> Keranjang</a>
    <a href="riwayat_anggota.php" class="nav-item"><i class="fas fa-file-invoice"></i> Riwayat</a>
    <a href="logout.php" class="nav-item text-danger"><i class="fas fa-sign-out-alt"></i> Keluar</a>
</div>

</body>
</html>