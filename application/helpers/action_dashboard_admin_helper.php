<?php 
function getjumlahKomplainDivisiByMonth($bulanDalamAngka, $tahunDalamAngka){ 
    $ci = &get_instance();
    $res = $ci->KomplainAModel->jumlahKomplainDivisiByMonth($bulanDalamAngka, $tahunDalamAngka);
    return $res;
} 
function getjumlahKomplainMasukByYear($tahunDalamAngka){
    $ci = &get_instance();
    $res = $ci->KomplainAModel->fetchKomplainPerBulanByYear($tahunDalamAngka);
    return $res;
}