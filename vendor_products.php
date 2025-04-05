<?php
session_start();
require 'db_connection.php';

// Check if user is logged in and is a vendor
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'vendor') {
    header("location: login.php");
    exit;
}

// Handle delete request
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // Fetch product to ensure it belongs to the vendor and get image path
    $sql = "SELECT image FROM products WHERE id = ? AND vendor_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $product_id, $_SESSION['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        // Delete the image file
        if (file_exists($product['image'])) {
            unlink($product['image']);
        }

        // Delete from database
        $sql = "DELETE FROM products WHERE id = ? AND vendor_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $_SESSION['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch vendor's products
$sql = "SELECT * FROM products WHERE vendor_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - ArtSell</title>
    <style>
        body { 
            background-color: #f9f9f9; 
            color: #333; 
            font-family: 'Open Sans', sans-serif; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 0 20px; 
        }
        header { 
            background: #fff; 
            padding: 15px 0; 
            border-bottom: 1px solid #eee; 
        }
        .header-inner { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .logo a { 
            color: #ff6b00; 
            text-decoration: none; 
            font-size: 20px; 
            font-weight: bold; 
        }
        nav ul { 
            display: flex; 
            list-style: none; 
        }
        nav ul li { 
            margin-left: 25px; 
        }
        nav ul li a { 
            color: #333; 
            text-decoration: none; 
            font-weight: 500; 
        }
        h1 { 
            font-size: 24px; 
            font-weight: 600; 
            margin: 30px 0; 
            color: #333; 
        }
        .products-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); 
            gap: 20px; 
        }
        .product-card { 
            background: #fff; 
            border-radius: 6px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            overflow: hidden; 
            padding: 15px; 
        }
        .product-image img { 
            max-width: 100%; 
            height: 150px; 
            object-fit: cover; 
            border-radius: 4px; 
        }
        .product-details { 
            margin-top: 10px; 
        }
        .product-name { 
            font-weight: bold; 
            font-size: 16px; 
        }
        .product-price { 
            font-weight: 600; 
            color: #ff6b00; 
            margin: 5px 0; 
        }
        .button-container { 
            display: flex; 
            gap: 10px; 
            margin-top: 10px; 
        }
        .edit-btn, .delete-btn { 
            flex: 1; 
            padding: 8px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            text-align: center; 
            font-weight: 500; 
        }
        .edit-btn { 
            background: #3b5998; 
            color: white; 
        }
        .delete-btn { 
            background: #ff0000; 
            color: white; 
        }
        .edit-btn:hover { 
            background: #2a4373; 
        }
        .delete-btn:hover { 
            background: #cc0000; 
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-inner">
            <div class="logo"><a href="#">Art<span style="color: #333;">Sell</span></a></div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="add_product.php">Add Product</a></li>
                    <li><a href="vendor_products.php">My Products</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1>My Products</h1>
        <div class="products-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-details">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-price">₱<?php echo number_format($product['price'], 2); ?></p>
                            <div class="button-container">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">Edit</a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="delete-btn">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven’t added any products yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>