<?php
session_start();
include 'baglan.php';

// Giriş yapmamışsa bu sayfayı görmesin
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Ekle - Needed</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F5F5F5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .add-product-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { color: #6A0DAD; text-align: center; }
        input, textarea, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #2ecc71; color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: bold; }
        button:hover { background-color: #27ae60; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #6A0DAD; text-decoration: none; }
    </style>
</head>
<body>

<div class="add-product-card">
    <h2>Yeni Ürün Ekle</h2>
    <form action="urun_islem.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="urun_ad" placeholder="Ürün Adı" required>
        
        <label>Ürün Kategorisi:</label>
        <select name="urun_kategori" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">
            <option value="">Kategori Seçiniz...</option>
            <option value="giyim">GENEL</option>
            <option value="aksesuar">GİYİM</option>
            <option value="teknoloji">TEKNOLOJİ</option>
        </select>

        <input type="number" name="urun_fiyat" placeholder="Fiyat (₺)" step="0.01" required>
        <textarea name="urun_aciklama" placeholder="Ürün Açıklaması" rows="4"></textarea>
        
        <label>Ürün Görseli Seçin:</label>
        <input type="file" name="urun_resim" accept="image/*">
        
        <button type="submit" name="urun_kaydet">ÜRÜNÜ YAYINLA</button>
    </form>
    <a href="index.php" class="back-link">Vazgeç ve Dön</a>
</div>

</body>
</html>