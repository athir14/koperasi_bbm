<?php
include 'koneksi.php';

if (isset($_POST['daftar'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah digunakan, cari yang lain!');</script>";
    } else {
        
        $query = "INSERT INTO users (username, password, role, saldo) VALUES ('$username', '$password', 'anggota', 0)";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Koperasi BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #004a99 0%, #007bff 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif; padding: 20px 0;
        }
        .register-card {
            background: white; border-radius: 20px; width: 100%; max-width: 400px;
            padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .form-control { border-radius: 10px; padding: 12px; background: #f8f9fa; }
        .btn-register { border-radius: 10px; padding: 12px; font-weight: bold; }
    </style>
</head>
<body>

<div class="register-card mx-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-1">Gabung Anggota</h4>
        <p class="text-muted small">Dapatkan kemudahan transaksi digital</p>
    </div>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="small fw-bold text-muted">Username Baru</label>
            <input type="text" name="username" class="form-control" placeholder="Buat username" required>
        </div>
        <div class="mb-3">
            <label class="small fw-bold text-muted">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Buat password" required>
        </div>
        <div class="mb-4 small text-muted">
            <i class="fas fa-info-circle me-1"></i> Dengan mendaftar, Anda otomatis mendapatkan akses <b>BBM Pay</b>.
        </div>
        <button type="submit" name="daftar" class="btn btn-success w-100 btn-register mb-3 shadow">Daftar Sekarang</button>
    </form>

    <div class="text-center">
        <p class="small text-muted">Sudah punya akun? <a href="login.php" class="text-primary fw-bold text-decoration-none">Login Disini</a></p>
    </div>
</div>

</body>
</html>