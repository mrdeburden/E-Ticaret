<?php
include 'baglan.php';

if(isset($_POST['kayit_buton'])){
    $user = $_POST['username'];
    $mail = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $zaman = time(); 

    $sorgu = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, ?)");
    $sorgu->bind_param("sssi", $user, $mail, $pass, $zaman);

    if($sorgu->execute()){
        header("Location: login.php?durum=ok");
    } else {
        header("Location: kayit_sayfasi.php?durum=no");
    }
    $sorgu->close();
}
?>