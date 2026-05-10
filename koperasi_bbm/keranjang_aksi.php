<?php
session_start();
include 'koneksi.php';

$action = $_GET['action'];
$id = $_GET['id'];

if($action == "add") {
    
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
} elseif($action == "remove") {
    unset($_SESSION['cart'][$id]);
}

header("location:anggota_dashboard.php");