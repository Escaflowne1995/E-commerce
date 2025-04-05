<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Sync session cart with database
$sql = "SELECT c.product_id, c.quantity, p.name, p.description, p.price, p.image
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$_SESSION['cart'] = [];
while ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['cart'][$row['product_id']] = [
        'name' => $row['name'],
        'description' => $row['description'],
        'price' => $row['price'],
        'image' => $row['image'],
        'quantity' => $row['quantity']
    ];
}

// Handle cart updates
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $quantity = max(1, (int)$quantity);
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            // Update database
            $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $_SESSION['id'], $product_id);
            mysqli_stmt_execute($stmt);
        }
    }
}

// Handle remove item
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    unset($_SESSION['cart'][$product_id]);
    // Remove from database
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['id'], $product_id);
    mysqli_stmt_execute($stmt);
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $price = isset($item['price']) ? (float)$item['price'] : 0;
    $total += $price * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtSell - Your Cart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Modern cart styles */
        body { background-color: #f8f9fa; color: #333; font-family: 'Poppins', 'Inter', sans-serif; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: #fff; padding: 15px 0; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .header-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo a { color: #5048E5; text-decoration: none; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .logo a span { color: #333; font-weight: 600; }
        nav ul { display: flex; list-style: none; }
        nav ul li { margin-left: 30px; }
        nav ul li a { color: #333; text-decoration: none; font-weight: 500; transition: color 0.3s ease; }
        nav ul li a:hover { color: #5048E5; }
        .profile-dropdown { position: relative; }
        .profile-dropdown:hover .dropdown-content { display: block; opacity: 1; transform: translateY(0); }
        .dropdown-content { position: absolute; right: 0; background: #fff; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); border-radius: 8px; min-width: 180px; padding: 8px 0; display: none; opacity: 0; transform: translateY(-10px); transition: opacity 0.3s, transform 0.3s; }
        .dropdown-content a { display: block; padding: 12px 20px; color: #333; text-decoration: none; font-size: 14px; transition: all 0.2s ease; }
        .dropdown-content a:hover { background: #f5f5f5; color: #5048E5; }
        .cart-container { padding: 100px 0 50px; }
        h1 { font-size: 32px; font-weight: 700; margin-bottom: 30px; color: #111; }
        .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); margin-bottom: 30px; overflow: hidden; }
        .cart-table th, .cart-table td { padding: 20px; text-align: left; border-bottom: 1px solid #eee; }
        .cart-table th { background: #f5f5f5; font-weight: 600; color: #333; }
        .cart-table img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); }
        .cart-table input[type="number"] { width: 70px; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px; font-family: 'Inter', sans-serif; transition: all 0.3s ease; }
        .cart-table input[type="number"]:focus { border-color: #5048E5; box-shadow: 0 0 0 3px rgba(80, 72, 229, 0.1); outline: none; }
        .remove-btn { background: #e53e3e; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; }
        .remove-btn:hover { background: #c53030; transform: translateY(-2px); box-shadow: 0 5px 10px rgba(229, 62, 62, 0.2); }
        .cart-total { text-align: right; font-size: 24px; font-weight: 700; margin: 30px 0; color: #111; }
        .button-container { display: flex; gap: 20px; justify-content: flex-end; margin-top: 30px; }
        .update-cart, .checkout-btn { padding: 14px 30px; border: none; border-radius: 8px; cursor: pointer; text-align: center; font-weight: 600; transition: all 0.3s ease; font-size: 16px; }
        .update-cart { background: #f5f5f5; color: #333; }
        .checkout-btn { background: #5048E5; color: white; }
        .update-cart:hover { background: #e0e0e0; transform: translateY(-2px); }
        .checkout-btn:hover { background: #3c34c2; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(80, 72, 229, 0.25); }
        .empty-cart { text-align: center; padding: 60px; background: #fff; border-radius: 12px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        .empty-cart h2 { font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #111; }
        .empty-cart p { margin-bottom: 30px; color: #666; }
        .empty-cart a { color: #5048E5; text-decoration: none; font-weight: 600; display: inline-block; padding: 12px 25px; background-color: #f0f0ff; border-radius: 8px; transition: all 0.3s ease; }
        .empty-cart a:hover { background-color: #5048E5; color: white; transform: translateY(-3px); box-shadow: 0 8px 15px rgba(80, 72, 229, 0.25); }

        /* Footer styling */
        footer { background-color: #1A1A2E; color: white; padding: 80px 0 20px; }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; margin-bottom: 40px; }
        .footer-column { }
        .footer-column h3 { font-size: 18px; margin-bottom: 20px; font-weight: 600; }
        .footer-logo { color: #5048E5; font-size: 24px; font-weight: 700; text-decoration: none; display: inline-block; margin-bottom: 15px; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: rgba(255, 255, 255, 0.7); text-decoration: none; font-size: 14px; transition: color 0.3s; }
        .footer-links a:hover { color: #fff; }
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.5); font-size: 14px; }

        /* Profile link styling */
        .profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .profile-link:hover {
            opacity: 0.85;
        }
        .profile-pic {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .cart-table { display: block; overflow-x: auto; }
            .button-container { flex-direction: column; align-items: center; }
            .update-cart, .checkout-btn { width: 100%; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-inner">
            <div class="logo"><a href="#">Art<span style="color: #333;">Sell</span></a></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="cart.php"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
</svg>(<?php echo count($_SESSION['cart']); ?>)</a></li>
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

    <!-- Cart Content -->
    <div class="container cart-container">
        <h1>Your Cart</h1>
        <?php if (!empty($_SESSION['cart'])): ?>
            <form method="POST">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo $item['quantity']; ?>" min="1">
                                </td>
                                <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-total">
                    Total: ₱<?php echo number_format($total, 2); ?>
                </div>
                <div class="button-container">
                    <button type="submit" name="update_cart" class="update-cart">Update Cart</button>
                    <a href="checkout.php" class="checkout-btn">Checkout</a>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is empty. <a href="shop.php">Start shopping now!</a></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-logo">ArtSell</div>
                    <p>Discover Cebu's finest crafts and delicacies.</p>
                    <div class="newsletter">
                        <input type="email" placeholder="Subscribe to our newsletter">
                        <button>Sign Up</button>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="?category=crafts">Crafts</a></li>
                        <li><a href="?category=delicacies">Delicacies</a></li>
                        <li><a href="shop.php">All Products</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <p>📍 Cebu City, Philippines</p>
                    <p>✉️ info@artsell.ph</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>