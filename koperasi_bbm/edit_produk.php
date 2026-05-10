<?php
session_start();
include "koneksi.php";

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id'"));

if (isset($_POST["update"])) {
    $k = $_POST["kode"];
    $n = $_POST["nama"];
    $h = $_POST["harga"];
    $s = $_POST["stok"];
    
    $foto_nama = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];

    if (!empty($foto_nama)) {
        $ekstensi = pathinfo($foto_nama, PATHINFO_EXTENSION);
        $nama_baru = time() . "." . $ekstensi;
        move_uploaded_file($tmp, "img/" . $nama_baru);
        
        mysqli_query($koneksi, "UPDATE produk SET kode_barang='$k', nama_produk='$n', harga='$h', stok='$s', foto='$nama_baru' WHERE id_produk='$id'");
    } else {
        
        mysqli_query($koneksi, "UPDATE produk SET kode_barang='$k', nama_produk='$n', harga='$h', stok='$s' WHERE id_produk='$id'");
    }
    
    header("location:admin_dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk - Koperasi BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-warning { background: #ffc107; border: none; border-radius: 10px; padding: 12px; color: #000; }
        .btn-light { border-radius: 10px; padding: 12px; }
        .current-img { border-radius: 10px; border: 2px dashed #ddd; padding: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="card mx-auto p-4" style="max-width: 550px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary">Edit Data Produk</h4>
            <p class="text-muted small">ID Produk: #<?= $data['id_produk'] ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="small fw-bold">KODE BARANG</label>
                <input type="text" name="kode" class="form-control" value="<?= $data['kode_barang'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="small fw-bold">NAMA PRODUK</label>
                <input type="text" name="nama" class="form-control" value="<?= $data['nama_produk'] ?>" required>
            </div>

            <div class="row mb-3">
                <div class="col-7">
                    <label class="small fw-bold">HARGA (RP)</label>
                    <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
                </div>
                <div class="col-5">
                    <label class="small fw-bold">STOK</label>
                    <input type="number" name="stok" class="form-control" value="<?= $data['stok'] ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="small fw-bold">FOTO SAAT INI</label><br>
                <img src="img/<?= $data['foto'] ?>" class="current-img" width="100"><br>
                <label class="small fw-bold mt-2">GANTI FOTO (Kosongkan jika tidak diganti)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="update" class="btn btn-warning fw-bold">
                    <i class="fas fa-sync-alt me-1"></i> UPDATE PRODUK
                </button>
                <a href="admin_dashboard.php" class="btn btn-light fw-bold text-muted">
                    <i class="fas fa-times me-1"></i> BATAL / KEMBALI
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>