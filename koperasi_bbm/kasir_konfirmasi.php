<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("location:login.php"); exit();
}

if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id = $_GET['id'];
    if ($_GET['aksi'] == 'setuju') {
        $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM topup WHERE id_topup='$id'"));
        $user_id = $data['id_user'];
        $jumlah = $data['jumlah'];

        mysqli_query($koneksi, "UPDATE users SET saldo = saldo + $jumlah WHERE id_user='$user_id'");
        mysqli_query($koneksi, "UPDATE topup SET status='success' WHERE id_topup='$id'");
        
        echo "<script>alert('Saldo berhasil dikonfirmasi!'); window.location='kasir_konfirmasi.php';</script>";
    }
}

$nama_petugas = $_SESSION['username'] ?? 'Petugas';
$notif_topup = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Saldo - BBM PAY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e3e6f0; position: fixed; width: 260px; z-index: 1000; padding: 20px; }
        .sidebar-brand { color: #4e73df; font-weight: 800; font-size: 1.5rem; margin-bottom: 10px; display: block; text-decoration: none; }
        
        .sidebar-divider { 
            margin: 15px 10px; 
            border: 0;
            border-top: 1.5px solid #000000; 
            opacity: 1;
        }

        .nav-link { color: #4e73df; font-weight: 600; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; transition: 0.2s; text-decoration: none; }
        .nav-link:hover { background: #f8f9fc; }
        .nav-link.active { background: #4e73df; color: white !important; box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2); }
        .nav-link i { width: 25px; font-size: 1.1rem; }
        .badge-notif { background: #e74a3b; color: white; border-radius: 50%; padding: 2px 7px; font-size: 0.7rem; margin-left: auto; }
        
        .main-content { margin-left: 260px; padding: 40px; }
        
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; background: white; }
        .card-header-blue { background: #4e73df; color: white; padding: 20px; border: none; }
        .card-title-text { font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem; margin: 0; }
        
        .list-group-item { border-left: none; border-right: none; padding: 20px; border-bottom: 1px solid #f0f0f0; transition: 0.2s; }
        .list-group-item:hover { background-color: #fcfcfc; }
        .btn-confirm { background: #1a1d23; color: white; border: none; border-radius: 10px; padding: 10px 25px; font-weight: 600; transition: 0.3s; }
        .btn-confirm:hover { background: #000; transform: translateY(-2px); }
        .text-nominal { color: #e74a3b; font-weight: 800; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="sidebar shadow-sm">
    <a href="#" class="sidebar-brand text-center">
        <i class="fas fa-university"></i> BBM PAY
    </a>
    
    <hr class="sidebar-divider"> 

    <nav class="nav flex-column">
        <a class="nav-link" href="kasir_dashboard.php">
            <i class="fas fa-cash-register"></i> Kasir Terminal
        </a>
        <a class="nav-link active" href="kasir_konfirmasi.php">
            <i class="fas fa-hand-holding-usd"></i> Konfirmasi Saldo
            <?php if($notif_topup > 0): ?>
                <span class="badge-notif"><?= $notif_topup ?></span>
            <?php endif; ?>
        </a>
        <a class="nav-link" href="kasir_stok.php">
            <i class="fas fa-boxes"></i> Stok Barang
        </a>
    </nav>
    
    <hr class="sidebar-divider"> 

    <div class="mt-3 px-2">
        <small class="text-muted d-block px-2 mb-2">Petugas: <strong><?= $nama_petugas ?></strong></small>
        <a class="nav-link text-danger fw-bold" href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header-blue">
                <h6 class="card-title-text">ANTREAN KONFIRMASI SALDO (TOP UP)</h6>
            </div>
            
            <div class="list-group list-group-flush">
                <?php
                $q = mysqli_query($koneksi, "SELECT t.*, u.username FROM topup t JOIN users u ON t.id_user = u.id_user WHERE t.status='pending' ORDER BY t.id_topup DESC");
                
                if(mysqli_num_rows($q) == 0) {
                    echo "<div class='p-5 text-center text-muted'>
                            <i class='fas fa-check-circle fa-3x mb-3 text-light'></i>
                            <p>Tidak ada antrean top up saat ini.</p>
                          </div>";
                }

                while($row = mysqli_fetch_assoc($q)){
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="mb-1">
                            <span class="fw-bold text-dark h5"><?= strtoupper($row['username']) ?></span> 
                            <span class="text-muted ms-2 small">| <?= date('d M Y - H:i', strtotime($row['tgl_topup'])) ?></span>
                        </div>
                        <div class="text-nominal">Rp <?= number_format($row['jumlah']) ?></div>
                    </div>
                    
                    <a href="?id=<?= $row['id_topup'] ?>&aksi=setuju" 
                       class="btn-confirm text-decoration-none"
                       onclick="return confirm('Apakah Anda sudah menerima uang tunai dari <?= $row['username'] ?>?')">
                        Konfirmasi
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>