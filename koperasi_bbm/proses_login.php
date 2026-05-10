<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($query);

if ($cek > 0) {
    $data = mysqli_fetch_assoc($query);
    
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['id_user'] = $data['id_user'];

    if ($data['role'] == "admin") {
        header("location:admin_dashboard.php");
    } else if ($data['role'] == "kasir") {
        header("location:kasir_dashboard.php");
    } else if ($data['role'] == "anggota") {
        header("location:anggota_dashboard.php");
    }
} else {
    echo "<script>alert('Login Gagal! Username atau Password salah.'); window.location='login.php';</script>";
}
?>