<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:login.php"); 
    exit();
}

$query_notif = mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'");
$notif = mysqli_num_rows($query_notif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Koperasi BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e3e6f0; position: fixed; width: 240px; z-index: 1000; }
        .main-content { margin-left: 240px; padding: 30px; }
        .nav-link { color: #4e73df; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #4e73df; color: white !important; box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2); }
        .card-stat { border: none; border-radius: 15px; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
        .table-container { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .img-produk { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

<div class="sidebar p-3 shadow-sm">
    <div class="text-center mb-4">
        <h4 class="text-primary fw-bold"><i class="fas fa-university"></i> BBM</h4>
        <small class="text-muted text-uppercase fw-bold">Pusat Kendali Admin</small>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="admin_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="data_anggota.php"><i class="fas fa-users me-2"></i> Data Anggota</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="konfirmasi_topup.php">
                <i class="fas fa-check-circle me-2"></i> Monitoring Saldo
                <?php if($notif > 0): ?>
                    <span class="badge bg-danger ms-1 shadow-sm"><?= $notif ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="riwayat_transaksi.php"><i class="fas fa-file-invoice-dollar me-2"></i> Laporan</a>
        </li>
        <hr>
        <li class="nav-item">
            <a class="nav-link text-danger fw-bold" href="logout.php" onclick="return confirm('Keluar dari sistem?')">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    
    <?php if($notif > 0): ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-3 fa-2x text-warning"></i>
        <div>
            <h6 class="mb-0 fw-bold">Perhatian Admin!</h6>
            <span>Ada <strong><?= $notif ?> permintaan saldo</strong> yang menunggu konfirmasi. <a href="konfirmasi_topup.php" class="alert-link text-decoration-none">Proses Sekarang &rarr;</a></span>
        </div>
    </div>
    <?php endif; ?>

    <h2 class="h4 fw-bold text-gray-800 mb-4">Ringkasan Sistem</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white shadow-sm border-start border-primary border-4">
                <small class="text-muted fw-bold">TOTAL ANGGOTA</small>
                <h3 class="fw-bold mb-0 text-primary">
                    <?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users WHERE role='anggota'")) ?>
                </h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white shadow-sm border-start border-success border-4">
                <small class="text-muted fw-bold">PRODUK AKTIF</small>
                <h3 class="fw-bold mb-0 text-success">
                    <?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM produk")) ?>
                </h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white shadow-sm border-start border-warning border-4">
                <small class="text-muted fw-bold">TOP UP PENDING</small>
                <h3 class="fw-bold mb-0 text-warning"><?= $notif ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white shadow-sm border-start border-info border-4">
                <small class="text-muted fw-bold">SALDO BEREDAR</small>
                <?php 
                $query_saldo = mysqli_query($koneksi, "SELECT SUM(saldo) as total FROM users");
                $s = mysqli_fetch_assoc($query_saldo); 
                ?>
                <h3 class="fw-bold mb-0 text-info">Rp <?= number_format($s['total'] ?? 0, 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Daftar Produk Koperasi</h5>
            <a href="tambah_produk.php" class="btn btn-primary px-3 shadow-sm btn-sm fw-bold">
                <i class="fas fa-plus me-1"></i> Tambah Produk
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $p = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id_produk DESC");
                    while($d = mysqli_fetch_assoc($p)){
                    ?>
                    <tr>
                        <td>
                            <?php if(!empty($d['foto'])): ?>
                                <img src="img/<?= $d['foto'] ?>" class="img-produk shadow-sm">
                            <?php else: ?>
                                <div class="img-produk bg-light d-flex align-items-center justify-content-center border"><i class="fas fa-image text-muted"></i></div>
                            <?php endif; ?>
                        </td>
                        <td class="small fw-bold text-muted"><?= $d['kode_barang'] ?></td>
                        <td class="fw-bold"><?= $d['nama_produk'] ?></td>
                        <td class="text-primary fw-bold">Rp <?= number_format($d['harga'], 0, ',', '.') ?></td>
                        <td>
                            <?php if($d['stok'] <= 5): ?>
                                <span class="badge bg-danger">Hampir Habis: <?= $d['stok'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border"><?= $d['stok'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="edit_produk.php?id=<?= $d['id_produk'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="hapus_produk.php?id=<?= $d['id_produk'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>