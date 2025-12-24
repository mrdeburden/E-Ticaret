<?php
session_start();
include 'baglan.php';

// 1. Giriş Kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// 2. Kullanıcı ID'sini Al
$kullanici_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$kullanici_sor->bind_param("s", $username);
$kullanici_sor->execute();
$user_sonuc = $kullanici_sor->get_result();
$user = $user_sonuc->fetch_assoc();
$user_id = $user['ID'];

// 3. Favori Ürünleri Çek (JOIN İşlemi)
// favorites tablosundaki product_id ile products tablosundaki bilgileri birleştiriyoruz.
$sql = "SELECT products.* FROM favorites 
        JOIN products ON favorites.product_id = products.product_id 
        WHERE favorites.user_id = ? 
        ORDER BY favorites.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$favoriler = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Favorilerim - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; margin: 0; }
        
        /* Navbar stili (Basit Tutuldu) */
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .logo { font-size: 24px; font-weight: 700; color: #6A0DAD; text-decoration: none; }
        .nav-link { color: #333; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 5px; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 30px; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; }
        
        /* Kart Tasarımı */
        .card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.3s; position: relative; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 250px; object-fit: cover; }
        .card-body { padding: 20px; }
        .price { font-size: 18px; font-weight: bold; color: #6A0DAD; display: block; margin-bottom: 10px; }
        h3 { margin: 0 0 10px; font-size: 18px; color: #333; }
        
        /* İşlem Butonları */
        .actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn { flex: 1; padding: 10px; text-align: center; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
        .btn-sepet { background: #6A0DAD; color: white; }
        .btn-sil { background: #ff4757; color: white; }
        .btn:hover { opacity: 0.9; }

        .empty-state { text-align: center; padding: 50px; color: #777; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">NEEDED.</a>
        <a href="index.php" class="nav-link"><i class="fas fa-arrow-left"></i> Alışverişe Dön</a>
    </nav>

    <div class="container">
        <h2><i class="fas fa-heart" style="color:#ff4757;"></i> Favori Ürünlerim</h2>

        <?php if ($favoriler->num_rows > 0): ?>
            <div class="grid">
                <?php while($row = $favoriler->fetch_assoc()): ?>
                    <div class="card">
                        <a href="urun_detay.php?id=<?php echo $row['product_id']; ?>">
                            <img src="<?php echo !empty($row['image_url']) ? $row['image_url'] : 'https://via.placeholder.com/300'; ?>">
                        </a>
                        <div class="card-body">
                            <span class="price"><?php echo number_format($row['price'], 2, ',', '.'); ?> ₺</span>
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            
                            <div class="actions">
                                <form action="sepet_islem.php" method="POST" style="flex:1;">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                    <button type="submit" name="sepete_ekle" class="btn btn-sepet">Sepete Ekle</button>
                                </form>

                                <a href="favori_islem.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sil">Kaldır</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="far fa-heart" style="font-size: 50px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Henüz favori ürününüz yok.</h3>
                <p>Beğendiğiniz ürünleri kalp ikonuna tıklayarak buraya ekleyebilirsiniz.</p>
                <a href="index.php" style="color: #6A0DAD; font-weight: bold;">Ürünleri Keşfet</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>