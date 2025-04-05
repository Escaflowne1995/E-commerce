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
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Your existing styles remain unchanged */
        body { background-color: #f9f9f9; color: #333; font-family: 'Open Sans', sans-serif; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: #fff; padding: 15px 0; border-bottom: 1px solid #eee; }
        .header-inner { display: flex; justify-content: space-between; align-items: center; }
        .logo a { color: #ff6b00; text-decoration: none; font-size: 20px; font-weight: bold; }
        nav ul { display: flex; list-style: none; }
        nav ul li { margin-left: 25px; }
        nav ul li a { color: #333; text-decoration: none; font-weight: 500; }
        .profile-dropdown { position: relative; }
        .profile-dropdown:hover .dropdown-content { display: block; }
        .dropdown-content { position: absolute; right: 0; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-radius: 4px; min-width: 120px; }
        .dropdown-content a { display: block; padding: 10px 15px; color: #333; text-decoration: none; }
        .dropdown-content a:hover { background: #f5f5f5; }
        .cart-container { padding: 30px 0; }
        h1 { font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #333; }
        .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .cart-table th, .cart-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        .cart-table th { background: #f5f5f5; font-weight: 600; color: #555; }
        .cart-table img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; margin-right: 10px; }
        .cart-table input[type="number"] { width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .remove-btn { background: #ff6b00; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; transition: background 0.3s ease; }
        .remove-btn:hover { background: #e65c00; }
        .cart-total { text-align: right; font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #333; }
        .button-container { display: flex; gap: 10px; justify-content: center; }
        .add-to-cart, .checkout-btn { flex: 1; padding: 10px; border: none; border-radius: 4px; cursor: pointer; text-align: center; max-width: 200px; }
        .add-to-cart { background: #3b5998; color: white; }
        .checkout-btn { background: #ff6b00; color: white; }
        .add-to-cart:hover { background: #2a4373; }
        .checkout-btn:hover { background: #e65c00; }
        .empty-cart { text-align: center; padding: 40px; background: #fff; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .empty-cart a { color: #ff6b00; text-decoration: none; font-weight: 500; }
        .empty-cart a:hover { text-decoration: underline; }
        footer { background: #2c3e50; color: white; padding: 40px 0 20px; }
        .footer-content { display: flex; justify-content: space-between; }
        .footer-column { flex: 1; padding: 0 15px; }
        .footer-logo { color: #ff6b00; font-size: 20px; font-weight: bold; }
        .newsletter input { padding: 8px; width: 70%; border: none; border-radius: 4px 0 0 4px; }
        .newsletter button { padding: 8px; background: #ff6b00; color: white; border: none; border-radius: 0 4px 4px 0; }
        .footer-column h3 { font-size: 16px; margin-bottom: 10px; }
        .footer-column ul { list-style: none; }
        .footer-column ul li { margin-bottom: 8px; }
        .footer-column ul li a { color: white; text-decoration: none; }
        .footer-column ul li a:hover { color: #ff6b00; }

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
                    <button type="submit" name="update_cart" class="add-to-cart">Update Cart</button>
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