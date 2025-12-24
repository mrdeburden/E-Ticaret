<?php
session_start();
include 'baglan.php';

// Oturum kontrolü: Giriş yapmamışsa login sayfasına at
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['urun_kaydet'])) {
    
    // 1. Giriş yapan kullanıcının ID'sini buluyoruz (Satıcı kim?)
    $username = $_SESSION['username'];
    $user_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
    $user_sor->bind_param("s", $username);
    $user_sor->execute();
    $user_sonuc = $user_sor->get_result()->fetch_assoc();
    $user_id = $user_sonuc['ID'];

    // 2. Formdan gelen verileri alıyoruz
    $name = htmlspecialchars($_POST['name']);
    $category = $_POST['category']; // <--- KATEGORİ BURADA ALINIYOR
    $price = $_POST['price'];
    $description = htmlspecialchars($_POST['description']);
    $stock = $_POST['stock_quantity'];

    // 3. Resim Yükleme İşlemi
    $image_url = "";
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $klasor = 'uploads/';
        // Klasör yoksa oluştur
        if (!file_exists($klasor)) { mkdir($klasor, 0777, true); }
        
        $uzanti = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        $yeni_ad = uniqid() . "." . $uzanti; // Benzersiz isim ver
        $hedef = $klasor . $yeni_ad;
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $hedef)) {
            $image_url = $hedef;
        }
    }

    // 4. Veritabanına Kayıt (Kategori ve Kullanıcı ID dahil)
    // Sütun sırası: user_id, category, name, description, price, stock_quantity, image_url
    $sorgu = $conn->prepare("INSERT INTO products (user_id, category, name, description, price, stock_quantity, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // i=integer, s=string, d=double(ondalıklı sayı)
    $sorgu->bind_param("isssdis", $user_id, $category, $name, $description, $price, $stock, $image_url);

    if ($sorgu->execute()) {
        // Başarılıysa anasayfaya dön
        header("Location: index.php?durum=basarili");
    } else {
        echo "Hata oluştu: " . $conn->error;
    }
}
?>