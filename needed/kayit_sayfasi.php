

<?php include 'baglan.php'; /*sitenin kayıt sayfası*/?>

<!DOCTYPE html>
<html lang="tr">
<head>
 
    <meta charset="UTF-8">
    <title>Üye Ol - Needed</title>
    <style>
        /* Sitenin mor temasını buraya da ekliyoruz */ 
        body { font-family: 'Poppins', sans-serif; background-color: #F5F5F5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .register-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { color: #6A0DAD; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #6A0DAD; color: white; border: none; border-radius: 25px; cursor: pointer; font-weight: 600; }
        button:hover { background-color: #8A2BE2; } 
    </style>
</head>

<body>

<div class="register-card">
    <h2>Needed Üye Kaydı</h2>
    <form action="kayit_islem.php" method="POST">
        <input type="text" name="username" placeholder="Kullanıcı Adı" required>
        <input type="email" name="email" placeholder="E-posta Adresiniz" required>
        <input type="password" name="password" placeholder="Şifreniz" required>
        <button type="submit" name="kayit_buton">KAYIT OL</button>
    </form>
    <p style="text-align:center; font-size: 0.8em; margin-top: 15px;">Zaten üye misiniz? <a href="login.php" style="color:#6A0DAD;">Giriş Yap</a></p>
</div>

</body>
</html>  