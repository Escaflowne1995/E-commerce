<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1; // Default quantity set to 1

    // Fetch product details from database
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        // Add to session cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }

        // Add or update in database cart table
        $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $_SESSION['id'], $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['id'], $product_id);
        } else {
            $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $_SESSION['id'], $product_id, $quantity);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect to cart.php
        header("Location: cart.php");
        exit;
    }
}

// Get filter parameters and product listing
$category = isset($_GET['category']) ? $_GET['category'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}
if (!empty($city)) {
    $sql .= " AND city = ?";
    $params[] = $city;
    $types .= "s";
}
if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

// Pagination
$items_per_page = 8;
$total_items = count($products);
$total_pages = ceil($total_items / $items_per_page);
$page = isset($_GET['page']) ? max(1, min($total_pages, (int)$_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;
$paginated_products = array_slice($products, $offset, $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtSell - Cebu Cultural Marketplace</title>
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

        .logo a {
            color: #5048E5;
            text-decoration: none;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo a span {
            color: #333;
            font-weight: 600;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        nav ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: #5048E5;
        }

        .profile-dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border-radius: 8px;
            min-width: 180px;
            z-index: 1000;
            top: 130%;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.3s, transform 0.3s;
            padding: 8px 0;
        }

        .profile-dropdown:hover .dropdown-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .dropdown-content a {
            display: block;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .dropdown-content a:hover {
            background-color: #f5f5f5;
            color: #5048E5;
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            padding: 40px 0;
        }

        /* Filters */
        .filters {
            background-color: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .filters h3 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 12px;
            color: #333;
        }

        .filters select, .filters input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            background-color: #f8f9fa;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: calc(100% - 12px) center;
            padding-right: 35px;
        }

        .filters select:focus, .filters input:focus {
            outline: none;
            border-color: #5048E5;
            box-shadow: 0 0 0 3px rgba(80, 72, 229, 0.1);
        }

        .filters input[type="text"] {
            background-image: none;
            padding-right: 15px;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 200px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
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
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 10px;
            color: #111;
        }

        .product-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-weight: 600;
            font-size: 18px;
            color: #5048E5;
            margin-bottom: 15px;
        }

        .button-container {
            display: flex;
            gap: 10px;
        }

        .add-to-cart, .buy-now {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .add-to-cart {
            background-color: #5048E5;
            color: white;
        }

        .buy-now {
            background-color: transparent;
            border: 2px solid #5048E5;
            color: #5048E5;
        }

        .add-to-cart:hover {
            background-color: #3c34c2;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(80, 72, 229, 0.25);
        }

        .buy-now:hover {
            background-color: rgba(80, 72, 229, 0.1);
            transform: translateY(-3px);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin: 40px 0;
            gap: 8px;
        }

        .pagination a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #f5f5f5;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .pagination a.active {
            background-color: #5048E5;
            color: white;
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

        .modal-content {
            max-width: 80%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
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

        /* Profile link */
        .profile-link {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-pic {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
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
            margin-bottom: 15px;
            display: block;
        }

        .footer-column p {
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

        .newsletter {
            display: flex;
            margin-top: 20px;
        }

        .newsletter input {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px 0 0 8px;
            font-size: 14px;
        }

        .newsletter button {
            padding: 12px 20px;
            background-color: #5048E5;
            color: white;
            border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .newsletter button:hover {
            background-color: #3c34c2;
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
            transition: color 0.3s;
        }

        .footer-column ul li a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .filters {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 768px) {
            nav ul {
                gap: 15px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .header-inner {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .button-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-inner">
            <div class="logo"><a href="index.php">Art<span>iSell</span></a></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="shop.php" class="active">Shop</a></li>
                    <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === 'vendor'): ?>
                        <li><a href="add_product.php">Add Product</a></li>
                        <li><a href="vendor_products.php">My Products</a></li>
                    <?php else: ?>
                        <li><a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
                    <?php endif; ?>
                    <li class="profile-dropdown">
                        <a href="profile.php" class="profile-link">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                            <?php if (!empty($_SESSION['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" class="profile-pic">
                            <?php else: ?>
                                <img src="images/default-profile.jpg" alt="Profile" class="profile-pic">
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-content">
                            <a href="profile.php">Settings</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <main class="main-content">
            <!-- Filters -->
            <aside class="filters">
                <h3>Category</h3>
                <select onchange="location = this.value;">
                    <option value="?">All Categories</option>
                    <option value="?category=crafts" <?php echo $category === 'crafts' ? 'selected' : ''; ?>>Crafts</option>
                    <option value="?category=delicacies" <?php echo $category === 'delicacies' ? 'selected' : ''; ?>>Delicacies</option>
                </select>
                <h3>City</h3>
                <select onchange="location = this.value;">
                    <option value="?">All Cities</option>
                    <?php
                    $cities = ['aloquinsan', 'catmon', 'dumanjug', 'santander', 'alcoy', 'minglanilla', 'alcantara', 'moalboal', 'borbon'];
                    foreach ($cities as $c) {
                        echo "<option value='?city=$c'" . ($city === $c ? ' selected' : '') . ">" . ucfirst($c) . "</option>";
                    }
                    ?>
                </select>
                <h3>Search</h3>
                <form method="GET">
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Search" style="display: none;">
                </form>
            </aside>

            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (!empty($paginated_products)): ?>
                    <?php foreach ($paginated_products as $product): ?>
                        <div class="product-card">
                            <div class="product-image" onclick="openModal('<?php echo htmlspecialchars($product['image']); ?>')">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <div class="product-details">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
                                <div class="button-container">
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                                    </form>
                                    <button class="buy-now">Buy Now</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found<?php echo $search ? " for '$search'" : ($category || $city ? " in this selection" : ""); ?>.</p>
                <?php endif; ?>
            </div>
        </main>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?category=<?php echo $category; ?>&city=<?php echo $city; ?>&search=<?php echo $search; ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
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

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Buy Now alert
        document.querySelectorAll('.buy-now').forEach(btn => {
            btn.addEventListener('click', () => alert('Proceeding to checkout!'));
        });
    </script>
</body>
</html>