<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];
$u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id'"));

if (isset($_POST['update'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $saldo = $_POST['saldo'];

    $query = mysqli_query($koneksi, "UPDATE users SET username='$user', password='$pass', saldo='$saldo' WHERE id_user='$id'");
    if ($query) {
        echo "<script>alert('Data Anggota Berhasil Diupdate!'); window.location='data_anggota.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota - Admin BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 w-100" style="max-width: 450px;">
        <h4 class="fw-bold text-primary mb-4">Edit Data Anggota</h4>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" value="<?= $u['username'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="small fw-bold">Password</label>
                <input type="text" name="password" class="form-control" value="<?= $u['password'] ?>" required>
            </div>
            <div class="mb-4">
                <label class="small fw-bold">Saldo (Rp)</label>
                <input type="number" name="saldo" class="form-control" value="<?= $u['saldo'] ?>" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" name="update" class="btn btn-warning fw-bold">Update Data</button>
                <a href="data_anggota.php" class="btn btn-light fw-bold text-muted">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>