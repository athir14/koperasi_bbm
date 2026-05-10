<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("location:login.php"); exit();
}

$nama_petugas = $_SESSION['username'] ?? 'Petugas';
$notif_topup = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Barang - BBM PAY</title>
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
        
        .badge-stok { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: bold; font-size: 1.1rem; }
        .search-box { max-width: 400px; border-radius: 12px; border: 1px solid #e3e6f0; padding: 10px 20px; outline: none; }
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
        <a class="nav-link" href="kasir_konfirmasi.php">
            <i class="fas fa-hand-holding-usd"></i> Konfirmasi Saldo
            <?php if($notif_topup > 0): ?><span class="badge-notif"><?= $notif_topup ?></span><?php endif; ?>
        </a>
        <a class="nav-link active" href="kasir_stok.php">
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark m-0">Monitoring Stok</h4>
        <input type="text" id="cariBarang" class="search-box shadow-sm" placeholder="Cari nama produk..." oninput="filterStok()">
    </div>

    <div class="card card-custom">
        <div class="card-header-blue">
            <h6 class="card-title-text">DAFTAR KETERSEDIAAN BARANG</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelStok">
                <thead class="table-light">
                    <tr class="small text-uppercase">
                        <th class="ps-4">No</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Sisa Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama_produk ASC");
                    while($row = mysqli_fetch_assoc($query)){
                        $stok = $row['stok'];
                        
                        if($stok <= 0) {
                            $status = '<span class="badge bg-danger rounded-pill px-3">Habis</span>';
                            $bg_box = 'bg-danger text-white';
                        } elseif($stok <= 5) {
                            $status = '<span class="badge bg-warning text-dark rounded-pill px-3">Stok Hampir Habis</span>';
                            $bg_box = 'bg-warning text-dark';
                        } else {
                            $status = '<span class="badge bg-success rounded-pill px-3">Tersedia</span>';
                            $bg_box = 'bg-primary text-white';
                        }
                    ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <td class="fw-bold nama-produk"><?= strtoupper($row['nama_produk']) ?></td>
                        <td class="text-center d-flex justify-content-center py-3">
                            <div class="badge-stok <?= $bg_box ?> shadow-sm"><?= $stok ?></div>
                        </td>
                        <td><?= $status ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterStok() {
        const input = document.getElementById('cariBarang').value.toLowerCase();
        const rows = document.querySelectorAll('#tabelStok tbody tr');
        rows.forEach(row => {
            const nama = row.querySelector('.nama-produk').textContent.toLowerCase();
            row.style.display = nama.includes(input) ? "" : "none";
        });
    }
</script>

</body>
</html>