<?php
// Hitung notifikasi pending
$notif_sidebar = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar p-3 shadow-sm">
    <div class="text-center mb-4">
        <h4 class="text-primary fw-bold"><i class="fas fa-university"></i> BBM</h4>
        <small class="text-muted text-uppercase fw-bold">Pusat Kendali Admin</small>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'admin_dashboard.php') ? 'active' : '' ?>" href="admin_dashboard.php">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'data_anggota.php') ? 'active' : '' ?>" href="data_anggota.php">
                <i class="fas fa-users me-2"></i> Data Anggota
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'konfirmasi_topup.php') ? 'active' : '' ?>" href="konfirmasi_topup.php">
                <i class="fas fa-check-circle me-2"></i> Konfirmasi Saldo
                <?php if($notif_sidebar > 0): ?>
                    <span class="badge bg-danger ms-1 shadow-sm"><?= $notif_sidebar ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'riwayat_transaksi.php') ? 'active' : '' ?>" href="riwayat_transaksi.php">
                <i class="fas fa-file-invoice-dollar me-2"></i> Laporan
            </a>
        </li>
        <hr>
        <li class="nav-item">
            <a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </li>
    </ul>
</div>