<?php
session_start();
include 'baglan.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kullanıcıyı ve rolünü veritabanından çekiyoruz
    $stmt = $conn->prepare("SELECT ID, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Şifre kontrolü
        if (password_verify($password, $row['password'])) {
            // Başarılı giriş: Session'ları oluştur
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['ID'];
            $_SESSION['role'] = $row['role']; // <-- ÖNEMLİ: Yetkiyi kaydediyoruz (admin/customer)
            
            header("Location: index.php");
            exit();
        } else {
            $hata = "Hatalı şifre!";
        }
    } else {
        $hata = "Kullanıcı bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - Needed</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h2 { color: #6A0DAD; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #6A0DAD; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        button:hover { background: #5a0b9e; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
        a { color: #6A0DAD; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Giriş Yap</h2>
        <?php if(isset($hata)) echo "<p class='error'>$hata</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Kullanıcı Adı" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit" name="login">GİRİŞ YAP</button>
        </form>
        <p style="margin-top:15px;">Hesabın yok mu? <a href="kayit_sayfasi.php">Kayıt Ol</a></p>
    </div>
</body>
</html>