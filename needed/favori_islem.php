<?php
session_start();
include 'baglan.php';

// Giriş kontrolü
if (!isset($_SESSION['username'])) {
    // Giriş yapmamışsa login'e atabilirsin veya uyarı verebilirsin
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $username = $_SESSION['username'];

    // 1. Kullanıcı ID bul
    $kullanici_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
    $kullanici_sor->bind_param("s", $username);
    $kullanici_sor->execute();
    $user = $kullanici_sor->get_result()->fetch_assoc();
    $user_id = $user['ID'];

    // 2. Bu ürün zaten favorilerde mi?
    $kontrol = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $kontrol->bind_param("ii", $user_id, $product_id);
    $kontrol->execute();
    $sonuc = $kontrol->get_result();

    if ($sonuc->num_rows > 0) {
        // VARSA -> SİL (Favoriden Çıkar)
        $sil = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        $sil->bind_param("ii", $user_id, $product_id);
        $sil->execute();
    } else {
        // YOKSA -> EKLE (Favorilere Ekle)
        $ekle = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $ekle->bind_param("ii", $user_id, $product_id);
        $ekle->execute();
    }

    // Geldiği sayfaya geri dön (index.php veya favorilerim.php)
    if(isset($_SERVER['HTTP_REFERER'])) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: index.php");
    }
    exit();
}
?>