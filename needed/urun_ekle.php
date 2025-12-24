<?php 
session_start(); 
include 'baglan.php'; 

// Giriş kontrolü
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #F5F5F5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .add-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { color: #6A0DAD; text-align: center; margin-bottom: 20px; }
        input, textarea, select { width: 100%; padding: 12px; margin-top: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        button { width: 100%; padding: 14px; background: #2ecc71; color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: bold; margin-top: 20px; transition: 0.3s; }
        button:hover { background: #27ae60; }
        .vazgec-btn { display:block; text-align:center; margin-top:15px; color:#6A0DAD; text-decoration:none; font-weight: 500; }
    </style>
</head>
<body>

<div class="add-card">
    <h2>Yeni Ürün Ekle</h2>
    <form action="urun_islem.php" method="POST" enctype="multipart/form-data">
        
        <input type="text" name="name" required placeholder="Ürün Adı">
        
        <select name="category" required style="background-color: #fff;">
            <option value="" disabled selected>Kategori Seçiniz</option>
            <option value="teknoloji">Teknoloji</option>
            <option value="giyim">Giyim</option>
            <option value="aksesuar">Aksesuar</option>
            <option value="diger">Diğer</option>
        </select>

        <input type="number" name="price" step="0.01" required placeholder="Fiyat (0.00)">
        
        <textarea name="description" rows="4" placeholder="Açıklama"></textarea>
        
        <input type="number" name="stock_quantity" value="1" min="1" required placeholder="Stok Adedi">
        
        <div style="margin-top: 10px;">
            <input type="file" name="image_file" accept="image/*" required style="border:none; padding-left:0;">
        </div>

        <button type="submit" name="urun_kaydet">ÜRÜNÜ YAYINLA</button>
    </form>
    
    <a href="index.php" class="vazgec-btn">Vazgeç</a>
</div>

</body>
</html>