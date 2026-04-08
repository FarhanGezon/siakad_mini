<?php
session_start();

// login session
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

require_once 'Mahasiswa.php';
require_once 'Dosen.php';

$statusProses = false;

// Data Dosen Koordinator & Matkul
$dosen = new Dosen("198706112015042002", "Eka Yuniar, S.Kom., MMSI");
$matkulJadwal = [
    "Pemrograman Berorientasi Objek", "Workshop Visualisasi Keputusan Bisnis", 
    "Manajemen Pemasaran Digital", "Interpersonal Skill", 
    "Workshop Desain Grafis Dan Multimedia", "Bahasa Indonesia", 
    "Intermediate English", "Kewarganegaraan"
];
foreach($matkulJadwal as $m) { $dosen->tambahMataKuliah($m); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mhs = new Mahasiswa($_POST['nim'], $_POST['nama'], $_POST['jurusan']);
    foreach($_POST['mk'] as $i => $mk) {
        if(!empty($_POST['nilai'][$i])) {
            $mhs->inputNilai($mk, $_POST['sks'][$i], $_POST['nilai'][$i]);
        }
    }
    $statusProses = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD MINI · POLIJE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="app-header animate-slideDown" style="position: relative;">
        <a href="logout.php" class="btn-logout">🚪 Logout</a>
        
        <h1>🎓 SIAKAD MINI</h1>
        <p><?= $statusProses ? '<span class="badge-success animate-pop">✅ Data Berhasil Diproses</span>' : 'Sistem Informasi Akademik · Polije Bondowoso' ?></p>
    </header>

    <div class="dashboard-grid">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h2>📝 Input Mahasiswa & Nilai</h2></div>
            <form method="post">
                <div class="form-row">
                    <div class="form-group"><label>NIM</label><input type="text" name="nim" placeholder="NIM Anda" required></div>
                    <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" placeholder="Nama Mahasiswa" required></div>
                </div>
                
                <div class="form-group">
                    <label>Program Studi</label>
                    <select name="jurusan" required>
                        <option value="">-- Pilih Prodi --</option>
                            <option value="Sarjana Terapan (D4) Bisnis Digital">Sarjana Terapan (D4) Bisnis Digital</option>
                            <option value="Sarjana Terapan (D4) Produksi Media">Sarjana Terapan (D4) Produksi Media</option>
                            <option value="Diploma (D3/D4) Manajemen Agribisnis">Diploma (D3/D4) Manajemen Agribisnis</option>
                    </select>
                </div>
                <h3>📚 Input Nilai Mata Kuliah</h3>
                <div class="scroll-area">
                <?php foreach($dosen->getMataKuliah() as $i => $mk): ?>
                    <div class="form-row mk-row">
                        <div class="form-group">
                            <label>Matkul <?= $i+1 ?></label>
                            <input type="text" name="mk[]" value="<?= $mk ?>" readonly>
                        </div>
                        <div class="form-row-nested">
                            <div class="form-group"><label>SKS</label><input type="number" name="sks[]" value="3" min="1"></div>
                            <div class="form-group">
                                <label>Nilai</label>
                                <select name="nilai[]">
                                    <option value="">--</option>
                                    <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option><option value="E">E</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Proses & Simpan KHS</button>
                    <button type="reset" class="btn btn-outline">🔄 Reset</button>
                </div>
            </form>
            <div class="info-panel">
                <p><strong>👨‍🏫 Dosen Pengampu:</strong> <?= $dosen->getNama() ?> (NIDN: <?= $dosen->getNidn() ?>)</p>
            </div>
        </div>

        <div class="card animate-fadeInUp" style="animation-delay: 0.2s">
            <div class="card-header"><h2>📊 Kartu Hasil Studi (KHS)</h2></div>
            <?php if ($statusProses && isset($mhs)): ?>
                <div class="khs-info animate-pop">
                    <p><strong>NIM:</strong> <?= $mhs->getNim() ?> | <strong>Nama:</strong> <?= $mhs->getNama() ?></p>
                    <p><strong>Prodi:</strong> <?= $mhs->getJurusan() ?></p>
                </div>
                <div class="table-container">
                    <table>
                        <thead><tr><th>No</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th><th>Bobot</th></tr></thead>
                        <tbody>
                            <?php 
                            $tSks = 0; $tBobot = 0; $no = 1;
                            foreach($mhs->getNilai() as $n): 
                                $tSks += $n->getSks(); $tBobot += ($n->getSks() * $n->getBobot());
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $n->getNamaMk() ?></td>
                                    <td><?= $n->getSks() ?></td>
                                    <td><span class="badge-<?= strtolower($n->getNilaiHuruf()) ?>"><?= $n->getNilaiHuruf() ?></span></td>
                                    <td><?= number_format($n->getBobot(), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="summary-box animate-scaleUp">
                    <div class="summary-item"><span>TOTAL SKS</span><strong><?= $tSks ?></strong></div>
                    <div class="summary-item"><span>IPK SEMESTER</span><strong><?= ($tSks > 0) ? number_format($tBobot/$tSks, 2) : '0.00' ?></strong></div>
                </div>
            <?php else: ?>
                <div class="empty-state"><p>📭 Silakan isi data dan nilai untuk melihat KHS.</p></div>
            <?php endif; ?>
        </div>
    </div>
    <footer>UTS Pemrograman Web · Polije Bondowoso · <?= date('Y') ?></footer>
</body>
</html>
