<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "kasir") {
    header("location:login.php"); exit();
}

$nama_petugas = $_SESSION['username'] ?? 'Petugas';
$notif_topup = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM topup WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Terminal - BBM PAY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; border-right: 1px solid #e3e6f0; position: fixed; width: 260px; z-index: 1000; padding: 20px; }
        .sidebar-brand { color: #4e73df; font-weight: 800; font-size: 1.5rem; margin-bottom: 10px; display: block; text-decoration: none; }
        .sidebar-divider { margin: 15px 10px; border: 0; border-top: 1.5px solid #000000; opacity: 1; }
        .nav-link { color: #4e73df; font-weight: 600; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; transition: 0.2s; text-decoration: none; }
        .nav-link:hover { background: #f8f9fc; }
        .nav-link.active { background: #4e73df; color: white !important; box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2); }
        .nav-link i { width: 25px; font-size: 1.1rem; }
        .main-content { margin-left: 260px; padding: 40px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: white; overflow: hidden; }
        .scan-box { background: #4e73df; color: white; padding: 25px; }
        .total-display { font-size: 3.5rem; font-weight: 800; color: #1cc88a; line-height: 1; }
        #struk-cetak { display: none; width: 80mm; padding: 10px; background: white; color: black; font-family: 'Courier New', monospace; font-size: 12px; }
        .garis-putus { border-top: 1px dashed black; margin: 5px 0; }
        @media print {
            body * { visibility: hidden; }
            #struk-cetak, #struk-cetak * { visibility: visible; }
            #struk-cetak { display: block; position: absolute; left: 0; top: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="sidebar shadow-sm no-print">
    <a href="#" class="sidebar-brand text-center"><i class="fas fa-university"></i> BBM PAY</a>
    <hr class="sidebar-divider">
    <nav class="nav flex-column">
        <a class="nav-link active" href="kasir_dashboard.php"><i class="fas fa-cash-register"></i> Kasir Terminal</a>
        <a class="nav-link" href="kasir_konfirmasi.php"><i class="fas fa-hand-holding-usd"></i> Konfirmasi Saldo</a>
        <a class="nav-link" href="kasir_stok.php"><i class="fas fa-boxes"></i> Stok Barang</a>
    </nav>
    <hr class="sidebar-divider">
    <div class="mt-3 px-2">
        <small class="text-muted d-block px-2 mb-2">Petugas: <strong><?= $nama_petugas ?></strong></small>
        <a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom mb-4">
                <div class="scan-box no-print">
                    <label class="small text-uppercase fw-bold mb-2">Input Barang</label>
                    <div class="input-group">
                        <input type="text" id="inputBarang" class="form-control form-control-lg border-0 shadow-none" placeholder="Barcode...">
                        <button class="btn btn-dark btn-lg px-4 fw-bold" onclick="tambahManual()">TAMBAH</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle text-center mb-0">
                        <thead class="bg-light text-uppercase small">
                            <tr>
                                <th class="py-3">Nama Barang</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th class="no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="isiKeranjang"></tbody>
                    </table>
                </div>
            </div>

            <div class="row no-print">
                <div class="col-md-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-warning py-3 border-0 text-center">
                            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-money-bill-wave me-2"></i>ANTREAN TUNAI</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php
                            // Menggunakan LIKE 'Cash' untuk antisipasi spasi tak terlihat
                            $q_cash = mysqli_query($koneksi, "SELECT t.*, u.username, p.nama_produk FROM transaksi t JOIN users u ON t.id_user = u.id_user JOIN produk p ON t.id_produk = p.id_produk WHERE t.metode_pembayaran LIKE 'Cash%' AND t.status='pending' ORDER BY t.id_transaksi ASC");
                            if(mysqli_num_rows($q_cash) == 0) echo "<div class='p-4 text-center text-muted small'>Kosong</div>";
                            while($c = mysqli_fetch_assoc($q_cash)){ ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="text-dark"><?= strtoupper($c['username']) ?></strong>
                                    <div class="text-primary fw-bold">Rp <?= number_format($c['total_harga']) ?></div>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="terimaUang('<?= $c['nama_produk'] ?>', <?= $c['total_harga'] ?>, this)">Pindahkan</button>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card card-custom h-100">
                        <div class="card-header bg-info py-3 border-0 text-center">
                            <h6 class="m-0 fw-bold text-white"><i class="fas fa-wallet me-2"></i>ANTREAN BBM PAY</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php
                           $q_saldo = mysqli_query($koneksi, "SELECT t.*, u.username, u.saldo, p.nama_produk 
                                 FROM transaksi t 
                                 JOIN users u ON t.id_user = u.id_user 
                                 JOIN produk p ON t.id_produk = p.id_produk 
                                 WHERE t.metode_pembayaran LIKE 'Saldo' 
                                 AND t.status='pending' 
                                 ORDER BY t.id_transaksi ASC");
                            if(mysqli_num_rows($q_saldo) == 0) echo "<div class='p-4 text-center text-muted small'>Kosong</div>";
                            while($s = mysqli_fetch_assoc($q_saldo)){ ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <strong class="text-dark"><?= strtoupper($s['username']) ?></strong>
                                    <div class="text-primary fw-bold">Rp <?= number_format($s['total_harga']) ?></div>
                                    <small class="text-muted">Saldo: <?= number_format($s['saldo']) ?></small>
                                </div>
                                <div>
                                    <?php if($s['saldo'] >= $s['total_harga']): ?>
                                        <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold" onclick="konfirmasiAmbil('<?= $s['id_transaksi'] ?>', '<?= addslashes($s['nama_produk']) ?>')">Ambil</button>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Saldo Kurang</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom p-4 text-center no-print">
                <small class="text-uppercase fw-bold text-muted">Total Belanja</small>
                <div class="total-display my-3">Rp <span id="totalBesar">0</span></div>
                <div class="mb-3 text-start">
                    <label class="small fw-bold text-muted">UANG DITERIMA</label>
                    <input type="number" id="uangBayar" class="form-control form-control-lg text-center fw-bold bg-light" oninput="hitungKembalian()">
                </div>
                <div class="bg-light rounded-4 p-3 mb-4 border text-center">
                    <small class="text-uppercase fw-bold text-muted">Kembalian</small>
                    <h3 class="text-danger fw-bold mb-0" id="textKembalian">Rp 0</h3>
                </div>
                <button class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-sm" onclick="cetakStruk()">
                    <i class="fas fa-print me-2"></i> CETAK STRUK
                </button>
            </div>
        </div>
    </div>
</div>

<div id="struk-cetak">
    <div class="text-center">
        <h6 class="fw-bold m-0">KOPERASI BBM</h6>
        <small>SMKN 1 Cibinong</small>
        <div class="garis-putus"></div>
    </div>
    <div id="item-struk"></div>
    <div class="garis-putus"></div>
    <div class="d-flex justify-content-between"><span>TOTAL</span><span id="s-total"></span></div>
    <div class="d-flex justify-content-between"><span>BAYAR</span><span id="s-bayar"></span></div>
    <div class="d-flex justify-content-between fw-bold"><span>KEMBALI</span><span id="s-kembali"></span></div>
    <div class="garis-putus"></div>
    <div class="text-center mt-2">
        <small>Terima Kasih</small><br>
        <small><?= date('d/m/y H:i') ?> | <?= $nama_petugas ?></small>
    </div>
</div>

<script>
let totalBelanja = 0;
function terimaUang(nama, harga, btn) {
    const row = `<tr><td class="text-start ps-4 fw-bold text-dark">${nama.toUpperCase()}</td><td>Rp ${harga.toLocaleString()}</td><td>1</td><td class="fw-bold text-primary">Rp ${harga.toLocaleString()}</td><td class="no-print"><button class="btn btn-sm text-danger" onclick="hapusRow(this, ${harga})"><i class="fas fa-trash"></i></button></td></tr>`;
    document.getElementById('isiKeranjang').innerHTML += row;
    totalBelanja += harga; updateUI();
    if(btn) btn.closest('.list-group-item').remove();
}
function konfirmasiAmbil(id, nama) {
    if(confirm('Barang ' + nama + ' sudah diambil?')) window.location.href = 'proses_ambil_barang.php?id=' + id;
}
function tambahManual() {
    let nama = document.getElementById('inputBarang').value;
    if(!nama) return alert("Isi nama barang!");
    terimaUang(nama, 0, null); // Harga 0 karena input manual bebas
    document.getElementById('inputBarang').value = "";
}
function hapusRow(btn, harga) { btn.closest('tr').remove(); totalBelanja -= harga; updateUI(); }
function updateUI() { document.getElementById('totalBesar').innerText = totalBelanja.toLocaleString(); hitungKembalian(); }
function hitungKembalian() {
    let bayar = document.getElementById('uangBayar').value;
    let kembali = bayar - totalBelanja;
    document.getElementById('textKembalian').innerText = "Rp " + (kembali >= 0 ? kembali.toLocaleString() : 0);
}
function cetakStruk() {
    let bayar = document.getElementById('uangBayar').value;
    if(totalBelanja <= 0 || bayar < totalBelanja) return alert("Pembayaran tidak valid!");
    document.getElementById('s-total').innerText = "Rp " + totalBelanja.toLocaleString();
    document.getElementById('s-bayar').innerText = "Rp " + parseInt(bayar).toLocaleString();
    document.getElementById('s-kembali').innerText = "Rp " + (bayar - totalBelanja).toLocaleString();
    let items = "";
    document.querySelectorAll('#isiKeranjang tr').forEach(r => { items += `<div class="d-flex justify-content-between"><span>${r.cells[0].innerText}</span><span>${r.cells[3].innerText}</span></div>`; });
    document.getElementById('item-struk').innerHTML = items;
    window.print(); 
    setTimeout(() => { location.reload(); }, 1000);
}
</script>
</body>
</html>