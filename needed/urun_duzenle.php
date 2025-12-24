<?php
session_start();
include 'baglan.php';

// 1. Giriş Yapılmış mı?
if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 2. Linkte ID var mı?
if (!isset($_GET['id'])) { 
    header("Location: ilanlarim.php"); 
    exit(); 
}

$product_id = intval($_GET['id']);
$username = $_SESSION['username'];

// 3. Kullanıcı ID'sini Bul
$u_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$u_sor->bind_param("s", $username);
$u_sor->execute();
$user_id = $u_sor->get_result()->fetch_assoc()['ID'];

// 4. Ürünü Çek (Sadece bu kullanıcıya aitse!)
$urun_sor = $conn->prepare("SELECT * FROM products WHERE product_id = ? AND user_id = ?");
$urun_sor->bind_param("ii", $product_id, $user_id);
$urun_sor->execute();
$urun = $urun_sor->get_result()->fetch_assoc();

if (!$urun) {
    echo "<h3 style='text-align:center; margin-top:50px; font-family:sans-serif;'>Bu ürün size ait değil veya bulunamadı!</h3>";
    echo "<p style='text-align:center; font-family:sans-serif;'><a href='ilanlarim.php'>Geri Dön</a></p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Düzenle - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* SİTE GENEL TEMASIYLA UYUMLU STİLLER */
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        
        .add-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        
        /* BAŞLIK RENGİ: MOR */
        h2 { color: #6A0DAD; text-align: center; margin-bottom: 20px; }
        
        label { font-size: 12px; color: #777; font-weight: 600; display: block; margin-top: 10px; }
        
        input, textarea, select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: 'Poppins', sans-serif; outline: none; }
        input:focus, textarea:focus, select:focus { border-color: #6A0DAD; }
        
        /* BUTON RENGİ: MOR */
        button { width: 100%; padding: 14px; background: #6A0DAD; color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: bold; margin-top: 20px; transition: 0.3s; font-size: 15px; }
        button:hover { background: #5a0b9e; }
        
        .vazgec-btn { display:block; text-align:center; margin-top:15px; color: #6A0DAD; text-decoration:none; font-weight: 500; font-size: 14px; }
        .vazgec-btn:hover { text-decoration: underline; }
        
        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        .file-input-wrapper { display: flex; align-items: center; gap: 10px; background: #f9f9f9; padding: 10px; border-radius: 6px; border: 1px dashed #ccc; margin-top: 5px; }
    </style>
</head>
<body>

<div class="add-card">
    <h2>İlanı Düzenle</h2>
    
    <form action="ilan_islem.php" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="product_id" value="<?php echo $urun['product_id']; ?>">
        <input type="hidden" name="eski_resim" value="<?php echo $urun['image_url']; ?>">

        <label>Ürün Adı</label>
        <input type="text" name="name" required value="<?php echo htmlspecialchars($urun['name']); ?>">
        
        <label>Kategori</label>
        <select name="category" required>
            <option value="teknoloji" <?php if($urun['category']=='teknoloji') echo 'selected'; ?>>Teknoloji</option>
            <option value="giyim" <?php if($urun['category']=='giyim') echo 'selected'; ?>>Giyim</option>
            <option value="aksesuar" <?php if($urun['category']=='aksesuar') echo 'selected'; ?>>Aksesuar</option>
            <option value="diger" <?php if($urun['category']=='diger') echo 'selected'; ?>>Diğer</option>
        </select>

        <label>Fiyat (₺)</label>
        <input type="number" name="price" step="0.01" required value="<?php echo $urun['price']; ?>">
        
        <label>Açıklama</label>
        <textarea name="description" rows="4"><?php echo htmlspecialchars($urun['description']); ?></textarea>
        
        <label>Stok Adedi</label>
        <input type="number" name="stock_quantity" value="<?php echo $urun['stock_quantity']; ?>" required>
        
        <label>Ürün Resmi</label>
        <div class="file-input-wrapper">
            <img src="<?php echo !empty($urun['image_url']) ? $urun['image_url'] : 'https://via.placeholder.com/60'; ?>" class="img-preview">
            <input type="file" name="image_file" accept="image/*" style="font-size: 12px;">
        </div>

        <button type="submit" name="urun_guncelle">KAYDET VE GÜNCELLE</button>
    </form>
    
    <a href="ilanlarim.php" class="vazgec-btn">Vazgeç</a>
</div>

</body>
</html>