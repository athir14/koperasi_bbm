<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") { 
    header("location:login.php"); 
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBM Pay - Kasir Terminal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --blue-dark: #004a99; --blue-main: #007bff; --blue-soft: #f0f7ff; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        
        .header-app { 
            background: linear-gradient(180deg, var(--blue-dark) 0%, var(--blue-main) 100%); 
            padding: 40px 20px 70px 20px; color: white; border-radius: 0 0 30px 30px;
            text-align: center;
        }

        .scan-card {
            background: white; border-radius: 20px; padding: 25px;
            margin-top: -50px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: none;
        }

        .input-scan {
            border: 2px solid var(--blue-main);
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.2rem;
            text-align: center;
            background-color: var(--blue-soft);
        }

        .detail-card {
            border: none; border-radius: 20px; background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .status-box {
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .status-lunas { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .status-tagihan { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
    </style>
</head>
<body>

<div class="header-app">
    <h5 class="fw-bold mb-1"><i class="fas fa-barcode me-2"></i>KASIR TERMINAL</h5>
    <p class="mb-0 opacity-75 small text-uppercase tracking-wider">Koperasi BBM Mobile</p>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="scan-card mb-4">
                <form action="" method="GET">
                    <label class="small text-muted mb-2 d-block text-center fw-bold">SCAN ATAU KETIK KODE STRUK</label>
                    <input type="text" name="kode" id="kode_input" class="form-control input-scan mb-3" placeholder="BBM-XXXXXXXX" autofocus autocomplete="off">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 12px;">
                        <i class="fas fa-search me-1"></i> CEK TRANSAKSI
                    </button>
                </form>
            </div>

            <?php
            if (isset($_GET['kode']) && !empty($_GET['kode'])) {
                $kode = mysqli_real_escape_string($koneksi, $_GET['kode']);
                
                $query = mysqli_query($koneksi, "SELECT t.*, u.username, p.nama_produk 
                                                FROM transaksi t 
                                                JOIN users u ON t.id_user = u.id_user 
                                                JOIN produk p ON t.id_produk = p.id_produk 
                                                WHERE t.kode_transaksi = '$kode' AND t.status = 'pending'");

                if (mysqli_num_rows($query) > 0) {
                    $total = 0;
                    $items = [];
                    while($row = mysqli_fetch_assoc($query)) {
                        $items[] = $row;
                        $total += $row['total_harga'];
                        $metode = $row['metode_pembayaran'];
                        $nama = $row['username'];
                    }
            ?>
                <div class="detail-card p-4 animate__animated animate__fadeIn">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted small d-block">Pembeli</span>
                            <h6 class="fw-bold mb-0 text-uppercase"><?= $nama ?></h6>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block">Kode Struk</span>
                            <span class="badge bg-dark"><?= $kode ?></span>
                        </div>
                    </div>

                    <?php if($metode == "Saldo") : ?>
                        <div class="status-box status-lunas text-center">
                            <i class="fas fa-check-circle mb-1 d-block fa-lg"></i>
                            SUDAH DIBAYAR (SALDO)
                            <small class="d-block opacity-75 fw-normal">Langsung serahkan barang ke anggota</small>
                        </div>
                    <?php else : ?>
                        <div class="status-box status-tagihan text-center">
                            <i class="fas fa-exclamation-circle mb-1 d-block fa-lg"></i>
                            PEMBAYARAN TUNAI (CASH)
                            <small class="d-block opacity-75 fw-normal">Terima uang tunai sebesar Rp <?= number_format($total) ?></small>
                        </div>
                    <?php endif; ?>

                    <h6 class="fw-bold small mb-2 text-muted">DAFTAR BARANG:</h6>
                    <div class="list-group list-group-flush mb-4">
                        <?php foreach($items as $item) : ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent">
                                <div>
                                    <span class="d-block small fw-bold"><?= $item['nama_produk'] ?></span>
                                    <small class="text-muted"><?= $item['jumlah'] ?> pcs x Rp <?= number_format($item['total_harga']/$item['jumlah']) ?></small>
                                </div>
                                <span class="fw-bold text-primary small">Rp <?= number_format($item['total_harga']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center bg-transparent border-0 pt-3">
                            <span class="fw-bold">TOTAL AKHIR</span>
                            <h5 class="fw-bold text-primary mb-0">Rp <?= number_format($total) ?></h5>
                        </div>
                    </div>

                    <a href="proses_konfirmasi_transaksi.php?kode=<?= $kode ?>" class="btn btn-success w-100 py-3 fw-bold shadow" style="border-radius: 15px;">
                        KONFIRMASI SELESAI
                    </a>
                    <a href="kasir_scan.php" class="btn btn-link w-100 text-muted mt-2 text-decoration-none small">Batal</a>
                </div>
            <?php
                } else {
                    echo '<div class="alert alert-danger border-0 rounded-4 text-center mt-3 shadow-sm">
                            <i class="fas fa-search mb-2 d-block fa-2x"></i>
                            Kode struk tidak ditemukan atau sudah diproses.
                          </div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
   
    window.onload = function() {
        document.getElementById('kode_input').focus();
    };
</script>
</body>
</html>