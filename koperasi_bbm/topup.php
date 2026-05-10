<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "anggota") {
    header("location:login.php"); exit();
}

$id_user = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up Saldo - BBM Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --blue-bbm: #3b71ca; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .header-blue { background: linear-gradient(180deg, var(--blue-bbm) 0%, #5a91e6 100%); padding: 40px 20px; color: white; border-bottom-left-radius: 35px; border-bottom-right-radius: 35px; }
        .card-topup { background: white; border-radius: 20px; padding: 25px; margin-top: -30px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="header-blue text-center">
    <a href="anggota_dashboard.php" class="text-white float-start"><i class="fas fa-arrow-left fs-4"></i></a>
    <h4 class="fw-bold mb-0">Top Up Saldo</h4>
</div>

<div class="container px-4">
    <div class="card-topup">
        <!-- Pastikan action mengarah ke proses_topup.php -->
<form action="proses_topup.php" method="POST">
    <div class="mb-4 text-center">
        <i class="fas fa-wallet fa-3x text-primary mb-3"></i>
        <p class="text-muted small">Masukkan jumlah saldo yang ingin kamu isi ke akun BBM Pay</p>
    </div>
    
    <div class="mb-4">
        <label class="fw-bold mb-2">Nominal Top Up</label>
        <div class="input-group input-group-lg">
            <span class="input-group-text bg-light border-end-0">Rp</span>
            <!-- KUNCI: name="nominal" -->
            <input type="number" name="nominal" class="form-control bg-light border-start-0 fw-bold" placeholder="Contoh: 50000" required min="1000">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
        AJUKAN TOP UP
    </button>
</form>
    </div>
    
    <div class="mt-4 p-3 bg-white rounded-4 shadow-sm border-start border-4 border-warning">
        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Setelah mengajukan, silahkan tunjukkan nama akun Anda ke kasir untuk proses pembayaran tunai.</small>
    </div>
</div>

</body>
</html>