<?php
require_once 'User.php';
require_once 'CetakLaporan.php';

class DetailNilai {
    private $mataKuliah;
    private $sks;
    private $huruf;
    private $bobot;

    public function __construct($mataKuliah, $sks, $huruf, $bobot) {
        $this->mataKuliah = $mataKuliah;
        $this->sks = $sks;
        $this->huruf = strtoupper($huruf);
        $this->bobot = $bobot;
    }

    public function getMataKuliah() { return $this->mataKuliah; }
    public function getMatkul() { return $this->mataKuliah; }
    public function getNamaMk() { return $this->mataKuliah; } 
    
    public function getSks() { return $this->sks; }
    public function getHuruf() { return $this->huruf; }
    public function getNilaiHuruf() { return $this->huruf; }
    public function getBobot() { return $this->bobot; }
}

class Mahasiswa extends User implements CetakLaporan {
    private $jurusan;
    private $transkrip = []; 

    public function __construct($id, $nama, $jurusan) {
        parent::__construct($id, $nama);
        $this->jurusan = $jurusan;
    }

    public function getRole() { return "Mahasiswa"; }
    public function getNim() { return $this->id; }
    public function getJurusan() { return $this->jurusan; }
    public function getNilai() { return $this->transkrip; }

    public function inputNilai($mataKuliah, $sks, $nilaiHuruf) {
        $bobot = $this->konversiHurufKeAngka($nilaiHuruf);
        $this->transkrip[] = new DetailNilai($mataKuliah, $sks, $nilaiHuruf, $bobot);
    }

    private function konversiHurufKeAngka($huruf) {
        $standarNilai = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0];
        return $standarNilai[strtoupper($huruf)] ?? 0.0;
    }

    public function hitungIPK() {
        $totalSKS = 0;
        $totalPoin = 0;

        foreach ($this->transkrip as $item) {
            $totalSKS += $item->getSks();
            $totalPoin += ($item->getSks() * $item->getBobot());
        }

        return ($totalSKS > 0) ? round($totalPoin / $totalSKS, 2) : 0;
    }

    public function cetak() {
        $data = "<div class='laporan-card'>";
        $data .= "<h3>Kartu Hasil Studi (KHS) - {$this->getRole()}</h3>";
        $data .= "<p>NIM: {$this->id} | Nama: {$this->nama} | Jurusan: {$this->jurusan}</p>";
        $data .= "<table><tr><th>Matkul</th><th>SKS</th><th>Nilai</th></tr>";
        
        $totalSKS = 0;
        foreach ($this->transkrip as $t) {
            $data .= "<tr><td>{$t->getMataKuliah()}</td><td>{$t->getSks()}</td><td>{$t->getHuruf()}</td></tr>";
            $totalSKS += $t->getSks();
        }
        
        $data .= "</table>";
        $data .= "<div class='summary'><span>Total SKS: {$totalSKS}</span><span>IPK: " . $this->hitungIPK() . "</span></div>";
        $data .= "</div>";
        return $data;
    }
}
?>
