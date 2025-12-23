<?php
session_start();
include 'baglan.php';

// Giriş kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// 1. Kullanıcı ID'sini al
$kullanici_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$kullanici_sor->bind_param("s", $username);
$kullanici_sor->execute();
$user_sonuc = $kullanici_sor->get_result();
$user = $user_sonuc->fetch_assoc();
$user_id = $user['ID'];

// 2. Sepeti ve Ürün Bilgilerini Çek (JOIN İşlemi)
// Cart tablosundaki product_id ile Products tablosundaki product_id'yi eşleştiriyoruz.
$sql = "SELECT cart.id as sepet_id, cart.quantity, products.name, products.price, products.image_url 
        FROM cart 
        LEFT JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sepet_urunleri = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepetim - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; margin: 0; }
        .container { max-width: 1000px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        h2 { color: #6A0DAD; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; color: #777; padding: 10px; border-bottom: 1px solid #ddd; }
        td { padding: 15px 10px; border-bottom: 1px solid #eee; vertical-align: middle; }
        
        .urun-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .toplam-fiyat { font-size: 24px; color: #6A0DAD; font-weight: bold; text-align: right; margin-top: 20px; }
        
        .btn-sil { background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        .btn-sil:hover { background: #c0392b; }
        
        .btn-ode { display: block; background: #6A0DAD; color: white; text-align: center; padding: 15px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold; }
        .btn-ode:hover { background: #560a8d; }
        
        .bos-sepet { text-align: center; padding: 50px; color: #777; }
        .nav-link { color: #6A0DAD; text-decoration: none; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="nav-link">← Alışverişe Dön</a>
    <h2>Sepetim</h2>

    <?php if ($sepet_urunleri->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>Fiyat</th>
                    <th>Adet</th>
                    <th>Toplam</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $genel_toplam = 0;
                while($row = $sepet_urunleri->fetch_assoc()): 
                    $ara_toplam = $row['price'] * $row['quantity'];
                    $genel_toplam += $ara_toplam;
                ?>
                <tr>
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <img src="<?php echo $row['image_url']; ?>" class="urun-img">
                        <span><?php echo $row['name']; ?></span>
                    </td>
                    <td><?php echo number_format($row['price'], 2); ?> ₺</td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo number_format($ara_toplam, 2); ?> ₺</td>
                    <td>
                        <form action="sepet_islem.php" method="POST">
                            <input type="hidden" name="sepet_id" value="<?php echo $row['sepet_id']; ?>">
                            <button type="submit" name="sepetten_sil" class="btn-sil">Sil</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="toplam-fiyat">
            Genel Toplam: <?php echo number_format($genel_toplam, 2); ?> ₺
        </div>

        <a href="odeme.php" class="btn-ode">ÖDEMEYİ TAMAMLA</a>

    <?php else: ?>
        <div class="bos-sepet">
            <h3>Sepetinizde henüz ürün yok.</h3>
            <p>Hemen alışverişe başlayıp harika ürünleri keşfedin!</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>