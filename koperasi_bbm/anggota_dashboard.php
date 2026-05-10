<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "anggota") {
    header("location:login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];


$query_user = mysqli_query($koneksi, "SELECT saldo FROM users WHERE id_user = '$id_user'");
$data_user = mysqli_fetch_assoc($query_user);
$saldo = $data_user['saldo'];

$keyword = "";
$query_produk = "SELECT * FROM produk";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['search']);
    $query_produk .= " WHERE nama_produk LIKE '%$keyword%'";
}

$query_produk .= " ORDER BY id_produk DESC";
$produk = mysqli_query($koneksi, $query_produk);

$query_cart_count = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM keranjang WHERE id_user = '$id_user'");
$data_cart = mysqli_fetch_assoc($query_cart_count);
$cart_count = $data_cart['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anggota - BBM Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --blue-bbm: #3b71ca; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; padding-bottom: 90px; }
        
        .header-blue { 
            background: linear-gradient(180deg, var(--blue-bbm) 0%, #5a91e6 100%);
            height: 160px; 
            border-bottom-left-radius: 35px; 
            border-bottom-right-radius: 35px; 
            padding: 30px 20px;
            color: white;
        }

        .card-saldo {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: -60px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-box {
            background: white;
            border-radius: 15px;
            padding: 5px 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            padding: 8px;
            font-size: 14px;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: 0.3s;
            position: relative;
        }

        .img-container {
            height: 120px;
            overflow: hidden;
            background: #f1f1f1;
        }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }

        .qty-input-group { width: 90%; margin: 0 auto 10px auto; }
        .btn-qty { padding: 2px 10px; font-weight: bold; }

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

<div class="header-blue text-center">
    <small class="opacity-75 text-uppercase fw-bold">Anggota Koperasi BBM</small>
    <h4 class="fw-bold mt-1"><?= strtoupper($username) ?> 👋</h4>
</div>

<div class="container px-3">
    <div class="card-saldo mb-4">
        <div>
            <small class="text-muted fw-bold d-block mb-1">Saldo BBM Pay</small>
            <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($saldo, 0, ',', '.') ?></h4>
        </div>
        <a href="topup.php" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">TOP UP</a>
    </div>

    <form action="" method="GET">
        <div class="search-box">
            <i class="fas fa-search text-muted"></i>
            <input type="text" name="search" placeholder="Cari barang di sini..." value="<?= htmlspecialchars($keyword) ?>">
            <?php if(!empty($keyword)): ?>
                <a href="anggota_dashboard.php" class="text-danger small ms-2 text-decoration-none">Reset</a>
            <?php endif; ?>
        </div>
    </form>

    <h6 class="fw-bold mb-3">
        <?= !empty($keyword) ? "Hasil Pencarian: '$keyword'" : "<i class='fas fa-shopping-basket text-primary me-2'></i>Katalog Produk" ?>
    </h6>

    <div class="row row-cols-2 g-3">
        <?php if(mysqli_num_rows($produk) > 0): ?>
            <?php while($p = mysqli_fetch_assoc($produk)): ?>
            <div class="col">
                <div class="product-card h-100">
                    <div class="img-container">
                        <img src="img/<?= $p['foto'] ?>" onerror="this.src='https://via.placeholder.com/150'">
                    </div>
                    <div class="p-2 text-center">
                        <h6 class="fw-bold mb-1" style="font-size: 13px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;"><?= $p['nama_produk'] ?></h6>
                        <p class="text-primary fw-bold small mb-1">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                        
                        <p class="mb-2" style="font-size: 10px; color: #666;">Stok: <span class="fw-bold <?= ($p['stok'] > 0) ? 'text-success' : 'text-danger' ?>"><?= $p['stok'] ?></span></p>
                        
                        <?php if($p['stok'] > 0): ?>
                            <div class="input-group input-group-sm qty-input-group">
                                <button class="btn btn-outline-primary btn-qty" type="button" onclick="changeQty('qty<?= $p['id_produk'] ?>', -1)">-</button>
                                <input type="number" id="qty<?= $p['id_produk'] ?>" class="form-control text-center fw-bold border-primary" value="1" min="1" max="<?= $p['stok'] ?>" readonly>
                                <button class="btn btn-outline-primary btn-qty" type="button" onclick="changeQty('qty<?= $p['id_produk'] ?>', 1, <?= $p['stok'] ?>)">+</button>
                            </div>

                            <button onclick="addToCart(<?= $p['id_produk'] ?>)" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">
                                <i class="fas fa-shopping-cart me-1"></i> KERANJANG
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm w-100 rounded-pill fw-bold disabled">
                                HABIS
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-light-emphasis mb-3"></i>
                <p class="text-muted">Produk "<?= $keyword ?>" tidak ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="bottom-nav">
    <a href="anggota_dashboard.php" class="nav-item active">
        <i class="fas fa-home"></i> Beranda
    </a>
    <a href="keranjang_view.php" class="nav-item">
        <i class="fas fa-shopping-cart"></i> 
        <?php if($cart_count > 0): ?>
            <span class="badge-cart"><?= $cart_count ?></span>
        <?php endif; ?>
        Keranjang
    </a>
    <a href="riwayat_anggota.php" class="nav-item">
        <i class="fas fa-file-invoice"></i> Riwayat
    </a>
    <a href="logout.php" class="nav-item text-danger">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </a>
</div>

<script>
    
    function changeQty(inputId, delta, maxStok) {
        let input = document.getElementById(inputId);
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (maxStok !== undefined && val > maxStok) val = maxStok;
        input.value = val;
    }

    function addToCart(idProduk) {
        let qty = document.getElementById('qty' + idProduk).value;
        window.location.href = "tambah_keranjang.php?id=" + idProduk + "&qty=" + qty;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>