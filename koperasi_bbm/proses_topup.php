<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "anggota") {
    header("location:login.php"); 
    exit();
}

$id_user = $_SESSION['id_user'];

if (isset($_POST['nominal'])) {
    
    $nominal = intval($_POST['nominal']); 
    
    $metode = "Tunai"; 

    if ($nominal < 1000) {
        echo "<script>
                alert('Minimal Top Up adalah Rp 1.000');
                window.history.back();
              </script>";
        exit();
    }

    $query = "INSERT INTO topup (id_user, jumlah, metode, status) 
              VALUES ('$id_user', '$nominal', '$metode', 'pending')";
    
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        echo "<script>
                alert('Permintaan Top Up Rp " . number_format($nominal, 0, ',', '.') . " Berhasil! Silakan ke Kasir untuk bayar.');
                window.location='anggota_dashboard.php';
              </script>";
    } else {
      
        echo "<script>
                alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');
                window.history.back();
              </script>";
    }

} else {
   
    header("location:topup.php");
    exit();
}
?>