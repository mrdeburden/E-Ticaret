<?php
session_start();
include 'baglan.php';

// 1. Giriş Kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// 2. Kullanıcı ID'sini Bul
$u_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$u_sor->bind_param("s", $username);
$u_sor->execute();
$user_id = $u_sor->get_result()->fetch_assoc()['ID'];

// 3. Siparişleri Çek (En yeniden eskiye)
$siparis_sor = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$siparis_sor->bind_param("i", $user_id);
$siparis_sor->execute();
$siparisler = $siparis_sor->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Aldıklarım (Siparişlerim) - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; color: #333; }

        /* Navbar */
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: 700; color: #6A0DAD; text-decoration: none; }
        .nav-link { color: #333; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 5px; }

        .container { max-width: 1000px; margin: 50px auto; padding: 0 20px; }
        h2 { margin-bottom: 30px; color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }

        /* Sipariş Tablosu */
        .order-card { background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden; }
        
        .order-header { background: #fdfdfd; padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .order-info span { display: block; font-size: 13px; color: #777; margin-bottom: 3px; }
        .order-info strong { color: #333; font-size: 15px; }

        .order-body { padding: 20px; }
        
        /* Durum Renkleri */
        .status { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .bekliyor { background: #fff3cd; color: #856404; }
        .hazirlaniyor { background: #d1ecf1; color: #0c5460; }
        .kargolandi { background: #cce5ff; color: #004085; }
        .tamamlandi { background: #d4edda; color: #155724; }

        /* İçerik Gösterme */
        .order-items { margin-top: 15px; border-top: 1px dashed #eee; padding-top: 15px; }
        .item-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; color: #555; align-items: center; }
        .item-row img { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px; vertical-align: middle; }

        .empty-state { text-align: center; padding: 50px; color: #888; }
        .btn-home { background: #6A0DAD; color: white; padding: 10px 20px; border-radius: 20px; text-decoration: none; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">NEEDED.</a>
        <a href="index.php" class="nav-link"><i class="fas fa-arrow-left"></i> Alışverişe Dön</a>
    </nav>

    <div class="container">
        <h2><i class="fas fa-box-open" style="color:#3498db;"></i> Aldıklarım (Siparişlerim)</h2>

        <?php if ($siparisler->num_rows > 0): ?>
            <?php while($siparis = $siparisler->fetch_assoc()): 
                // Tarihi düzgün formatla
                $tarih = date("d.m.Y H:i", strtotime($siparis['created_at']));
                
                // Duruma göre renk belirle
                $durum_class = "bekliyor"; 
                $d = strtolower($siparis['status']);
                if(strpos($d, 'hazır') !== false) $durum_class = "hazirlaniyor";
                if(strpos($d, 'kargo') !== false) $durum_class = "kargolandi";
                if(strpos($d, 'tamam') !== false) $durum_class = "tamamlandi";
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div style="display:flex; gap: 40px;">
                            <div class="order-info">
                                <span>Sipariş Tarihi</span>
                                <strong><?php echo $tarih; ?></strong>
                            </div>
                            <div class="order-info">
                                <span>Toplam Tutar</span>
                                <strong><?php echo number_format($siparis['total_amount'], 2, ',', '.'); ?> ₺</strong>
                            </div>
                            <div class="order-info">
                                <span>Teslimat Adresi</span>
                                <strong><?php echo htmlspecialchars($siparis['address']); ?></strong>
                            </div>
                        </div>
                        <div>
                            <span class="status <?php echo $durum_class; ?>"><?php echo $siparis['status']; ?></span>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <div class="order-items">
                            <?php
                            $detay_sor = $conn->prepare("SELECT order_items.*, products.name, products.image_url 
                                                         FROM order_items 
                                                         JOIN products ON order_items.product_id = products.product_id 
                                                         WHERE order_id = ?");
                            $detay_sor->bind_param("i", $siparis['id']);
                            $detay_sor->execute();
                            $detaylar = $detay_sor->get_result();
                            
                            while($urun = $detaylar->fetch_assoc()):
                            ?>
                                <div class="item-row">
                                    <div style="display:flex; align-items:center;">
                                        <img src="<?php echo !empty($urun['image_url']) ? $urun['image_url'] : 'https://via.placeholder.com/40'; ?>">
                                        <span><?php echo $urun['quantity']; ?> x <?php echo htmlspecialchars($urun['name']); ?></span>
                                    </div>
                                    <span><?php echo number_format($urun['price'] * $urun['quantity'], 2, ',', '.'); ?> ₺</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shopping-basket" style="font-size: 50px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Henüz hiç sipariş vermediniz.</h3>
                <a href="index.php" class="btn-home">Alışverişe Başla</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>