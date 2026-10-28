<?php
// fungsi_nilai.php
function hitung_nilai($nilai) {
    if ($nilai <= 34.50) return ['E', 'Tidak Lulus'];
    if ($nilai <= 50.50) return ['D', 'Kurang Memuaskan'];
    if ($nilai <= 65.50) return ['C', 'Cukup Memuaskan'];
    if ($nilai <= 80.50) return ['B', 'Memuaskan'];
    return ['A', 'Sangat Memuaskan'];
}
?>