<?php
$host = "localhost";      
$user = "root";           
$pass = "";               
$db   = "needed_database";      

$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>