<?php
include 'baglan.php';
// kayıt yaparkenki kontrol ve güvenlik aşamaları
require_once 'baglan.php';

if(isset($_POST['kayit_buton'])){
    $user = $_POST['username'];
    $mail = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Şifreyi gizle

    $sorgu = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $sorgu->bind_param("sss", $user, $mail, $pass);

    if($sorgu->execute()){
        echo "Kayıt Başarılı! Ana sayfaya yönlendiriliyorsunuz.";
        header("Refresh: 2; url=index.php"); // Paylaştığın ana sayfaya döner
    } else {
        echo "Hata: Bu kullanıcı adı veya e-posta zaten kullanımda olabilir.";
    }
}
?>