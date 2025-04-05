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
</style>
</head>
<body>
    <!-- Header -->
    <header>
    <div class="container header-inner">
        <div class="logo">
            <a href="#">Art<span style="color: #333;">Sell</span></a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="shop.php" class="nav-link">Shop</a></li>
                <?php if ($_SESSION["role"] === 'vendor'): ?>
                    <li><a href="add_product.php" class="nav-link">Add Product</a></li>
                <?php else: ?>
                    <li><a href="cart.php" class="nav-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg> (<?php echo count($_SESSION['cart']); ?>)</a></li>
                <?php endif; ?>
                <li class="profile-dropdown">
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
                </li>
            </ul>
        </nav>
    </div>
</header>
    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <h1>DISCOVER THE<br>CEBU CITY<br>UNIQUE CRAFTS</h1>
            <p>Discover authentic crafts and delicacies from talented artisans across Cebu</p>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container">
        <main class="main-content">
            <!-- Filters Sidebar -->
            <aside class="filters">
                <div class="filters-header">
                    <i>📊</i> Filters
                </div>
                <div class="filter-section">
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
                    <form action="shop.php" method="GET">
                        <input type="text" name="search" placeholder="Search categories...">
                    </form>
                </div>
            </aside>

            <!-- Categories Grid -->
            <div class="products-grid">
                <?php
                $categories = [
                    ['name' => 'ALOGUINSAN', 'image' => 'image/ALOGUINSAN.jpg', 'desc' => 'Explore our collection of unique crafts from Aloquinsan. These items showcase the rich cultural heritage of the area.', 'city' => 'aloquinsan'],
                    ['name' => 'CATMON', 'image' => 'image/CATMON.jpg', 'desc' => 'Discover the flavors and crafts of Catmon! From the famous Budbud Kabog to handcrafted baskets and bamboo crafts, explore the town’s rich culture and artistry.', 'city' => 'catmon'],
                    ['name' => 'DUMANJUG', 'image' => 'image/DUMANJUG.jpg', 'desc' => 'Experience the heritage of Dumanjug! Known for its delicious "Torta sa Dumanjug" and beautifully handcrafted native products, this town showcases the best of tradition and craftsmanship.', 'city' => 'dumanjug'],
                    ['name' => 'SANTANDER', 'image' => 'image/mingla.png', 'desc' => 'Discover the charm of Santander! From fresh seafood and local delicacies to beautifully crafted handmade souvenirs, experience the rich culture of this scenic coastal town.', 'city' => 'santander'],
                    ['name' => 'ALCOY', 'image' => 'image/mingla.png', 'desc' => 'Explore the beauty of Alcoy! Famous for its Tingko Beach, coconut-based delicacies, and handcrafted souvenirs, this coastal town offers a perfect mix of nature and culture.', 'city' => 'alcoy'],
                    ['name' => 'MINGLANILLA', 'image' => 'image/mingla.png', 'desc' => 'Explore our collection of unique crafts from Minglanilla. These items showcase the rich cultural heritage of the area.', 'city' => 'minglanilla'],
                    ['name' => 'ALCANTARA', 'image' => 'image/mingla.png', 'desc' => 'Discover Alcantara’s local treasures! From fresh seafood to traditional handicrafts, this town takes pride in its rich heritage and artisanal craftsmanship.', 'city' => 'alcantara'],
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
            Showing <?php echo count(array_filter($categories, fn($c) => $selected_city === '' || $selected_city === $c['city'])); ?> of <?php echo count($categories); ?> categories
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
                    <div class="footer-logo">ArtSell</div>
                    <p class="footer-description">
                        Connecting you with Cebu's authentic native crafts and delicacies. Supporting local artisans and preserving cultural heritage.
                    </p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/steve.pable.3/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg></a>
                        <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg></a>
                        <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/></svg></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Cities</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-title">Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-title">Contact Us</h3>
                    <ul class="contact-info">
                        <li>
                            <i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg></i> Bisag asa rami Blvd, Cebu City, Philippines 6000
                        </li>
                        <li>
                            <i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg></i> +63(9475451054)
                        </li>
                        <li>
                            <i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/></svg></i> info@artsell.ph
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 ArtSell. All rights reserved.</p>
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