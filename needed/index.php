<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Needed</title>
    
    
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        a {
            text-decoration: none;
        }
        
        .page-container {
            overflow-x: hidden; 
        }


        .sidebar {
            background-color: var(--color-primary-mor);
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width-desktop);
            height: 100vh; 
            display: flex;
            flex-direction: column; 
            color: var(--color-white);
            padding: 20px 0;
            z-index: 1000; 
            transition: transform var(--transition-speed) ease-in-out; 
        }

        .logo {
            font-size: 1.5em;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: var(--color-white);
        }

        .main-nav {
            flex-grow: 1; 
        }

        .main-nav ul {
            list-style: none;
            padding: 0;
        }

        .main-nav li a {
            display: block;
            padding: 15px 25px;
            color: var(--color-white);
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s;
            text-transform: uppercase;
        }

        .main-nav li.active {
            background-color: var(--color-light-mor); 
        }

        .main-nav li:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.1); 
        }

        .help-center {
            padding: 20px 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .help-center a {
            color: var(--color-white);
            font-size: 0.9em;
            opacity: 0.8;
            transition: opacity 0.3s;
        }


        .main-content {
            
            margin-left: var(--sidebar-width-desktop); 
            background-color: var(--color-white);
            min-height: 100vh; 
            padding: 20px 40px; 
            transition: margin-left var(--transition-speed); 
        }

        .header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-light-gray);
            margin-bottom: 30px;
        }
        
        .search-box {
            flex-grow: 1;
            max-width: 400px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        
        .header-icons span {
            font-size: 1.5em;
            color: var(--color-dark-gray);
            cursor: pointer;
        }
        
        .hero-banner {
            height: 250px;
            background: url('https://via.placeholder.com/1200x250/B8B8D1/6A0DAD?text=Yeni+Koleksiyonu+Keşfet') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-left: 5%;
            margin-bottom: 50px;
            border-radius: 8px;
        }

        .hero-banner button {
            background-color: var(--color-primary-mor);
            color: var(--color-white);
            border: none;
            padding: 12px 25px;
            font-size: 1em;
            font-weight: 600;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .hero-banner button:hover {
            background-color: var(--color-light-mor);
        }

        h3 {
            color: var(--color-dark-gray);
            font-size: 1.8em;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .product-card {
            background-color: var(--color-white);
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            padding: 15px;
            transition: box-shadow 0.3s;
        }
        
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .product-card .name {
            font-weight: 400;
            color: var(--color-dark-gray);
            margin-bottom: 5px;
        }
        
        .product-card .price {
            font-weight: 700;
            color: var(--color-primary-mor);
            font-size: 1.2em;
        }

        .footer {
            background-color: var(--color-dark-gray);
            color: var(--color-white);
            padding: 40px 40px;
            margin-left: var(--sidebar-width-desktop); 
            transition: margin-left var(--transition-speed);
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .footer h4 {
            font-size: 1.1em;
            margin-bottom: 15px;
            color: var(--color-light-mor);
        }
        
        .footer ul {
            list-style: none;
            padding: 0;
        }
        
        .footer ul li a {
            color: #ccc;
            font-size: 0.9em;
            line-height: 1.8;
            transition: color 0.3s;
        }
        
        .footer ul li a:hover {
            color: var(--color-white);
        }
        
        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.8em;
            color: #aaa;
        }

        .menu-toggle {
            display: none; 
            position: fixed; 
            top: 20px;
            left: 20px;
            z-index: 1001; 
            cursor: pointer;
            font-size: 30px;
            line-height: 1;
            color: var(--color-primary-mor);
        }

        @media (max-width: 992px) {
           
            .sidebar {
                transform: translateX(-100%); 
                width: 70%; 
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            }
            .main-content {
                margin-left: 0; 
                padding: 15px;
            }
            
            .footer {
                margin-left: 0;
                padding: 30px 15px;
            }

            .menu-toggle {
                display: block; 
            }

            .header {
                padding-top: 50px; 
                margin-bottom: 20px;
            }
            
            .search-box {
                max-width: none;
            }

            
            .page-container.menu-open .sidebar {
                transform: translateX(0); 
            }
        }
        
        @media (max-width: 576px) {
             .product-grid {
                grid-template-columns: 1fr 1fr; 
             }
             
             .footer-content {
                 flex-direction: column;
                 gap: 30px;
             }
        }

    </style>
</head>
<body>

    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    
    <div class="page-container">
        
        <div class="sidebar">
            <div class="logo">
                Needed
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li class="active"><a href="#">YENİ ÜRÜNLER</a></li>
                    <li><a href="#">GİYİM</a></li>
                    <li><a href="#">AKSESUARLAR</a></li>
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
                    <span>🛒</span>
                    <span>👤</span>
                    <span>♥</span>
                </div>
            </header>
            
            <section class="hero-banner">
                <button>ŞİMDİ KEŞFET</button>
            </section>

            <section class="featured-products">
                <h3>Popüler Ürünler</h3>
                
                <div class="product-grid">
                    <div class="product-card">
                        <img src="" alt="Ürün Adı 1">
                        <div class="name">Minimal Sweatshirt</div>
                        <div class="price">149.90 ₺</div>
                    </div>
                    <div class="product-card">
                        <img src="" alt="Ürün Adı 2">
                        <div class="name">Gaming Laptop</div>
                        <div class="price">31.999 ₺</div>
                    </div>
                    <div class="product-card">
                        <img src="" alt="Ürün Adı 3">
                        <div class="name">Sade Kupa</div>
                        <div class="price">39.00 ₺</div>
                    </div>
                     <div class="product-card">
                        <img src="" alt="Ürün Adı 4">
                        <div class="name">Parker Ustura</div>
                        <div class="price">999.99 ₺</div>
                    </div>
                </div>
                
                <h3>Yeni Gelenler</h3>
                <div class="product-grid">
                    <div class="product-card">
                        <img src="" alt="Yeni Ürün 5">
                        <div class="name">Gri Pantolon</div>
                        <div class="price">199.90 ₺</div>
                    </div>
                     <div class="product-card">
                        <img src="" alt="Yeni Ürün 6">
                        <div class="name">Kimono Ceket</div>
                        <div class="price">1199.99 ₺</div>
                    </div>
                </div>
            </section>
        </div>

    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <div>
                <h4>Kurumsal</h4>
                <ul>
                    <li><a href="#">Hakkımızda</a></li>
                    <li><a href="#">Kariyer</a></li>
                    <li><a href="#">Gizlilik Politikası</a></li>
                </ul>
            </div>
            <div>
                <h4>Yardım</h4>
                <ul>
                    <li><a href="#">Sıkça Sorulanlar</a></li>
                    <li><a href="#">Kargo ve Teslimat</a></li>
                    <li><a href="#">İade Koşulları</a></li>
                </ul>
            </div>
          
        </div>
    </footer>


    <script>
        
        function toggleMenu() {
            const pageContainer = document.querySelector('.page-container');
            pageContainer.classList.toggle('menu-open');
        }
    </script>
</body>
</html>