<?php 
session_start(); 
include 'baglan.php'; 

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- GİRİŞ YAPAN KULLANICI & SEPET ---
$current_user_id = null;
$sepet_sayisi = 0;

if (isset($_SESSION['username'])) {
    $nav_user = $_SESSION['username'];
    
    // Kullanıcı ID'sini bul
    $nav_sor = $conn->prepare("SELECT ID FROM users WHERE username = ?");
    $nav_sor->bind_param("s", $nav_user);
    $nav_sor->execute();
    $u_row = $nav_sor->get_result()->fetch_assoc();
    
    if($u_row) {
        $current_user_id = $u_row['ID'];
        
        // Sepet sayısını çek
        $s_sor = $conn->prepare("SELECT SUM(quantity) as sayi FROM cart WHERE user_id = ?");
        $s_sor->bind_param("i", $current_user_id);
        $s_sor->execute();
        $sepet_sayisi = $s_sor->get_result()->fetch_assoc()['sayi'] ?? 0;
    }
}

// --- KATEGORİ SEÇİMİNİ AL ---
$secilen_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Needed | Alışverişin Adresi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #6A0DAD; --bg: #f8f9fa; --white: #ffffff; --text: #333; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg); color: var(--text); }
        a { text-decoration: none; transition: 0.3s; }
        
        /* Navbar */
        nav.navbar { background: var(--white); padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 24px; font-weight: 700; color: var(--primary); letter-spacing: 1px; }
        
        /* KATEGORİ MENÜSÜ */
        .category-bar {
            background: white;
            padding: 10px 10%;
            display: flex;
            justify-content: center;
            gap: 20px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }
        .cat-link {
            color: #555;
            font-size: 15px;
            font-weight: 500;
            padding: 5px 15px;
            border-radius: 20px;
        }
        .cat-link:hover { color: var(--primary); background: #f3e5f5; }
        .cat-link.active { background: var(--primary); color: white; font-weight: 600; }

        /* Auth Butonları */
        .auth-buttons { display: flex; align-items: center; gap: 15px; }
        .sepet-btn { background: var(--primary); color: white; padding: 8px 18px; border-radius: 20px; font-size: 14px; cursor: pointer; border: none; display: flex; align-items: center; gap: 5px;}

        /* Dropdown Menü */
        .dropdown-container { position: relative; }
        .dropdown-menu { display: none; position: absolute; top: 45px; right: 0; background: white; box-shadow: 0 8px 25px rgba(0,0,0,0.15); border-radius: 12px; width: 220px; z-index: 1000; padding: 10px; }
        .dropdown-menu.active { display: block; }
        .dropdown-menu a { display: flex; align-items: center; gap: 10px; padding: 10px; color: #333; border-radius: 5px; }
        .dropdown-menu a:hover { background: #f0f0f0; }

        /* Hero & Grid */
        .hero { height: 250px; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/karadelik.jpg'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; margin-bottom: 20px; }
        .search-container input { width: 350px; padding: 12px 25px; border-radius: 30px; border: none; outline: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; }
        .product-card { background: var(--white); border-radius: 15px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); display: flex; flex-direction: column; transition: 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .product-image { width: 100%; height: 220px; object-fit: cover; }
        .product-info { padding: 15px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        
        .product-name { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px; height: 44px; overflow: hidden; }
        .product-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
        .price { font-size: 18px; font-weight: 700; color: var(--primary); }

        .buy-btn { background: var(--primary); color: white; padding: 8px 15px; border-radius: 8px; font-size: 13px; border: none; cursor: pointer; }
        .own-product { background: #eee; color: #777; padding: 8px 15px; border-radius: 8px; font-size: 12px; border: none; cursor: default; }
        .fav-link { color: #888; font-size: 18px; transition: 0.3s; }
        .fav-link.active { color: #ff4757; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">NEEDED.</a>
        
        <div class="auth-buttons">
            <?php if ($current_user_id): ?>
                <a href="urun_ekle.php" style="color:#333; font-weight:600; margin-right:10px;">+ Ürün Sat</a>
                
                <div class="dropdown-container">
                    <button class="sepet-btn" onclick="toggleMenu('sepet-dropdown')">
                        <i class="fas fa-shopping-cart"></i> (<?php echo $sepet_sayisi; ?>)
                    </button>
                    <div id="sepet-dropdown" class="dropdown-menu">
                        <a href="sepet.php" style="justify-content:center; color:var(--primary); font-weight:bold;">Sepete Git</a>
                    </div>
                </div>

                <div class="dropdown-container">
                    <div class="sepet-btn" style="background:#333; margin-left:10px;" onclick="toggleMenu('user-dropdown')">
                        👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </div>
                    <div id="user-dropdown" class="dropdown-menu">
                        <a href="favorilerim.php"><i class="fas fa-heart" style="color:#ff4757;"></i> Favorilerim</a>
                        
                        <a href="siparislerim.php"><i class="fas fa-box" style="color:#3498db;"></i> Aldıklarım</a>
                        
                        <a href="satislarim.php"><i class="fas fa-hand-holding-usd" style="color:#27ae60;"></i> Gelen Siparişler</a>

                        <a href="ilanlarim.php"><i class="fas fa-edit" style="color:#f39c12;"></i> İlanlarım / Stok</a>
                        
                        <hr style="margin: 5px 0; border:0; border-top:1px solid #eee;">
                        <a href="cikis.php" style="color:red;"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" style="font-weight: 600; color: var(--primary);">Giriş Yap</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="category-bar">
        <a href="index.php" class="cat-link <?php echo ($secilen_kategori == '') ? 'active' : ''; ?>">Tüm Ürünler</a>
        <a href="index.php?kategori=teknoloji" class="cat-link <?php echo ($secilen_kategori == 'teknoloji') ? 'active' : ''; ?>">Teknoloji</a>
        <a href="index.php?kategori=giyim" class="cat-link <?php echo ($secilen_kategori == 'giyim') ? 'active' : ''; ?>">Giyim</a>
        <a href="index.php?kategori=aksesuar" class="cat-link <?php echo ($secilen_kategori == 'aksesuar') ? 'active' : ''; ?>">Aksesuar</a>
        <a href="index.php?kategori=diger" class="cat-link <?php echo ($secilen_kategori == 'diger') ? 'active' : ''; ?>">Diğer</a>
    </div>

    <section class="hero">
        <h1>
            <?php 
                if($secilen_kategori) { echo ucfirst($secilen_kategori) . " Ürünleri"; }
                else { echo "Aradığın Her Şey Burada"; }
            ?>
        </h1>
        <div class="search-container">
           <form action="index.php" method="GET">
               <input type="text" name="ara" placeholder="Ürün ara..." value="<?php echo isset($_GET['ara']) ? htmlspecialchars($_GET['ara']) : ''; ?>">
           </form>
        </div>
    </section>

    <div class="container">
        <div class="product-grid">
            <?php
            // SQL SORGUSUNU HAZIRLA (Filtreleme)
            $sql = "SELECT * FROM products";
            $params = [];
            $types = "";

            // 1. Kategori Filtresi
            if (!empty($secilen_kategori)) {
                $sql .= " WHERE category = ?";
                $params[] = $secilen_kategori;
                $types .= "s";
            }
            // 2. Arama Filtresi
            elseif (isset($_GET['ara']) && !empty($_GET['ara'])) {
                $sql .= " WHERE name LIKE ?";
                $params[] = "%" . $_GET['ara'] . "%";
                $types .= "s";
            }

            $sql .= " ORDER BY product_id DESC";

            // Sorguyu Çalıştır
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0):
                while ($u = $result->fetch_assoc()):
                    
                    // Favori Kontrolü
                    $is_fav = false;
                    if ($current_user_id) {
                        $fav_kontrol = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
                        $fav_kontrol->bind_param("ii", $current_user_id, $u['product_id']);
                        $fav_kontrol->execute();
                        if ($fav_kontrol->get_result()->num_rows > 0) $is_fav = true;
                    }
            ?>
                <div class="product-card">
                    <a href="urun_detay.php?id=<?php echo $u['product_id']; ?>">
                        <img src="<?php echo !empty($u['image_url']) ? $u['image_url'] : 'https://via.placeholder.com/300'; ?>" class="product-image">
                    </a>
                    
                    <div class="product-info">
                        <div style="display:flex; justify-content:space-between;">
                            <div class="product-name"><?php echo htmlspecialchars($u['name']); ?></div>
                            <a href="favori_islem.php?id=<?php echo $u['product_id']; ?>" class="fav-link <?php echo $is_fav ? 'active' : ''; ?>">
                                <i class="<?php echo $is_fav ? 'fas' : 'far'; ?> fa-heart"></i>
                            </a>
                        </div>
                        
                        <div class="product-footer">
                            <div class="price"><?php echo number_format($u['price'], 2, ',', '.'); ?> ₺</div>
                            
                            <?php if ($current_user_id && $current_user_id == $u['user_id']): ?>
                                <button class="own-product">Bu Ürün Sizin</button>
                            <?php else: ?>
                                <form action="sepet_islem.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $u['product_id']; ?>">
                                    <button type="submit" name="sepete_ekle" class="buy-btn">
                                        <i class="fas fa-cart-plus"></i> Ekle
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                    <i class="fas fa-search" style="font-size: 40px; color: #ddd; margin-bottom: 15px;"></i>
                    <p>Aradığınız kriterlere uygun ürün bulunamadı.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if(m.id !== id) m.classList.remove('active');
            });
            menu.classList.toggle('active');
        }

        // Tıklama dışına basınca menüyü kapat
        window.onclick = function(e) {
            if (!e.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('active'));
            }
        }
    </script>
</body>
</html>