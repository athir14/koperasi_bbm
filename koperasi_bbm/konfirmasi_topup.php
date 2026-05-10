<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:login.php"); exit();
}

$notif_count = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Saldo - Admin BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e3e6f0; position: fixed; width: 240px; z-index: 1000; }
        .main-content { margin-left: 240px; padding: 30px; transition: 0.3s; }
        .nav-link { color: #4e73df; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background-color: #4e73df; color: white !important; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        @media print {
            .sidebar, .no-print, .badge.bg-danger { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
            .table-container { box-shadow: none !important; border: 1px solid #eee !important; }
            .card-header-print { display: block !important; text-align: center; margin-bottom: 20px; }
            body { background-color: white; }
            .status-badge { border: 1px solid #000 !important; color: #000 !important; }
        }
        .card-header-print { display: none; }
    </style>
</head>
<body>

<div class="sidebar p-3 shadow-sm no-print">
    <div class="text-center mb-4">
        <h4 class="text-primary fw-bold"><i class="fas fa-university"></i> BBM</h4>
        <small class="text-muted text-uppercase fw-bold">Pusat Kendali Admin</small>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="data_anggota.php"><i class="fas fa-users me-2"></i> Data Anggota</a></li>
        <li class="nav-item">
            <a class="nav-link active" href="konfirmasi_topup.php">
                <i class="fas fa-check-circle me-2"></i> Monitoring Saldo
                <?php if($notif_count > 0): ?>
                    <span class="badge bg-danger ms-1 shadow-sm"><?= $notif_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="riwayat_transaksi.php"><i class="fas fa-file-invoice-dollar me-2"></i> Laporan</a></li>
        <hr>
        <li class="nav-item"><a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    
    <div class="card-header-print">
        <h2 class="fw-bold">LAPORAN MONITORING SALDO ANGGOTA</h2>
        <p>Koperasi Bina Bangkit Mandiri (BBM) - Dicetak pada: <?= date('d/m/Y H:i') ?></p>
        <hr>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="h4 fw-bold text-gray-800 mb-0">Log Transaksi Saldo</h2>
            <p class="text-muted small">Riwayat pengisian saldo yang diproses oleh sistem/kasir.</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary shadow-sm fw-bold">
            <i class="fas fa-print me-2"></i> CETAK LAPORAN SALDO
        </button>
    </div>

    <div class="table-container">
        
        <h6 class="fw-bold mb-3 text-warning"><i class="fas fa-spinner fa-spin me-2"></i> Sedang Diproses Kasir</h6>
        <div class="table-responsive mb-5">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Username Anggota</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_pending = "SELECT topup.*, users.username FROM topup 
                                     JOIN users ON topup.id_user = users.id_user 
                                     WHERE topup.status = 'pending' 
                                     ORDER BY topup.id_topup DESC";
                    $res_pending = mysqli_query($koneksi, $query_pending);
                    
                    if(mysqli_num_rows($res_pending) == 0) {
                        echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada transaksi yang sedang diproses.</td></tr>";
                    }

                    while($d = mysqli_fetch_assoc($res_pending)) {
                    ?>
                    <tr>
                        <td class="small"><?= $d['tgl_topup'] ?></td>
                        <td class="fw-bold"><?= $d['username'] ?></td>
                        <td class="text-success fw-bold">Rp <?= number_format($d['jumlah']) ?></td>
                        <td><i class="fas fa-wallet me-1 text-muted"></i> <?= $d['metode'] ?></td>
                        <td><span class="badge bg-warning text-dark px-3 status-badge">Menunggu Kasir</span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <h6 class="fw-bold mb-3 text-success"><i class="fas fa-history me-2"></i> Top Up Berhasil (Riwayat Selesai)</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Waktu Selesai</th>
                        <th>Anggota</th>
                        <th>Nominal</th>
                        <th class="text-end">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_log = "SELECT topup.*, users.username FROM topup 
                                  JOIN users ON topup.id_user = users.id_user 
                                  WHERE topup.status = 'berhasil' 
                                  ORDER BY topup.id_topup DESC LIMIT 20";
                    $res_log = mysqli_query($koneksi, $query_log);
                    
                    while($l = mysqli_fetch_assoc($res_log)) {
                    ?>
                    <tr>
                        <td class="small"><?= date('d/m H:i', strtotime($l['tgl_topup'])) ?></td>
                        <td><?= $l['username'] ?></td>
                        <td class="fw-bold text-primary">+ Rp <?= number_format($l['jumlah']) ?></td>
                        <td class="text-end text-success small fw-bold">Diterima Kasir <i class="fas fa-check-double ms-1"></i></td>
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