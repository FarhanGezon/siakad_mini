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

// Data Dosen Koordinator
$dosen = new Dosen("198706112015042002", "Eka Yuniar, S.Kom., MMSI");

// mata kuliah per program studi
$matkulPerProdi = [
    "Sarjana Terapan (D4) Bisnis Digital" => [
        ["nama" => "Pemrograman Berorientasi Objek", "sks" => 3],
        ["nama" => "Workshop Visualisasi Keputusan Bisnis", "sks" => 3],
        ["nama" => "Manajemen Pemasaran Digital", "sks" => 3],
        ["nama" => "Interpersonal Skill", "sks" => 2],
        ["nama" => "Bahasa Indonesia", "sks" => 2],
        ["nama" => "Intermediate English", "sks" => 2],
        ["nama" => "Kewarganegaraan", "sks" => 2]
    ],
    "Sarjana Terapan (D4) Produksi Media" => [
        ["nama" => "Workshop Desain Grafis Dan Multimedia", "sks" => 4],
        ["nama" => "Produksi Konten Digital", "sks" => 3],
        ["nama" => "Manajemen Proyek Media", "sks" => 3],
        ["nama" => "Interpersonal Skill", "sks" => 2],
        ["nama" => "Bahasa Indonesia", "sks" => 2],
        ["nama" => "Intermediate English", "sks" => 2],
        ["nama" => "Kewarganegaraan", "sks" => 2]
    ],
    "Diploma (D3/D4) Manajemen Agribisnis" => [
        ["nama" => "Manajemen Usaha Tani", "sks" => 3],
        ["nama" => "Pemasaran Hasil Pertanian", "sks" => 3],
        ["nama" => "Kewirausahaan Agribisnis", "sks" => 3],
        ["nama" => "Interpersonal Skill", "sks" => 2],
        ["nama" => "Bahasa Indonesia", "sks" => 2],
        ["nama" => "Intermediate English", "sks" => 2],
        ["nama" => "Kewarganegaraan", "sks" => 2]
    ]
];

