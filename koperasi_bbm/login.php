<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($login);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($login);
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == "admin") {
            header("location:admin_dashboard.php");
        } else if ($data['role'] == "kasir") {
            header("location:kasir_dashboard.php");
        } else {
            header("location:anggota_dashboard.php");
        }
    } else {
        echo "<script>alert('Username atau Password Salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #004a99 0%, #007bff 100%);
            height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: white; border-radius: 20px; width: 100%; max-width: 400px;
            padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .form-control { border-radius: 10px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; }
        .btn-login { border-radius: 10px; padding: 12px; font-weight: bold; background: #007bff; border: none; }
        .btn-login:hover { background: #0056b3; }
        .logo-box { width: 70px; height: 70px; background: #e7f1ff; color: #007bff; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 30px; }
    </style>
</head>
<body>

<div class="login-card mx-3">
    <div class="text-center mb-4">
        <div class="logo-box">
            <i class="fas fa-university"></i>
        </div>
        <h4 class="fw-bold mb-1">Koperasi BBM</h4>
        <p class="text-muted small">Silakan masuk ke akun Anda</p>
    </div>

    <form action="" method="POST">
        <div class="mb-3">
            <label class="small fw-bold text-muted">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
        </div>
        <div class="mb-4">
            <label class="small fw-bold text-muted">Password</label>
            <input type="password" name="password" class="form-control" placeholder="********" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100 btn-login mb-3 shadow">Masuk Sekarang</button>
    </form>

    <div class="text-center">
        <p class="small text-muted">Belum punya akun anggota? <br> 
        <a href="daftar.php" class="text-primary fw-bold text-decoration-none">Daftar Jadi Anggota</a></p>
    </div>
</div>

</body>
</html>