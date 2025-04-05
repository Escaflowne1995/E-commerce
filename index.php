<?php
session_start(); // Start the session to access session variables
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ArtiSell - Cebu Artisan Marketplace</title>
    <meta name="description" content="Discover authentic Cebuano arts, crafts, and traditional foods" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/index.css">
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
  color: white;
  text-decoration: none;
  font-size: 16px;
  background-color: rgba(255,255,255,0.1);
  transition: background-color 0.3s ease;
}

.social-links a:hover {
  background-color: #ff6b00;
}
        </style>
  </head>
  <body>

    <div class="main-container">
      <main class="main-content">

      <header class="header">
    <div class="container header-inner">
        <a href="" class="logo">Art<span>iSell</span></a>
        <nav>
            <div class="nav-links">
                <a href="#" class="nav-link active">Home</a>
                <a href="shop.php" class="nav-link">Products</a>
                <a href="about.php" class="nav-link">Cities</a>
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

        <!-- Hero Section -->
        <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Discover Cebu's Native Crafts & Delicacies</h1>
                <p>Connecting you with authentic local treasures, handcrafted by Filipino artisans. Support local businesses and find the best of Cebu's culture.</p>
                <p>Connecting you with authentic local treasures, handcrafted by Filipino artisans. Support local businesses and find the best of Cebu's culture.</p>
                <a href="#" class="btn btn-primary">Shop Now</a>
                <a href="#" class="btn btn-secondary">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products">
        <div class="container">
            <h2>Featured Products</h2>
            <p>Explore our curated selection of Cebu's finest treasures, each with authentic craftsmanship and cultural significance.</p>

            <div class="products-container">
                <div class="product-card">
                    <div class="product-image">
                        <img src="/api/placeholder/200/200" alt="Coconut Bowl">
                    </div>
                    <div class="product-details">
                        <div class="product-category">Featured</div>
                        <h3 class="product-title">Coconut Bowl</h3>
                        <p class="product-price">₱ 450.00</p>
                        <button class="add-to-cart">Add to Cart</button>
                    </div>
                </div>
                <!-- More product cards would go here -->
            </div>

            <a href="#" class="view-all-btn">View All Products</a>
        </div>
    </section>

    <!-- Explore by City Section -->
    <section class="explore-city">
        <div class="container">
            <h2>Explore by City</h2>
            <p>Find local arts, crafts, and delicacies unique to each city. Discover items from different regions across the Philippines.</p>
            <a href="#" class="view-all-btn">View All Cities</a>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <div class="container" style="display: flex;">
            <div class="about-image">
                <div class="image-placeholder">
                </div>
            </div>
            /
            <div class="about-content">
                <h2>About ArtSell</h2>
                <p>ArtSell is a marketplace dedicated to promoting authentic local arts and connecting artisans with customers who care about heritage through craft and culture.</p>
                <p>Our mission is to empower local artisans and preserve Filipino craft traditions while providing quality products to customers around the globe.</p>
                <p>By supporting ArtSell, you're not just buying products - you're helping preserve traditional craftsmanship and supporting local communities.</p>
                <a href="#" class="btn btn-primary">Learn More About Us</a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2>What Our Customers Say</h2>
            <div class="testimonials-container">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"The quality of the products is exceptional! I love how each item comes with a story about the artisan who made it."</p>
                    <div class="testimonial-name">Maria Garcia</div>
                    <div class="testimonial-location">Manila, PH</div>
                </div>
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"I ordered several items as gifts for my family, and they all arrived beautifully packaged. Will definitely order again!"</p>
                    <div class="testimonial-name">John Santos</div>
                    <div class="testimonial-location">Cebu, PH</div>
                </div>
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p>"The coconut bowls are gorgeous! Supporting local artisans while getting beautiful, sustainable products feels great."</p>
                    <div class="testimonial-name">Sophie Reyes</div>
                    <div class="testimonial-location">Davao, PH</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Discover Cebu's Treasures?</h2>
            <p>Join ArtSell today and start your journey through Cebu's rich culture of native crafts and delicacies.</p>
            <div class="cta-buttons">
                <a href="#" class="btn btn-primary">Shop Now</a>
                <a href="#" class="btn btn-secondary">Become a Vendor</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <a href="#" class="footer-logo">ArtSell</a>
                    <p>Connecting artisans with customers who appreciate authentic local crafts and delicacies.</p>

                    <div class="social-links">
                        <a href="https://www.facebook.com/steve.pable.3/"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg></a>
                        <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg></a>
                        <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/></svg></a>
                    </div>

                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Cities</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Customer Service</h3>
                    <ul class="footer-links">
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li>123 Main Street, Cebu City, Philippines</li>
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
</body>
</html>