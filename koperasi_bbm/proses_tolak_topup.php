<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "UPDATE topup SET status = 'ditolak' WHERE id_topup = '$id'";
    $update = mysqli_query($koneksi, $query);

    if ($update) {
        echo "<script>
                alert('Permintaan Top Up telah ditolak.');
                window.location = 'konfirmasi_topup.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memproses penolakan.');
                window.location = 'konfirmasi_topup.php';
              </script>";
    }
} else {
  
    header("location:konfirmasi_topup.php");
}
?>