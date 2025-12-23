<?php
session_start();
include 'baglan.php';

// Giriş kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['siparisi_tamamla'])) {
    
    $username = $_SESSION['username'];
    $adres = htmlspecialchars($_POST['adres']); // Formdan gelen adres
    
    // 1. Kullanıcı ID'sini Bul
    $u_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
    $u_sor->bind_param("s", $username);
    $u_sor->execute();
    $user_id = $u_sor->get_result()->fetch_assoc()['ID'];

    // 2. Sepet Verilerini Çek
    $sepet_sor = $conn->prepare("SELECT cart.quantity, products.product_id, products.price 
                                 FROM cart 
                                 JOIN products ON cart.product_id = products.product_id 
                                 WHERE cart.user_id = ?");
    $sepet_sor->bind_param("i", $user_id);
    $sepet_sor->execute();
    $sepet_urunleri = $sepet_sor->get_result();

    // Sepet boşsa işlem yapma
    if ($sepet_urunleri->num_rows == 0) {
        header("Location: index.php");
        exit();
    }

    // 3. Toplam Tutarı Hesapla
    $toplam_tutar = 0;
    // Verileri bir diziye alalım, çünkü while döngüsü ile dönünce result seti sıfırlanır
    $urunler = []; 
    while($row = $sepet_urunleri->fetch_assoc()) {
        $toplam_tutar += ($row['price'] * $row['quantity']);
        $urunler[] = $row;
    }

    // 4. Siparişi 'orders' Tablosuna Kaydet
    $siparis_ekle = $conn->prepare("INSERT INTO orders (user_id, total_amount, address, status) VALUES (?, ?, ?, 'Bekliyor')");
    $siparis_ekle->bind_param("ids", $user_id, $toplam_tutar, $adres);
    
    if ($siparis_ekle->execute()) {
        // Yeni oluşturulan siparişin ID'sini al (order_items için lazım)
        $new_order_id = $conn->insert_id;

        // 5. Sepetteki Ürünleri 'order_items' Tablosuna Aktar ve Stoktan Düş
        foreach ($urunler as $urun) {
            // Sipariş detayını kaydet
            $detay_ekle = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $detay_ekle->bind_param("iiid", $new_order_id, $urun['product_id'], $urun['quantity'], $urun['price']);
            $detay_ekle->execute();

            // Stoktan düş
            $stok_dus = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
            $stok_dus->bind_param("ii", $urun['quantity'], $urun['product_id']);
            $stok_dus->execute();
        }

        // 6. Sepeti Boşalt (Mutlu Son)
        $sepeti_sil = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $sepeti_sil->bind_param("i", $user_id);
        $sepeti_sil->execute();

        // 7. Kullanıcıyı Yönlendir
        // Şimdilik ana sayfaya atıyoruz, sonra "Siparişlerim" sayfasına atarız.
        echo "<script>alert('Siparişiniz başarıyla alındı! Teşekkür ederiz.'); window.location.href='index.php';</script>";
        exit();

    } else {
        echo "Sipariş oluşturulurken bir hata oluştu: " . $conn->error;
    }

} else {
    // Sayfaya direkt girilmeye çalışılırsa at
    header("Location: index.php");
    exit();
}
?>