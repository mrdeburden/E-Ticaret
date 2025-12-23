<?php
session_start();
include 'baglan.php';

// Giriş Kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// 1. Satıcının (Oturum açan kişinin) ID'sini bul
$user_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$user_sor->bind_param("s", $username);
$user_sor->execute();
$current_user_id = $user_sor->get_result()->fetch_assoc()['ID'];

// 2. SANA GELEN SİPARİŞLERİ ÇEK (Karmaşık bir JOIN Sorgusu)
// Mantık: Sipariş detaylarına git -> Ürüne bağlan -> Eğer ürünün sahibi bensem -> Siparişin genel bilgilerini (adres, alıcı) getir.
$sql = "SELECT 
            order_items.quantity, 
            order_items.price as satis_fiyati,
            products.name as urun_adi, 
            products.image_url,
            orders.created_at as tarih,
            orders.address as teslimat_adresi,
            users.username as alici_adi
        FROM order_items
        JOIN products ON order_items.product_id = products.product_id
        JOIN orders ON order_items.order_id = orders.id
        JOIN users ON orders.user_id = users.ID
        WHERE products.user_id = ? 
        ORDER BY orders.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$satislar = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Gelen Siparişler (Satışlarım) - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; color: #333; }
        
        /* Navbar (Basit) */
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: 700; color: #6A0DAD; text-decoration: none; }
        .nav-link { color: #333; font-weight: 500; display: flex; align-items: center; gap: 5px; text-decoration: none; }

        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        h2 { margin-bottom: 30px; color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }

        /* Kart Tasarımı */
        .sale-card { background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px; padding: 20px; display: flex; gap: 20px; flex-wrap: wrap; }
        
        .img-area img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        
        .info-area { flex: 1; min-width: 250px; }
        .product-title { font-size: 16px; font-weight: 700; color: #333; margin-bottom: 5px; }
        .sale-date { font-size: 12px; color: #888; margin-bottom: 10px; display: block; }
        
        .buyer-area { flex: 1; min-width: 250px; background: #f9f9f9; padding: 15px; border-radius: 8px; border-left: 4px solid #6A0DAD; }
        .buyer-title { font-size: 13px; font-weight: bold; color: #6A0DAD; text-transform: uppercase; margin-bottom: 8px; }
        .address-box { font-size: 14px; color: #555; line-height: 1.5; }

        .price-badge { font-size: 18px; font-weight: bold; color: #27ae60; margin-top: 10px; }

        .empty-state { text-align: center; padding: 50px; color: #888; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">NEEDED.</a>
        <a href="index.php" class="nav-link"><i class="fas fa-arrow-left"></i> Alışverişe Dön</a>
    </nav>

    <div class="container">
        <h2><i class="fas fa-hand-holding-usd" style="color:#27ae60;"></i> Satışlarım & Gelen Siparişler</h2>

        <?php if ($satislar->num_rows > 0): ?>
            <?php while($satis = $satislar->fetch_assoc()): ?>
                <div class="sale-card">
                    <div class="img-area">
                        <img src="<?php echo !empty($satis['image_url']) ? $satis['image_url'] : 'https://via.placeholder.com/100'; ?>">
                    </div>

                    <div class="info-area">
                        <div class="product-title"><?php echo htmlspecialchars($satis['urun_adi']); ?></div>
                        <span class="sale-date"><i class="far fa-clock"></i> <?php echo date("d.m.Y H:i", strtotime($satis['tarih'])); ?></span>
                        
                        <div>Adet: <strong><?php echo $satis['quantity']; ?></strong></div>
                        <div class="price-badge">+ <?php echo number_format($satis['satis_fiyati'] * $satis['quantity'], 2, ',', '.'); ?> ₺</div>
                    </div>

                    <div class="buyer-area">
                        <div class="buyer-title">📦 Teslimat Bilgileri</div>
                        <div style="margin-bottom: 5px;"><strong>Alıcı:</strong> <?php echo htmlspecialchars($satis['alici_adi']); ?></div>
                        <div class="address-box">
                            <strong>Adres:</strong><br>
                            <?php echo nl2br(htmlspecialchars($satis['teslimat_adresi'])); ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-store-slash" style="font-size: 50px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Henüz bir satış yapmadınız.</h3>
                <p>Ürünlerinizi yükleyin ve müşterilerinizi bekleyin!</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>