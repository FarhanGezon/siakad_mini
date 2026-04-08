<?php
require_once 'User.php';
require_once 'CetakLaporan.php';

class Dosen extends User implements CetakLaporan {
    private $mataKuliahDiampu = [];

    public function __construct($nidn, $nama) {
        parent::__construct($nidn, $nama);
    }

    public function getRole() { 
        return "Dosen"; 
    }

    public function tambahMataKuliah($mataKuliah) {
        $this->mataKuliahDiampu[] = $mataKuliah;
    }

    public function getMataKuliah() {
        return $this->mataKuliahDiampu;
    }

    public function getNidn() {
        return $this->id;
    }

    // Polymorphism & Interface 
    public function cetak() {
        $html = "<div class='laporan-card dosen-card'>";
        $html .= "<h3>Laporan Mengajar Dosen</h3>";
        $html .= "<p><strong>Nama:</strong> {$this->nama}</p>";
        $html .= "<p><strong>NIDN:</strong> {$this->id}</p>";
        
        $html .= "<h4>Mata Kuliah Diampu:</h4><ul>";
        foreach ($this->mataKuliahDiampu as $mk) {
            $html .= "<li>{$mk}</li>";
        }
        $html .= "</ul></div>";

        return $html;
    }
}
?>
