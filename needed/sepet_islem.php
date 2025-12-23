<?php
session_start();
include 'baglan.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 1. KULLANICI ID BULMA (Her işlemde lazım)
$username = $_SESSION['username'];
$kullanici_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$kullanici_sor->bind_param("s", $username);
$kullanici_sor->execute();
$sonuc = $kullanici_sor->get_result();
$kullanici = $sonuc->fetch_assoc();
$user_id = $kullanici['ID'];

if (!$user_id) { header("Location: index.php"); exit(); }


// --- İŞLEM 1: SEPETE EKLEME ---
if (isset($_POST['sepete_ekle'])) {
    $product_id = $_POST['product_id'];

    // Sepet kontrolü
    $sepet_sor = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $sepet_sor->bind_param("ii", $user_id, $product_id);
    $sepet_sor->execute();
    $sepet_sonuc = $sepet_sor->get_result();

    if ($sepet_sonuc->num_rows > 0) {
        $guncelle = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $guncelle->bind_param("ii", $user_id, $product_id);
        $guncelle->execute();
    } else {
        $ekle = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $ekle->bind_param("ii", $user_id, $product_id);
        $ekle->execute();
    }
    header("Location: index.php?durum=eklendi");
    exit();
}

// --- İŞLEM 2: SEPETTEN SİLME (YENİ EKLENDİ) ---
if (isset($_POST['sepetten_sil'])) {
    $sepet_id = $_POST['sepet_id'];

    // Sadece kendi sepetindeki ürünü silebilir (Güvenlik önlemi: user_id kontrolü)
    $sil = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $sil->bind_param("ii", $sepet_id, $user_id);
    $sil->execute();

    header("Location: sepet.php?durum=silindi");
    exit();
}
?>