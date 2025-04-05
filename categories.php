<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php"); // Redirect to login page
    exit; // Ensure no further code is executed after redirection
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get the selected city from the query parameter
$selected_city = isset($_GET['city']) ? strtolower($_GET['city']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtSell - Explore Cebu's Treasures</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/categories.css">
    <style>
    .profile-link {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .profile-pic {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Hero Banner Styles */
    .hero-banner {
        background: linear-gradient(135deg, #5048E5 0%, #8C5CFF 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 50px;
        text-align: center;
    }

    .hero-banner h1 {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 15px;
        line-height: 1.2;
    }

    .hero-banner p {
        max-width: 600px;
        margin: 0 auto;
        font-size: 16px;
        opacity: 0.9;
    }

    /* Main Content Layout */
    .main-content {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 30px;
        margin-bottom: 50px;
    }

    /* Filters Sidebar */
    .filters {
        background-color: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        height: fit-content;
    }

    .filters-header {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-section {
        margin-bottom: 25px;
    }

    .filter-section h3 {
        font-size: 16px;
        margin-bottom: 15px;
        font-weight: 500;
    }

    .dropdown select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        background-color: #f8f9fa;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: calc(100% - 12px) center;
        padding-right: 35px;
    }

    .dropdown select:focus {
        outline: none;
        border-color: #5048E5;
        box-shadow: 0 0 0 3px rgba(80, 72, 229, 0.1);
    }

    .search-box {
        margin-top: 20px;
    }

    .search-box h3 {
        font-size: 16px;
        margin-bottom: 15px;
        font-weight: 500;
    }

    .search-box input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
    }

    .search-box input:focus {
        outline: none;
        border-color: #5048E5;
        box-shadow: 0 0 0 3px rgba(80, 72, 229, 0.1);
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
    }

    .product-card {
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .product-image {
        height: 200px;
        overflow: hidden;
        cursor: pointer;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.08);
    }

    .product-details {
        padding: 20px;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #111;
    }

    .product-description {
        color: #666;
        margin-bottom: 20px;
        font-size: 14px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .add-to-cart {
        display: block;
        width: 100%;
        padding: 12px;
        background-color: #5048E5;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .add-to-cart:hover {
        background-color: #3c34c2;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(80, 72, 229, 0.25);
    }

    .products-count {
        margin-top: 30px;
        text-align: center;
        color: #666;
        font-size: 14px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 2100;
    }

    .modal-content {
        max-width: 80%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 8px;
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .main-content {
            grid-template-columns: 1fr;
        }

        .filters {
            margin-bottom: 30px;
        }
    }

    @media (max-width: 768px) {
        .hero-banner {
            padding: 60px 0 40px;
        }

        .hero-banner h1 {
            font-size: 28px;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .hero-banner h1 {
            font-size: 24px;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Footer Styles */
    footer {
        background-color: #1A1A2E;
        color: white;
        padding: 80px 0 20px;
        margin-top: 60px;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        margin-bottom: 40px;
    }

    .footer-column h3 {
        font-size: 18px;
        margin-bottom: 20px;
        font-weight: 600;
        color: white;
    }

    .footer-logo {
        color: #5048E5;
        font-size: 24px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 15px;
    }

    .footer-description {
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-links a {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background-color: #5048E5;
        transform: translateY(-3px);
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
        color: rgba(255, 255, 255, 0.7);
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s;
    }

    .footer-links a:hover {
        color: white;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 30px;
        }
    }

    @media (max-width: 576px) {
        footer {
            padding: 50px 0 20px;
        }

        .footer-content {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }
</style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container header-inner">
            <a href="index.php" class="logo">Art<span>iSell</span></a>
            <nav>
                <div class="nav-links">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="categories.php" class="nav-link active">Categories</a>
                    <a href="shop.php" class="nav-link">Shop</a>
                    <a href="about.php" class="nav-link">About</a>
                </div>
            </nav>
            <div class="header-right">
                <a href="#"><i class="search-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                </svg></i></a>
                <a href="cart.php"><i class="cart-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg></i><?php echo isset($_SESSION['cart']) ? " (" . count($_SESSION['cart']) . ")" : ""; ?></a>
                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <div class="profile-dropdown">
                        <a href="profile.php" class="nav-link profile-link">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <?php if (!empty($_SESSION['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" class="profile-pic">
                            <?php else: ?>
                                <img src="images/default-profile.jpg" alt="Profile" class="profile-pic">
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-content">
                            <a href="settings.php" class="dropdown-item">Settings</a>
                            <a href="logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <h1>DISCOVER CEBU'S UNIQUE CRAFTS</h1>
            <p>Explore authentic crafts and delicacies from talented artisans across Cebu</p>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container">
        <main class="main-content">
            <!-- Filters Sidebar -->
            <aside class="filters">
                <div class="filters-header">
                    <i>📊</i> Filter Categories
                </div>
                <div class="filter-section">
                    <h3>City</h3>
                    <div class="dropdown">
                        <select id="cityFilter" onchange="filterByCity(this.value)">
                            <option value="">All Cities</option>
                            <option value="aloquinsan">Aloquinsan</option>
                            <option value="catmon">Catmon</option>
                            <option value="dumanjug">Dumanjug</option>
                            <option value="santander">Santander</option>
                            <option value="alcoy">Alcoy</option>
                            <option value="minglanilla">Minglanilla</option>
                            <option value="alcantara">Alcantara</option>
                            <option value="moalboal">Moalboal</option>
                            <option value="borbon">Borbon</option>
                            <option value="cebu">Cebu</option>
                        </select>
                    </div>
                </div>
                <div class="search-box">
                    <h3>Search</h3>
                    <form action="categories.php" method="GET">
                        <input type="text" name="search" placeholder="Search categories...">
                    </form>
                </div>
            </aside>

            <!-- Categories Grid -->
            <div class="products-grid">
                <?php
                $categories = [
                    ['name' => 'ALOGUINSAN', 'image' => 'image/ALOGUINSAN.jpg', 'desc' => 'Explore our collection of unique crafts from Aloquinsan. These items showcase the rich cultural heritage of the area.', 'city' => 'aloquinsan'],
                    ['name' => 'CATMON', 'image' => 'image/CATMON.jpg', 'desc' => 'Discover the flavors and crafts of Catmon! From the famous Budbud Kabog to handcrafted baskets and bamboo crafts, explore the town's rich culture and artistry.', 'city' => 'catmon'],
                    ['name' => 'DUMANJUG', 'image' => 'image/DUMANJUG.jpg', 'desc' => 'Experience the heritage of Dumanjug! Known for its delicious "Torta sa Dumanjug" and beautifully handcrafted native products, this town showcases the best of tradition and craftsmanship.', 'city' => 'dumanjug'],
                    ['name' => 'SANTANDER', 'image' => 'image/mingla.png', 'desc' => 'Discover the charm of Santander! From fresh seafood and local delicacies to beautifully crafted handmade souvenirs, experience the rich culture of this scenic coastal town.', 'city' => 'santander'],
                    ['name' => 'ALCOY', 'image' => 'image/mingla.png', 'desc' => 'Explore the beauty of Alcoy! Famous for its Tingko Beach, coconut-based delicacies, and handcrafted souvenirs, this coastal town offers a perfect mix of nature and culture.', 'city' => 'alcoy'],
                    ['name' => 'MINGLANILLA', 'image' => 'image/mingla.png', 'desc' => 'Explore our collection of unique crafts from Minglanilla. These items showcase the rich cultural heritage of the area.', 'city' => 'minglanilla'],
                    ['name' => 'ALCANTARA', 'image' => 'image/mingla.png', 'desc' => 'Discover Alcantara's local treasures! From fresh seafood to traditional handicrafts, this town takes pride in its rich heritage and artisanal craftsmanship.', 'city' => 'alcantara'],
                    ['name' => 'MOALBOAL', 'image' => 'image/mingla.png', 'desc' => 'Beyond its stunning beaches, Moalboal offers fresh seafood, locally made shell crafts, and native souvenirs, reflecting its deep connection to nature and culture.', 'city' => 'moalboal'],
                    ['name' => 'BORBON', 'image' => 'image/mingla.png', 'desc' => 'Discover the hidden gem of Borbon! Famous for its Takyong (land snails) delicacy, fresh produce, and locally crafted woven products, this town is a haven for traditional flavors and artisanal craftsmanship.', 'city' => 'borbon'],
                    ['name' => 'CEBU', 'image' => 'image/mingla.png', 'desc' => 'Discover the heart of Cebu! From urban crafts to traditional delicacies, Cebu City offers a blend of modernity and heritage.', 'city' => 'cebu'],
                ];

                foreach ($categories as $category) {
                    $display = ($selected_city === '' || $selected_city === $category['city']) ? 'block' : 'none';
                    echo "
                    <div class='product-card' data-city='{$category['city']}' style='display: {$display};'>
                        <div class='product-image' onclick=\"openModal('{$category['image']}')\">
                            <img src='{$category['image']}' alt='{$category['name']}'>
                        </div>
                        <div class='product-details'>
                            <h3 class='product-name'>{$category['name']}</h3>
                            <p class='product-description'>{$category['desc']}</p>
                            <a href='shop.php?city={$category['city']}' class='add-to-cart'>Shop Now</a>
                        </div>
                    </div>";
                }
                ?>
            </div>
        </main>

        <div class="products-count" id="productsCount">
            Showing <?php echo count(array_filter($categories, function($c) use ($selected_city) { return $selected_city === '' || $selected_city === $c['city']; })); ?> of <?php echo count($categories); ?> categories
        </div>
    </div>

    <!-- Modal -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">×</span>
        <img class="modal-content" id="modalImage">
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <a href="index.php" class="footer-logo">ArtSell</a>
                    <p class="footer-description">
                        Connecting you with Cebu's authentic native crafts and delicacies. Supporting local artisans and preserving cultural heritage.
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg></a>
                        <a href="#" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg></a>
                        <a href="#" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/></svg></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li>123 Creative Blvd, Cebu City, Philippines 6000</li>
                        <li>+63 (32) 123-4567</li>
                        <li>support@artsell.com</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 ArtSell. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Modal functionality
        function openModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = imageSrc;
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
        }

        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Filter product cards by city
        function filterByCity(city) {
            const cards = document.querySelectorAll('.product-card');
            const countElement = document.getElementById('productsCount');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCity = card.getAttribute('data-city');
                if (city === '' || cardCity === city) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            countElement.textContent = `Showing ${visibleCount} of ${cards.length} categories`;
            window.history.pushState({}, document.title, city ? `?city=${city}` : window.location.pathname);
        }

        // Set initial filter based on URL parameter
        document.addEventListener('DOMContentLoaded', () => {
            const urlCity = '<?php echo $selected_city; ?>';
            if (urlCity) {
                document.getElementById('cityFilter').value = urlCity;
                filterByCity(urlCity);
            }
        });
    </script>
</body>
</html>