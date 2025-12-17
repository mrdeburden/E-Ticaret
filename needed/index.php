<?php 
session_start(); 
include 'baglan.php'; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Needed | E-Ticaret</title>
    <style>
        :root {
            --color-primary-mor: #6A0DAD; 
            --color-light-mor: #8A2BE2;
            --color-white: #FFFFFF;
            --color-dark-gray: #333333;
            --color-light-gray: #F5F5F5;
            --sidebar-width-desktop: 250px; 
            --transition-speed: 0.3s;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        a { text-decoration: none; }
        
        body { background-color: var(--color-light-gray); }

        .sidebar {
            background-color: var(--color-primary-mor);
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width-desktop); height: 100vh; 
            display: flex; flex-direction: column; color: var(--color-white);
            padding: 20px 0; z-index: 1000; 
            transition: transform var(--transition-speed) ease-in-out; 
        }

        .logo { font-size: 1.5em; font-weight: 700; text-align: center; margin-bottom: 30px; color: var(--color-white); }
        .main-nav ul { list-style: none; }
        .main-nav li a {
            display: block; padding: 15px 25px; color: var(--color-white);
            font-size: 1.1em; font-weight: 600; transition: 0.3s; text-transform: uppercase;
        }
        .main-nav li.active { background-color: var(--color-light-mor); }
        .main-nav li:hover:not(.active) { background-color: rgba(255, 255, 255, 0.1); }

        .main-content {
            margin-left: var(--sidebar-width-desktop); 
            background-color: var(--color-white);
            min-height: 100vh; padding: 20px 40px; 
            transition: margin-left var(--transition-speed); 
        }

        .header {
            display: flex; justify-content: space-between; align-items: center;
            padding-bottom: 20px; border-bottom: 1px solid var(--color-light-gray);
            margin-bottom: 30px;
        }
        
        .search-box { flex-grow: 1; max-width: 400px; }
        .search-box input { width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 5px; }
        
        .header-icons { display: flex; align-items: center; gap: 20px; }
        .header-icons span { color: var(--color-dark-gray); cursor: pointer; font-size: 0.9em; font-weight: bold; }

        /* Kullanıcı Menüsü */
        .user-menu { position: relative; display: inline-block; }
        .user-menu span { cursor: pointer; color: var(--color-primary-mor); font-weight: bold; }
        #user-dropdown {
            display: none; position: absolute; right: 0; top: 30px;
            background: white; border: 1px solid #ddd; padding: 10px;
            border-radius: 5px; z-index: 100; min-width: 160px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        #user-dropdown a { display: block; color: var(--color-dark-gray); margin-bottom: 8px; font-size: 0.9em; }

        .hero-banner {
            height: 250px; background: #B8B8D1 url('https://via.placeholder.com/1200x250?text=Yeni+Koleksiyonu+Keşfet') center/cover;
            display: flex; align-items: center; padding-left: 5%;
            margin-bottom: 50px; border-radius: 8px;
        }
        .hero-banner button {
            background-color: var(--color-primary-mor); color: white;
            border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; font-weight: 600;
        }

        h3 { color: var(--color-dark-gray); margin-bottom: 25px; }
        
        .product-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px; margin-bottom: 60px;
        }

        .product-card {
            background: white; border: 1px solid #eee; border-radius: 8px;
            padding: 15px; text-align: center; transition: 0.3s;
        }
        .product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        .product-card .price { font-weight: 700; color: var(--color-primary-mor); font-size: 1.1em; }

        .footer {
            background-color: var(--color-dark-gray); color: white;
            padding: 40px; margin-left: var(--sidebar-width-desktop);
        }

        .menu-toggle { display: none; position: fixed; top: 20px; left: 20px; z-index: 1001; cursor: pointer; font-size: 30px; color: var(--color-primary-mor); }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); width: 70%; }
            .main-content, .footer { margin-left: 0; }
            .menu-toggle { display: block; }
            .menu-open .sidebar { transform: translateX(0); }
            .header { padding-top: 50px; }
        }
    </style>
</head>
<body id="body">

    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    
    <div class="page-container">
        <div class="sidebar">
            <div class="logo">Needed</div>
            <nav class="main-nav">
                <ul>
                    <li class="active"><a href="#">YENİ ÜRÜNLER</a></li>
                    <li><a href="#">GENEL</a></li>
                    <li><a href="#">GİYİM</a></li>
                    <li><a href="#">TEKNOLOJİ</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <header class="header">
                <div class="search-box">
                    <input type="text" placeholder="Ürün Ara...">
                </div>
                
                <div class="header-icons">
                    <span>sepetim</span>
                    <?php if (isset($_SESSION['username'])): ?>
                        <div class="user-menu">
                            <span onclick="toggleUserMenu()">👤 Hesabım (<?php echo htmlspecialchars($_SESSION['username']); ?>)</span>
                            <div id="user-dropdown">
                                <a href="profil.php">Profilim</a>
                                <a href="urun_ekle.php" style="color: #2ecc71; font-weight: bold;">➕ Ürün Ekle</a>
                                <a href="siparislerim.php">Siparişlerim</a>
                                <hr style="margin: 5px 0; border: 0; border-top: 1px solid #eee;">
                                <a href="cikis.php" style="color: #e74c3c; font-weight: bold;">🚪 Çıkış Yap</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php"><span>giriş</span></a>
                        <a href="kayit_sayfasi.php"><span>üye ol</span></a>
                    <?php endif; ?>
                </div>
            </header>
            
            <section class="hero-banner">
                <button>ŞİMDİ KEŞFET</button>
            </section>

            <section class="featured-products">
                <h3>Popüler Ürünler</h3>
                <div class="product-grid">
                    <div class="product-card">
                        <img src="https://via.placeholder.com/200" alt="Sweatshirt">
                        <div class="name">Minimal Sweatshirt</div>
                        <div class="price">149.90 ₺</div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/200" alt="Laptop">
                        <div class="name">Gaming Laptop</div>
                        <div class="price">31.999 ₺</div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/200" alt="Kupa">
                        <div class="name">Sade Kupa</div>
                        <div class="price">39.00 ₺</div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/200" alt="Ustura">
                        <div class="name">Parker Ustura</div>
                        <div class="price">999.99 ₺</div>
                    </div>
                </div>
                
                <h3>Yeni Gelenler</h3>
                <div class="product-grid">
                    <div class="product-card">
                        <img src="https://via.placeholder.com/200" alt="Pantolon">
                        <div class="name">Gri Pantolon</div>
                        <div class="price">199.90 ₺</div>
                    </div>
                    <div class="product-card">
                        <img src="https://via.placeholder.com/200" alt="Kimono">
                        <div class="name">Kimono Ceket</div>
                        <div class="price">1199.99 ₺</div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    
    <footer class="footer">
        <p>Destek</p>
    </footer>

    <script>
        function toggleMenu() {
            document.body.classList.toggle('menu-open');
        }

        function toggleUserMenu() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        // Dışarı tıklandığında menüleri kapat
        window.onclick = function(event) {
            if (!event.target.matches('.user-menu span')) {
                const dropdown = document.getElementById('user-dropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
        }
    </script>
</body>
</html>