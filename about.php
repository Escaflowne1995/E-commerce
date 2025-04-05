<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - ArtiSell</title>
    <meta name="description" content="Learn about ArtiSell - Connecting you with authentic Cebuano arts, crafts, and traditional foods" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Inter', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: #fff;
            padding: 15px 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: #5048E5;
            text-decoration: none;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo span {
            color: #333;
            font-weight: 600;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .nav-link {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
            padding: 8px 0;
        }

        .nav-link:hover, .nav-link.active {
            color: #5048E5;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .about-hero {
            background-color: #f8f9fa;
            padding: 80px 0 60px;
            text-align: center;
        }

        .about-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .about-hero p {
            max-width: 800px;
            margin: 0 auto 30px;
            color: #666;
            font-size: 1.1rem;
        }

        .about-section {
            padding: 80px 0;
        }

        .about-section h2 {
            font-size: 2rem;
            margin-bottom: 40px;
            text-align: center;
            color: #333;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .about-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .about-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .about-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .about-card-content {
            padding: 25px;
        }

        .about-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }

        .about-card p {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .team-section {
            background-color: #f8f9fa;
            padding: 80px 0;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-member {
            text-align: center;
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-8px);
        }

        .team-member img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .team-member h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: #333;
        }

        .team-member p {
            color: #666;
            font-size: 0.9rem;
        }

        .mission-section {
            padding: 80px 0;
            background-color: #fff;
        }

        .mission-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 40px;
        }

        .mission-text {
            flex: 1;
            min-width: 300px;
        }

        .mission-text h3 {
            font-size: 1.6rem;
            margin-bottom: 15px;
            color: #333;
        }

        .mission-text p {
            color: #555;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .mission-text ul {
            padding-left: 20px;
            margin-bottom: 20px;
        }

        .mission-text ul li {
            margin-bottom: 8px;
        }

        .mission-image {
            flex: 1;
            min-width: 300px;
        }

        .mission-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .testimonial-section {
            background-color: #f8f9fa;
            padding: 60px 0;
        }

        .stats-section {
            padding: 80px 0;
            background-color: #5048E5;
            color: #fff;
            text-align: center;
        }

        .stats-section h2 {
            margin-bottom: 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .stat-item h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .cta-section {
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        footer {
            background-color: #1a1a1a;
            padding: 60px 0 20px;
            color: #fff;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 25px;
            color: #fff;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 12px;
        }

        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: #5048E5;
        }

        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            margin-bottom: 15px;
            display: inline-block;
        }

        .footer-column p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2rem;
            }

            .mission-content {
                flex-direction: column;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="header">
            <div class="container header-inner">
                <a href="index.php" class="logo">Art<span>iSell</span></a>
                <nav>
                    <div class="nav-links">
                        <a href="index.php" class="nav-link">Home</a>
                        <a href="shop.php" class="nav-link">Products</a>
                        <a href="categories.php" class="nav-link">Categories</a>
                        <a href="about.php" class="nav-link active">About</a>
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
                                    <img src="image/default-profile.jpg" alt="Profile" class="profile-pic">
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

        <main class="main-content">
            <!-- Hero Section -->
            <section class="about-hero">
                <div class="container">
                    <h1>About ArtiSell</h1>
                    <p>Connecting Cebuano artisans with customers who appreciate authentic local crafts and delicacies</p>
                </div>
            </section>

            <!-- Our Story Section -->
            <section class="about-section">
                <div class="container">
                    <h2>Our Story</h2>
                    <div class="mission-content">
                        <div class="mission-text">
                            <p>ArtiSell was founded in 2023 with a vision to preserve and promote the rich cultural heritage of Cebu through its artisanal crafts and traditional delicacies. What began as a small platform for local artists has grown into a thriving marketplace that connects skilled artisans with customers from around the world.</p>
                            <p>Our journey started when we recognized the challenges faced by local craftspeople in reaching wider markets. Despite their exceptional skills and the unique cultural value of their products, many artisans struggled to make sustainable livelihoods from their craft.</p>
                            <p>Today, ArtiSell serves as a bridge between tradition and commerce, ensuring that the authentic arts and crafts of Cebu reach appreciative customers while providing artisans with fair compensation for their work.</p>
                        </div>
                        <div class="mission-image">
                            <img src="image/about-story.jpg" alt="ArtiSell Story" onerror="this.src='https://via.placeholder.com/600x400?text=Our+Story'">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Mission & Values Section -->
            <section class="mission-section">
                <div class="container">
                    <h2>Our Mission & Values</h2>
                    <div class="mission-content">
                        <div class="mission-image">
                            <img src="image/about-mission.jpg" alt="Our Mission" onerror="this.src='https://via.placeholder.com/600x400?text=Our+Mission'">
                        </div>
                        <div class="mission-text">
                            <h3>Mission</h3>
                            <p>To empower Filipino artisans by providing a platform that connects them with a global market, preserves cultural heritage, and ensures fair compensation for their craftsmanship.</p>

                            <h3>Values</h3>
                            <ul>
                                <li><strong>Cultural Preservation:</strong> We are committed to safeguarding and promoting the rich cultural traditions of Cebu.</li>
                                <li><strong>Artisan Empowerment:</strong> We believe in fair trade principles and providing sustainable livelihoods for artisans.</li>
                                <li><strong>Authenticity:</strong> We prioritize genuine, handcrafted products that reflect traditional techniques and stories.</li>
                                <li><strong>Community Development:</strong> We invest in local communities and contribute to their economic growth.</li>
                                <li><strong>Sustainability:</strong> We promote environmentally responsible practices in production and packaging.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- What We Offer Section -->
            <section class="about-section" style="background-color: #f8f9fa;">
                <div class="container">
                    <h2>What We Offer</h2>
                    <div class="about-grid">
                        <div class="about-card">
                            <img src="image/about-crafts.jpg" alt="Handcrafted Arts" onerror="this.src='https://via.placeholder.com/400x200?text=Handcrafted+Arts'">
                            <div class="about-card-content">
                                <h3>Handcrafted Arts</h3>
                                <p>Discover unique handmade creations that showcase traditional Cebuano craftsmanship, from intricate woodcarvings to beautiful woven textiles.</p>
                            </div>
                        </div>

                        <div class="about-card">
                            <img src="image/about-food.jpg" alt="Traditional Delicacies" onerror="this.src='https://via.placeholder.com/400x200?text=Traditional+Delicacies'">
                            <div class="about-card-content">
                                <h3>Traditional Delicacies</h3>
                                <p>Taste authentic Filipino flavors through our selection of local delicacies and food products made with time-honored recipes.</p>
                            </div>
                        </div>

                        <div class="about-card">
                            <img src="image/about-stories.jpg" alt="Artisan Stories" onerror="this.src='https://via.placeholder.com/400x200?text=Artisan+Stories'">
                            <div class="about-card-content">
                                <h3>Artisan Stories</h3>
                                <p>Every product comes with the story of the artisan who created it, connecting you directly to the cultural context and personal journey behind each item.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Statistics Section -->
            <section class="stats-section">
                <div class="container">
                    <h2>Our Impact</h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h3>500+</h3>
                            <p>Artisans Supported</p>
                        </div>
                        <div class="stat-item">
                            <h3>30+</h3>
                            <p>Communities Reached</p>
                        </div>
                        <div class="stat-item">
                            <h3>5,000+</h3>
                            <p>Products Sold</p>
                        </div>
                        <div class="stat-item">
                            <h3>15+</h3>
                            <p>Cebu Cities Represented</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Team Section -->
            <section class="team-section">
                <div class="container">
                    <h2>Meet Our Team</h2>
                    <p style="text-align: center; max-width: 800px; margin: 0 auto 40px;">Our passionate team is dedicated to showcasing the best of Cebuano craftsmanship and connecting artisans with appreciative customers worldwide.</p>

                    <div class="team-grid">
                        <div class="team-member">
                            <img src="image/team-1.jpg" alt="Maria Santos" onerror="this.src='https://via.placeholder.com/180x180?text=Maria'">
                            <h3>Maria Santos</h3>
                            <p>Founder & CEO</p>
                        </div>

                        <div class="team-member">
                            <img src="image/team-2.jpg" alt="Juan Reyes" onerror="this.src='https://via.placeholder.com/180x180?text=Juan'">
                            <h3>Juan Reyes</h3>
                            <p>Artisan Coordinator</p>
                        </div>

                        <div class="team-member">
                            <img src="image/team-3.jpg" alt="Elena Cruz" onerror="this.src='https://via.placeholder.com/180x180?text=Elena'">
                            <h3>Elena Cruz</h3>
                            <p>Marketing Director</p>
                        </div>

                        <div class="team-member">
                            <img src="image/team-4.jpg" alt="Carlos Mendoza" onerror="this.src='https://via.placeholder.com/180x180?text=Carlos'">
                            <h3>Carlos Mendoza</h3>
                            <p>Operations Manager</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="cta-section">
                <div class="container">
                    <h2>Join Us in Supporting Cebuano Artisans</h2>
                    <p style="max-width: 700px; margin: 0 auto 30px;">Discover authentic products, support local communities, and help preserve Filipino cultural traditions.</p>
                    <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                        <a href="shop.php" class="btn btn-primary">Shop Now</a>
                        <a href="signup.php" class="btn btn-secondary">Become a Member</a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-column">
                        <a href="index.php" class="footer-logo">ArtSell</a>
                        <p>Connecting you with Cebu's authentic native crafts and delicacies. Supporting local artisans and preserving cultural heritage.</p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/" aria-label="Facebook"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg></a>
                            <a href="#" aria-label="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/></svg></a>
                            <a href="#" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter" viewBox="0 0 16 16"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/></svg></a>
                        </div>
                    </div>
                    <div class="footer-column">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="categories.php">Categories</a></li>
                            <li><a href="shop.php">Shop</a></li>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Customer Service</h3>
                        <ul>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Shipping & Returns</a></li>
                            <li><a href="#">Terms & Conditions</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Contact Us</h3>
                        <ul>
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
    </div>
</body>
</html>