$defaultProdi = array_key_first($matkulPerProdi);
$selectedProdi = $_POST['jurusan'] ?? $defaultProdi;
$currentMatkul = $matkulPerProdi[$selectedProdi] ?? $matkulPerProdi[$defaultProdi];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mhs = new Mahasiswa($_POST['nim'], $_POST['nama'], $_POST['jurusan']);
    foreach($_POST['mk'] as $i => $mk) {
        if(!empty($_POST['nilai'][$i])) {
            $mhs->inputNilai($mk, (int)$_POST['sks'][$i], $_POST['nilai'][$i]);
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
    <style>
        .mk-row {
            transition: opacity 0.2s;
        }
        .scroll-area {
            transition: min-height 0.2s;
        }
    </style>
</head>
<body>
    <header class="app-header animate-slideDown" style="position: relative;">
        <a href="logout.php" class="btn-logout no-print">🚪 Logout</a>
        <h1>🎓 SIAKAD MINI</h1>
        <p><?= $statusProses ? '<span class="badge-success animate-pop no-print">✅ Data Berhasil Diproses</span>' : '<span class="no-print">Sistem Informasi Akademik · Polije Bondowoso</span>' ?></p>
    </header>

    <div class="dashboard-grid">
        <!-- KOLOM KIRI: FORM INPUT -->
        <div class="card animate-fadeInUp no-print">
            <div class="card-header"><h2>📝 Input Mahasiswa & Nilai</h2></div>
            <form method="post" id="formKHS">
                <div class="form-row">
                    <div class="form-group"><label>NIM</label><input type="text" name="nim" placeholder="NIM Anda" required></div>
                    <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" placeholder="Nama Mahasiswa" required></div>
                </div>
                <div class="form-group">
                    <label>Program Studi</label>
                    <select name="jurusan" id="jurusan" required>
                        <option value="">-- Pilih Prodi --</option>
                        <?php foreach(array_keys($matkulPerProdi) as $prodi): ?>
                            <option value="<?= htmlspecialchars($prodi) ?>" <?= ($selectedProdi == $prodi) ? 'selected' : '' ?>><?= htmlspecialchars($prodi) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <h3>📚 Input Nilai Mata Kuliah</h3>
                <div class="scroll-area" id="scrollAreaMatkul">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Proses & Simpan KHS</button>
                    <button type="reset" class="btn btn-outline">🔄 Reset</button>
                </div>
            </form>
            <!-- INFO DOSEN -->
            <div class="info-panel no-print">
                <div class="dosen-info">
                    <strong>👨‍🏫 Dosen Pengampu:</strong>
                    <span class="dosen-nama"><?= $dosen->getNama() ?></span>
                    <span class="dosen-nidn">NIDN: <?= $dosen->getNidn() ?></span>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: KHS -->
        <div class="card animate-fadeInUp" style="animation-delay: 0.2s">
            <div class="card-header no-print"><h2>📊 Kartu Hasil Studi (KHS)</h2></div>
            <?php if ($statusProses && isset($mhs)): ?>
                <!-- KOP SURAT (hanya muncul saat print) -->
                <div class="print-only kop-surat">
                    <h2>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h2>
                    <h3>POLITEKNIK NEGERI JEMBER</h3>
                    <h4>KAMPUS BONDOWOSO</h4>
                    <p>Jl. Imam Bonjol No. 1, Bondowoso, Jawa Timur</p>
                    <hr>
                </div>

                <div class="khs-info animate-pop">
                    <p><strong>NIM:</strong> <?= $mhs->getNim() ?> | <strong>Nama:</strong> <?= $mhs->getNama() ?></p>
                    <p><strong>Prodi:</strong> <?= $mhs->getJurusan() ?></p>
                </div>
                <div class="table-container">
                    <table class="khs-table">
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

                <!-- TANDA TANGAN -->
                <div class="print-only tanda-tangan">
                    <p>Bondowoso, <?= date('d F Y') ?></p>
                    <p>Dosen Pengampu,</p>
                    <br><br><br>
                    <p><strong><?= $dosen->getNama() ?></strong></p>
                    <p>NIDN. <?= $dosen->getNidn() ?></p>
                </div>

                <!-- Tombol Cetak -->
                <div class="form-actions no-print" style="margin-top: 20px;">
                    <button type="button" class="btn btn-outline" onclick="window.print()">🖨️ Cetak Dokumen KHS</button>
                </div>
            <?php else: ?>
                <div class="empty-state no-print"><p>📭 Silakan isi data dan nilai untuk melihat KHS.</p></div>
            <?php endif; ?>
        </div>
    </div>
    <footer class="no-print">Pemrograman Web · Polije Bondowoso · <?= date('Y') ?></footer>

    <script>
        const matkulData = <?= json_encode($matkulPerProdi) ?>;
        
        function renderMataKuliah(prodi) {
            const container = document.getElementById('scrollAreaMatkul');
            const matkulList = matkulData[prodi] || matkulData[Object.keys(matkulData)[0]];
            
            let html = '';
            matkulList.forEach((mk, index) => {
                html += `
                    <div class="form-row mk-row">
                        <div class="form-group">
                            <label>Matkul ${index+1}</label>
                            <input type="text" name="mk[]" value="${mk.nama}" readonly>
                        </div>
                        <div class="form-row-nested">
                            <div class="form-group">
                                <label>SKS</label>
                                <input type="number" name="sks[]" value="${mk.sks}" min="1" max="6" required>
                            </div>
                            <div class="form-group">
                                <label>Nilai</label>
                                <select name="nilai[]">
                                    <option value="">--</option>
                                    <option value="A">A</option><option value="B">B</option><option value="C">C</option>
                                    <option value="D">D</option><option value="E">E</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        const selectProdi = document.getElementById('jurusan');
        selectProdi.addEventListener('change', function() {
            if (this.value) {
                renderMataKuliah(this.value);
            } else {
                document.getElementById('scrollAreaMatkul').innerHTML = '<p style="padding: 1rem; text-align: center;">Silakan pilih Program Studi terlebih dahulu.</p>';
            }
        });

        if (selectProdi.value) {
            renderMataKuliah(selectProdi.value);
        } else {
            document.getElementById('scrollAreaMatkul').innerHTML = '<p style="padding: 1rem; text-align: center;">Silakan pilih Program Studi terlebih dahulu.</p>';
        }
    </script>
</body>
</html>
