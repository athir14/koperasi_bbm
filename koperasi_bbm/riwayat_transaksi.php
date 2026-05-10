<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:login.php"); exit();
}

$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$notif = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Terpadu - Admin BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e3e6f0; position: fixed; width: 240px; z-index: 1000; }
        .main-content { margin-left: 240px; padding: 30px; }
        .nav-link-sidebar { color: #4e73df; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; text-decoration: none; display: block; }
        .nav-link-sidebar:hover, .nav-link-sidebar.active { background-color: #4e73df; color: white !important; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .nav-tabs .nav-link { color: #4e73df; font-weight: 600; border: none; border-bottom: 3px solid transparent; cursor: pointer; }
        .nav-tabs .nav-link.active { color: #2e59d9; border-bottom: 3px solid #4e73df; background: none; }
        @media print {
            .sidebar, .no-print, .nav-tabs, .filter-box { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

<div class="sidebar p-3 shadow-sm no-print">
    <div class="text-center mb-4">
        <h4 class="text-primary fw-bold"><i class="fas fa-university"></i> BBM</h4>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link-sidebar" href="admin_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link-sidebar" href="data_anggota.php"><i class="fas fa-users me-2"></i> Data Anggota</a></li>
        <li class="nav-item"><a class="nav-link-sidebar" href="konfirmasi_topup.php"><i class="fas fa-check-circle me-2"></i> Monitoring Saldo</a></li>
        <li class="nav-item"><a class="nav-link-sidebar active" href="riwayat_transaksi.php"><i class="fas fa-file-invoice-dollar me-2"></i> Laporan</a></li>
        <hr>
        <li class="nav-item"><a class="nav-link-sidebar text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h4 fw-bold text-gray-800">Pusat Laporan Koperasi</h2>
            <p class="text-muted small">Periode: <strong><?= date('F', mktime(0,0,0,$bulan_pilihan,10)) ?> <?= $tahun_pilihan ?></strong></p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-primary no-print"><i class="fas fa-print me-2"></i> Cetak</button>
    </div>

    <div class="card card-body mb-4 border-0 shadow-sm filter-box no-print">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="bulan" class="form-select form-select-sm">
                    <?php for($m=1; $m<=12; $m++) { 
                        echo "<option value='".sprintf('%02d',$m)."' ".($m==$bulan_pilihan?'selected':'').">".date('F',mktime(0,0,0,$m,10))."</option>";
                    } ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tahun" class="form-select form-select-sm">
                    <?php for($y=date('Y'); $y>=2024; $y--) { echo "<option value='$y' ".($y==$tahun_pilihan?'selected':'').">$y</option>"; } ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>

    <ul class="nav nav-tabs mb-4 no-print">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#transaksi">Transaksi</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#stok">Stok</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="transaksi">
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Tanggal</th><th>Anggota</th><th>Produk</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($koneksi, "SELECT t.*, u.username, p.nama_produk FROM transaksi t JOIN users u ON t.id_user=u.id_user JOIN produk p ON t.id_produk=p.id_produk WHERE MONTH(tgl_transaksi)='$bulan_pilihan' AND YEAR(tgl_transaksi)='$tahun_pilihan' ORDER BY id_transaksi DESC");
                        while($t = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td><?= $t['tgl_transaksi'] ?></td>
                            <td><?= $t['username'] ?></td>
                            <td><?= $t['nama_produk'] ?></td>
                            <td>Rp <?= number_format($t['total_harga']) ?></td>
                            <td><span class="badge <?= $t['status']=='pending'?'bg-warning':'bg-success' ?>"><?= strtoupper($t['status']) ?></span></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>