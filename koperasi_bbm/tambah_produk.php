<?php
session_start();
include "koneksi.php";

if (isset($_POST["simpan"])) {
    $k = $_POST["kode"]; 
    $n = $_POST["nama"]; 
    $h = $_POST["harga"]; 
    $s = $_POST["stok"];
    $foto_nama = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];
    
    if (!is_dir("img")) { mkdir("img"); }

    $ekstensi = pathinfo($foto_nama, PATHINFO_EXTENSION);
    $nama_baru = time() . "." . $ekstensi; 
    $path = "img/" . $nama_baru;

    if (move_uploaded_file($tmp, $path)) {
        mysqli_query($koneksi, "INSERT INTO produk (kode_barang, nama_produk, harga, stok, foto) 
                               VALUES ('$k', '$n', '$h', '$s', '$nama_baru')");
        header("location:admin_dashboard.php");
    } else {
        echo "<script>alert('Gagal upload gambar!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk - Koperasi BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background: #007bff; border: none; border-radius: 10px; padding: 12px; }
        .btn-light { border-radius: 10px; padding: 12px; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card mx-auto p-4" style="max-width: 550px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">Tambah Produk Baru</h4>
            <p class="text-muted small">Pastikan semua data terisi dengan benar</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="small fw-bold">KODE BARANG / SCAN BARCODE</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-barcode"></i></span>
                    <input type="text" name="kode" class="form-control" placeholder="Scan barcode..." required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="small fw-bold">NAMA PRODUK</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Buku Tulis" required>
            </div>

            <div class="row mb-3">
                <div class="col-7">
                    <label class="small fw-bold">HARGA (RP)</label>
                    <input type="number" name="harga" class="form-control" placeholder="0" required>
                </div>
                <div class="col-5">
                    <label class="small fw-bold">STOK AWAL</label>
                    <input type="number" name="stok" class="form-control" placeholder="0" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="small fw-bold">FOTO PRODUK</label>
                <input type="file" name="foto" class="form-control" accept="image/*" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="simpan" class="btn btn-primary fw-bold">
                    <i class="fas fa-save me-1"></i> SIMPAN PRODUK
                </button>
                <a href="admin_dashboard.php" class="btn btn-light fw-bold text-muted">
                    <i class="fas fa-arrow-left me-1"></i> KEMBALI
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>