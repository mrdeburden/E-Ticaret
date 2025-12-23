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
$user_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
$user_sor->bind_param("s", $username);
$user_sor->execute();
$user_id = $user_sor->get_result()->fetch_assoc()['ID'];

// 3. Sepet Toplamını Hesapla (Veritabanından)
// Kullanıcı ödeme yapacak ama sepeti boş mu? Kontrol edelim.
$sql = "SELECT SUM(products.price * cart.quantity) as toplam_tutar 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sonuc = $stmt->get_result()->fetch_assoc();
$toplam_tutar = $sonuc['toplam_tutar'];

// Eğer sepet boşsa anasayfaya at
if ($toplam_tutar == 0 || $toplam_tutar == null) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ödeme Yap - Needed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; margin: 0; color: #333; }
        
        .container { max-width: 900px; margin: 50px auto; padding: 20px; display: flex; gap: 30px; }
        
        /* Sol Taraf: Form */
        .form-section { flex: 2; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        
        /* Sağ Taraf: Özet */
        .summary-section { flex: 1; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); height: fit-content; }

        h2 { color: #6A0DAD; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px; font-size: 20px; }
        
        label { font-weight: 600; font-size: 14px; margin-bottom: 5px; display: block; color: #555; }
        input, textarea { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        input:focus, textarea:focus { border-color: #6A0DAD; outline: none; }
        
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }

        .total-price { font-size: 24px; font-weight: bold; color: #6A0DAD; text-align: center; margin: 20px 0; }
        
        .btn-pay { width: 100%; background: #27ae60; color: white; padding: 15px; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-pay:hover { background: #219150; }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="form-section">
            <h2><i class="fas fa-map-marker-alt"></i> Teslimat Adresi</h2>
            <form action="siparis_tamamla.php" method="POST">
                
                <label>Adres Başlığı (Ev, İş vb.)</label>
                <input type="text" name="adres_baslik" placeholder="Örn: Evim" required>

                <label>Açık Adres</label>
                <textarea name="adres" rows="4" placeholder="Mahalle, Sokak, No..." required></textarea>

                <h2 style="margin-top: 30px;"><i class="far fa-credit-card"></i> Kart Bilgileri</h2>
                
                <label>Kart Üzerindeki İsim</label>
                <input type="text" placeholder="Ad Soyad" required>

                <label>Kart Numarası</label>
                <input type="text" placeholder="0000 0000 0000 0000" maxlength="19" required>

                <div class="row">
                    <div class="col">
                        <label>Son Kullanma</label>
                        <input type="text" placeholder="AA/YY" maxlength="5" required>
                    </div>
                    <div class="col">
                        <label>CVV</label>
                        <input type="text" placeholder="123" maxlength="3" required>
                    </div>
                </div>

                <button type="submit" name="siparisi_tamamla" class="btn-pay">
                    <i class="fas fa-lock"></i> <?php echo number_format($toplam_tutar, 2, ',', '.'); ?> ₺ ÖDE
                </button>
            </form>
            <a href="sepet.php" style="display:block; text-align:center; margin-top:15px; color:#777; text-decoration:none;">Vazgeç ve Sepete Dön</a>
        </div>

        <div class="summary-section">
            <h2>Sipariş Özeti</h2>
            <p>Sepetinizdeki ürünlerin toplam tutarıdır.</p>
            <div class="total-price">
                <?php echo number_format($toplam_tutar, 2, ',', '.'); ?> ₺
            </div>
            <div style="font-size: 13px; color: #777;">
                <p><i class="fas fa-check-circle" style="color:#27ae60;"></i> Güvenli Ödeme</p>
                <p><i class="fas fa-truck" style="color:#6A0DAD;"></i> Ücretsiz Kargo</p>
            </div>
        </div>

    </div>

</body>
</html>