<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:login.php"); exit();
}

$notif = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));

if (isset($_POST['tambah_anggota'])) {
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass = $_POST['password'];
    $saldo = $_POST['saldo'];

    $query = mysqli_query($koneksi, "INSERT INTO users (username, password, role, saldo) VALUES ('$user', '$pass', 'anggota', '$saldo')");
    if ($query) {
        echo "<script>alert('Anggota Berhasil Ditambahkan!'); window.location='data_anggota.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Admin BBM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e3e6f0; position: fixed; width: 240px; z-index: 1000; }
        .main-content { margin-left: 240px; padding: 30px; }
        .nav-link { color: #4e73df; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background-color: #4e73df; color: white !important; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar p-3 shadow-sm">
    <div class="text-center mb-4">
        <h4 class="text-primary fw-bold"><i class="fas fa-university"></i> BBM</h4>
        <small class="text-muted text-uppercase fw-bold">Pusat Kendali Admin</small>
    </div>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="admin_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="data_anggota.php"><i class="fas fa-users me-2"></i> Data Anggota</a></li>
        <li class="nav-item">
            <a class="nav-link" href="konfirmasi_topup.php">
                <i class="fas fa-check-circle me-2"></i> Monitoring Saldo
                <?php if($notif > 0): ?>
                    <span class="badge bg-danger ms-1 shadow-sm"><?= $notif ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link" href="riwayat_transaksi.php"><i class="fas fa-file-invoice-dollar me-2"></i> Laporan</a></li>
        <hr>
        <li class="nav-item"><a class="nav-link text-danger fw-bold" href="logout.php" onclick="return confirm('Keluar?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 fw-bold text-gray-800 mb-0">Manajemen Anggota</h2>
            <p class="text-muted small">Total: <?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM users WHERE role='anggota'")) ?> Anggota terdaftar</p>
        </div>
        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-user-plus me-1"></i> Anggota Baru
        </button>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Saldo BBM Pay</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($koneksi, "SELECT * FROM users WHERE role='anggota' ORDER BY id_user DESC");
                    while($u = mysqli_fetch_assoc($res)) {
                    ?>
                    <tr>
                        <td><span class="text-muted small">#<?= $u['id_user'] ?></span></td>
                        <td class="fw-bold"><?= $u['username'] ?></td>
                        <td><span class="badge bg-light text-primary border border-primary px-3">Rp <?= number_format($u['saldo']) ?></span></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="edit_anggota.php?id=<?= $u['id_user'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="hapus_anggota.php?id=<?= $u['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus anggota dan semua riwayat transaksinya?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-primary">Daftarkan Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="small fw-bold mb-1">Username</label>
                    <input type="text" name="username" class="form-control bg-light border-0" placeholder="Username anggota" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold mb-1">Password</label>
                    <input type="password" name="password" class="form-control bg-light border-0" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="mb-0">
                    <label class="small fw-bold mb-1">Saldo Awal (Rp)</label>
                    <input type="number" name="saldo" class="form-control bg-light border-0" value="0">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="tambah_anggota" class="btn btn-primary px-4 fw-bold shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>