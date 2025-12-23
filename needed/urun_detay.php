<?php
session_start();
include 'baglan.php';

// 1. URL'den ID Geldi mi Kontrol Et
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// 2. Ürün Bilgilerini Çek
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$urun = $result->fetch_assoc();

if (!$urun) {
    echo "<div style='text-align:center; padding:50px;'>Ürün bulunamadı. <a href='index.php'>Anasayfaya Dön</a></div>";
    exit();
}

// 3. Şu anki Kullanıcının ID'sini Bul (Sahiplik Kontrolü İçin)
$current_user_id = null;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
    $user_sor->bind_param("s", $username);
    $user_sor->execute();
    $u_row = $user_sor->get_result()->fetch_assoc();
    if($u_row) {
        $current_user_id = $u_row['ID'];
    }
}

// 4. Favori Kontrolü
$is_fav = false;
if ($current_user_id) {
    $fav_sor = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $fav_sor->bind_param("ii", $current_user_id, $product_id);
    $fav_sor->execute();
    if ($fav_sor->get_result()->num_rows > 0) {
        $is_fav = true;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($urun['name']); ?> - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; color: #333; }
        
        /* Navbar */
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: 700; color: #6A0DAD; text-decoration: none; }
        .nav-back { color: #555; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .nav-back:hover { color: #6A0DAD; }

        /* Detay Alanı */
        .container { max-width: 1100px; margin: 50px auto; padding: 0 20px; display: flex; flex-wrap: wrap; gap: 40px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        
        .image-col { flex: 1; min-width: 300px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border-radius: 12px; padding: 20px; }
        .image-col img { max-width: 100%; max-height: 500px; object-fit: contain; mix-blend-mode: multiply; }

        .info-col { flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center; }
        
        h1 { font-size: 32px; margin-bottom: 10px; color: #222; }
        .price { font-size: 28px; color: #6A0DAD; font-weight: 700; margin-bottom: 20px; }
        .desc { line-height: 1.6; color: #666; margin-bottom: 30px; font-size: 15px; }
        
        .meta-info { margin-bottom: 30px; font-size: 14px; color: #888; }
        .stok-durum { color: #27ae60; font-weight: 600; }
        .tukendi { color: #e74c3c; font-weight: 600; }

        .actions { display: flex; gap: 15px; align-items: center; }
        
        .btn-add { flex: 1; background: #6A0DAD; color: white; padding: 15px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-add:hover { background: #550a8a; transform: translateY(-2px); }
        
        .btn-disabled { flex: 1; background: #eee; color: #999; padding: 15px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: default; display: flex; justify-content: center; align-items: center; gap: 10px; }

        .btn-fav { width: 50px; height: 50px; display: flex; justify-content: center; align-items: center; border: 2px solid #eee; border-radius: 8px; color: #ccc; font-size: 20px; transition: 0.3s; cursor: pointer; text-decoration: none; }
        .btn-fav:hover { border-color: #ff4757; color: #ff4757; }
        .btn-fav.active { background: #ff4757; border-color: #ff4757; color: white; }

    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">NEEDED.</a>
        <a href="index.php" class="nav-back"><i class="fas fa-arrow-left"></i> Alışverişe Dön</a>
    </nav>

    <div class="container">
        <div class="image-col">
            <img src="<?php echo !empty($urun['image_url']) ? $urun['image_url'] : 'https://via.placeholder.com/500'; ?>" alt="<?php echo htmlspecialchars($urun['name']); ?>">
        </div>

        <div class="info-col">
            <h1><?php echo htmlspecialchars($urun['name']); ?></h1>
            <div class="price"><?php echo number_format($urun['price'], 2, ',', '.'); ?> ₺</div>
            
            <div class="meta-info">
                Stok Durumu: 
                <?php if ($urun['stock_quantity'] > 0): ?>
                    <span class="stok-durum">Stokta Var (<?php echo $urun['stock_quantity']; ?> Adet)</span>
                <?php else: ?>
                    <span class="tukendi">Tükendi</span>
                <?php endif; ?>
            </div>

            <p class="desc">
                <?php echo nl2br(htmlspecialchars($urun['description'])); ?>
            </p>

            <div class="actions">
                
                <?php 
                // --- SAHİPLİK VE STOK KONTROLÜ BAŞLANGICI ---
                
                // 1. Durum: Ürün benim mi?
                if ($current_user_id && $current_user_id == $urun['user_id']): 
                ?>
                    <button class="btn-disabled">
                        <i class="fas fa-user-circle"></i> BU ÜRÜN SİZİN
                    </button>

                <?php 
                // 2. Durum: Stok var mı?
                elseif ($urun['stock_quantity'] > 0): 
                ?>
                    <form action="sepet_islem.php" method="POST" style="flex:1;">
                        <input type="hidden" name="product_id" value="<?php echo $urun['product_id']; ?>">
                        <button type="submit" name="sepete_ekle" class="btn-add">
                            <i class="fas fa-shopping-bag"></i> SEPETE EKLE
                        </button>
                    </form>

                <?php 
                // 3. Durum: Stok bitmiş
                else: 
                ?>
                    <button class="btn-disabled">STOK TÜKENDİ</button>
                <?php endif; ?>
                
                <a href="favori_islem.php?id=<?php echo $urun['product_id']; ?>" class="btn-fav <?php echo $is_fav ? 'active' : ''; ?>">
                    <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart"></i>
                </a>
            </div>
        </div>
    </div>

</body>
</html>