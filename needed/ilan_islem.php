<?php
session_start();
include 'baglan.php';

// Giriş kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Giriş yapan kullanıcının ID'sini al
$username = $_SESSION['username'];
$u_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$u_sor->bind_param("s", $username);
$u_sor->execute();
$user_id = $u_sor->get_result()->fetch_assoc()['ID'];

// --- İŞLEM 1: STOK GÜNCELLEME ---
if (isset($_POST['stok_guncelle'])) {
    $product_id = $_POST['product_id'];
    $new_stock = intval($_POST['new_stock']);

    // Güvenlik: Bu ürün gerçekten bu kullanıcının mı?
    $kontrol = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND user_id = ?");
    $kontrol->bind_param("ii", $product_id, $user_id);
    $kontrol->execute();

    if ($kontrol->get_result()->num_rows > 0) {
        // Evet, ürün onun. Güncelle.
        $guncelle = $conn->prepare("UPDATE products SET stock_quantity = ? WHERE product_id = ?");
        $guncelle->bind_param("ii", $new_stock, $product_id);
        $guncelle->execute();
        header("Location: ilanlarim.php?durum=guncellendi");
    } else {
        header("Location: ilanlarim.php?durum=hata");
    }
}

// --- İŞLEM 2: ÜRÜN SİLME ---
if (isset($_POST['urun_sil'])) {
    $product_id = $_POST['product_id'];

    // Güvenlik: Bu ürün gerçekten bu kullanıcının mı?
    $kontrol = $conn->prepare("SELECT product_id, image_url FROM products WHERE product_id = ? AND user_id = ?");
    $kontrol->bind_param("ii", $product_id, $user_id);
    $kontrol->execute();
    $sonuc = $kontrol->get_result()->fetch_assoc();

    if ($sonuc) {
        // 1. Resmi klasörden sil (Sunucuda yer kaplamasın)
        if (!empty($sonuc['image_url']) && file_exists($sonuc['image_url'])) {
            unlink($sonuc['image_url']);
        }

        // 2. Veritabanından sil
        $sil = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $sil->bind_param("i", $product_id);
        $sil->execute();
        
        header("Location: ilanlarim.php?durum=silindi");
    } else {
        header("Location: ilanlarim.php?durum=hata");
    }
}
?>