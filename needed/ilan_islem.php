<?php
session_start();
include 'baglan.php';

// Giriş kontrolü
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

$username = $_SESSION['username'];
$u_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$u_sor->bind_param("s", $username);
$u_sor->execute();
$user_id = $u_sor->get_result()->fetch_assoc()['ID'];

// --- İŞLEM 1: ÜRÜN SİLME ---
if (isset($_POST['urun_sil'])) {
    $product_id = $_POST['product_id'];
    $kontrol = $conn->prepare("SELECT product_id, image_url FROM products WHERE product_id = ? AND user_id = ?");
    $kontrol->bind_param("ii", $product_id, $user_id);
    $kontrol->execute();
    $sonuc = $kontrol->get_result()->fetch_assoc();

    if ($sonuc) {
        if (!empty($sonuc['image_url']) && file_exists($sonuc['image_url'])) { unlink($sonuc['image_url']); }
        $sil = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $sil->bind_param("i", $product_id);
        $sil->execute();
        header("Location: ilanlarim.php?durum=silindi");
    } else {
        header("Location: ilanlarim.php?durum=hata");
    }
}

// --- İŞLEM 2: ÜRÜN GÜNCELLEME (YENİ EKLENDİ) ---
if (isset($_POST['urun_guncelle'])) {
    $product_id = $_POST['product_id'];
    
    // Form verilerini al
    $name = htmlspecialchars($_POST['name']);
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = htmlspecialchars($_POST['description']);
    $stock = $_POST['stock_quantity'];
    
    // Varsayılan olarak eski resmi kullan
    $image_url = $_POST['eski_resim'];

    // 1. Yeni resim yüklenmiş mi?
    if (isset($_FILES['image_file']) && $_FILES['image_file']['size'] > 0) {
        $klasor = 'uploads/';
        if (!file_exists($klasor)) { mkdir($klasor, 0777, true); }
        
        $uzanti = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $yeni_ad = uniqid() . "." . $uzanti;
        $hedef = $klasor . $yeni_ad;
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $hedef)) {
            // Yeni resim başarılı yüklendiyse, eskisini sil (sunucuda yer aç)
            if (!empty($image_url) && file_exists($image_url)) { unlink($image_url); }
            $image_url = $hedef; // Veritabanına yeni yolu kaydet
        }
    }

    // 2. Güvenlik Kontrolü: Ürün bu adama mı ait?
    $kontrol = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND user_id = ?");
    $kontrol->bind_param("ii", $product_id, $user_id);
    $kontrol->execute();

    if ($kontrol->get_result()->num_rows > 0) {
        // 3. Veritabanını Güncelle
        $guncelle = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, stock_quantity=?, image_url=? WHERE product_id=?");
        $guncelle->bind_param("ssdsisi", $name, $category, $price, $description, $stock, $image_url, $product_id);
        $guncelle->execute();
        
        header("Location: ilanlarim.php?durum=guncellendi");
    } else {
        echo "Hata: Bu ürünü düzenleme yetkiniz yok.";
    }
}
?>