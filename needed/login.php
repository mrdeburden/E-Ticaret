<?php
session_start();
include 'baglan.php';

$error_msg = "";

if (isset($_POST['login_buton'])) {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    $sorgu = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $sorgu->bind_param("ss", $user_input, $user_input);
    $sorgu->execute();
    $sonuc = $sorgu->get_result();

    if ($sonuc->num_rows > 0) {
        $user_data = $sonuc->fetch_assoc();
        
        // Şifre kontrolü
        if (password_verify($pass_input, $user_data['password'])) {
            // Oturum bilgileri
            $_SESSION['username'] = $user_data['username']; 
            $_SESSION['user_id'] = $user_data['id'];

            session_write_close();
            header("Location: index.php"); 
            exit();
        } else {
            $error_msg = "Şifre hatalı!";
        }
    } else {
        $error_msg = "Kullanıcı adı veya e-posta bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Needed</title>
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #F5F5F5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 400px; 
        }
        h2 { color: #6A0DAD; text-align: center; margin-bottom: 20px; }
        input { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background-color: #6A0DAD; 
            color: white; 
            border: none; 
            border-radius: 25px; 
            cursor: pointer; 
            font-weight: 600; 
            margin-top: 10px;
            transition: background 0.3s;
        }
        button:hover { background-color: #8A2BE2; }
        .error-box {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Needed Giriş Yap</h2>

    <?php if ($error_msg != ""): ?>
        <div class="error-box"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Kullanıcı Adı veya E-posta" required>
        <input type="password" name="password" placeholder="Şifreniz" required>
        <button type="submit" name="login_buton">GİRİŞ YAP</button>
    </form>
    
    <p style="text-align:center; font-size: 0.8em; margin-top: 15px;">
        Hesabınız yok mu? <a href="kayit_sayfasi.php" style="color:#6A0DAD; font-weight:bold;">Üye Ol</a>
    </p>
</div>

</body>
</html>