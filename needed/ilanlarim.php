<?php
session_start();
include 'baglan.php';

// Giriş Kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Kullanıcı ID'sini Bul
$u_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$u_sor->bind_param("s", $username);
$u_sor->execute();
$user_id = $u_sor->get_result()->fetch_assoc()['ID'];

// KULLANICININ KENDİ ÜRÜNLERİNİ ÇEK
$urun_sor = $conn->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY product_id DESC");
$urun_sor->bind_param("i", $user_id);
$urun_sor->execute();
$urunler = $urun_sor->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İlanlarım / Ürün Yönetimi - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; color: #333; }
        
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: 700; color: #6A0DAD; text-decoration: none; }
        .nav-link { color: #333; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 5px; }

        .container { max-width: 1000px; margin: 50px auto; padding: 0 20px; }
        h2 { margin-bottom: 30px; color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }

        /* Ürün Listesi Tablo Görünümü yerine Kart Listesi */
        .manage-card { background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; padding: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px; }
        
        .product-brief { display: flex; align-items: center; gap: 15px; flex: 2; min-width: 250px; }
        .product-brief img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        .p-name { font-weight: 600; font-size: 16px; color: #333; display: block; }
        .p-cat { font-size: 13px; color: #888; background: #eee; padding: 2px 8px; border-radius: 10px; }

        .stock-form { display: flex; align-items: center; gap: 10px; flex: 1; }
        .stock-input { width: 70px; padding: 8px; border: 1px solid #ddd; border-radius: 6px; text-align: center; }
        .update-btn { background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .update-btn:hover { background: #2980b9; }

        .delete-form { flex: 0; }
        .delete-btn { background: #e74c3c; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .delete-btn:hover { background: #c0392b; }

        .empty-state { text-align: center; padding: 50px; color: #888; }
        .add-new-btn { background: #6A0DAD; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; font-weight: bold; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">NEEDED.</a>
        <a href="index.php" class="nav-link"><i class="fas fa-arrow-left"></i> Alışverişe Dön</a>
    </nav>

    <div class="container">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2><i class="fas fa-edit" style="color:#f39c12;"></i> İlanlarım & Stok Yönetimi</h2>
            <a href="urun_ekle.php" class="add-new-btn">+ Yeni Ürün Ekle</a>
        </div>

        <?php if (isset($_GET['durum'])): ?>
            <?php if ($_GET['durum'] == 'guncellendi'): ?>
                <div style="padding:10px; background:#d4edda; color:#155724; border-radius:5px; margin-bottom:15px;">Stok başarıyla güncellendi.</div>
            <?php elseif ($_GET['durum'] == 'silindi'): ?>
                <div style="padding:10px; background:#f8d7da; color:#721c24; border-radius:5px; margin-bottom:15px;">Ürün yayından kaldırıldı.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($urunler->num_rows > 0): ?>
            <?php while($urun = $urunler->fetch_assoc()): ?>
                <div class="manage-card">
                    <div class="product-brief">
                        <img src="<?php echo !empty($urun['image_url']) ? $urun['image_url'] : 'https://via.placeholder.com/70'; ?>">
                        <div>
                            <span class="p-name"><?php echo htmlspecialchars($urun['name']); ?></span>
                            <span class="p-cat"><?php echo ucfirst($urun['category']); ?></span>
                            <div style="font-size:14px; color:#6A0DAD; font-weight:bold; margin-top:5px;">
                                <?php echo number_format($urun['price'], 2, ',', '.'); ?> ₺
                            </div>
                        </div>
                    </div>

                    <form action="ilan_islem.php" method="POST" class="stock-form">
                        <input type="hidden" name="product_id" value="<?php echo $urun['product_id']; ?>">
                        <div style="text-align:center;">
                            <label style="font-size:11px; color:#777; display:block;">Stok Adedi</label>
                            <input type="number" name="new_stock" class="stock-input" value="<?php echo $urun['stock_quantity']; ?>" min="0" required>
                        </div>
                        <button type="submit" name="stok_guncelle" class="update-btn">Güncelle</button>
                    </form>

                    <form action="ilan_islem.php" method="POST" class="delete-form" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                        <input type="hidden" name="product_id" value="<?php echo $urun['product_id']; ?>">
                        <button type="submit" name="urun_sil" class="delete-btn" title="İlanı Sil">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list" style="font-size: 50px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Henüz hiç ürün eklememişsiniz.</h3>
                <p>Hemen satış yapmaya başlamak için yukarıdaki butonu kullanın!</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>