<?php
$host = "localhost";
$vt_adi = "needed_database";
$kullanici = "root";
$sifre = "";

$conn = new mysqli($host, $kullanici, $sifre, $vt_adi);

if ($conn->connect_error) {
    die("Bağlantı Hatası: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>