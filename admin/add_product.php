<?php
session_start();
require_once '../db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php"); // Redirect to login page if not logged in or not an admin
    exit;
}

// Initialize feedback message
$message = "";
$messageType = ""; // "success" or "error"

// Define upload directory (relative path)
$target_dir = "../uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and validate form inputs
    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = floatval($_POST["price"] ?? 0);
    $category = trim($_POST["category"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $vendor_id = isset($_POST["vendor_id"]) ? (int)$_POST["vendor_id"] : null;

    if (empty($name) || empty($description) || $price <= 0 || empty($category) || empty($city)) {
        $message = "All fields are required, and price must be greater than 0.";
        $messageType = "error";
    } elseif (!isset($_FILES["image"]) || $_FILES["image"]["error"] == UPLOAD_ERR_NO_FILE) {
        $message = "Please upload an image.";
        $messageType = "error";
    } else {
        // Handle file upload
        $image = uniqid() . '_' . basename($_FILES["image"]["name"]); // Add unique prefix to filename
        $target_file = $target_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $messageType = "error";
        } elseif ($_FILES["image"]["size"] > 5000000) { // 5MB limit
            $message = "Image file is too large. Maximum size is 5MB.";
            $messageType = "error";
        } elseif (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $message = "Error uploading image: " . $_FILES["image"]["error"];
            $messageType = "error";
        } else {
            // Save to database
            $sql = "INSERT INTO products (name, description, price, category, image, city, vendor_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssdsssi", $name, $description, $price, $category, $target_file, $city, $vendor_id);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Product added successfully!";
                    $messageType = "success";
                    // Clear form data
                    $name = $description = $category = $city = "";
                    $price = 0;
                } else {
                    $message = "Error saving product to database: " . mysqli_stmt_error($stmt);
                    $messageType = "error";
                    // Clean up uploaded file if database fails
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = "Database error: Could not prepare statement - " . mysqli_error($conn);
                $messageType = "error";
            }
        }
    }
}

// Get vendors for dropdown
$vendors = [];
$vendorsSql = "SELECT id, username FROM users WHERE role = 'vendor'";
$vendorsResult = mysqli_query($conn, $vendorsSql);
if ($vendorsResult) {
    while ($row = mysqli_fetch_assoc($vendorsResult)) {
        $vendors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product - Artisell Dashboard</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    /* Add Product Page Specific Styles */
    .form-container {
      background-color: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #333;
    }

    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      transition: border-color 0.3s;
    }

    .form-control:focus {
      border-color: #5048E5;
      outline: none;
      box-shadow: 0 0 0 2px rgba(80, 72, 229, 0.1);
    }

    textarea.form-control {
      min-height: 120px;
      resize: vertical;
    }

    .btn {
      display: inline-block;
      padding: 12px 24px;
      background-color: #5048E5;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: #3c34c2;
      transform: translateY(-2px);
      box-shadow: 0 8px 15px rgba(80, 72, 229, 0.25);
    }

    .message {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .message-success {
      background-color: #e6f7e6;
      color: #276927;
      border: 1px solid #c3e6c3;
    }

    .message-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .file-upload {
      position: relative;
      overflow: hidden;
      margin-top: 10px;
      width: 100%;
    }

    .file-upload .btn {
      width: 100%;
      text-align: center;
    }

    .file-upload input[type=file] {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }

    .file-name {
      margin-top: 10px;
      font-size: 14px;
      color: #666;
    }

    @media (max-width: 768px) {
      .form-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <h1 class="logo">ARTISELL</h1>
        <div class="menu-icon" id="close-menu">
          <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.61 0H25.041C25.7332 0 26.3971 0.274981 26.8865 0.764451C27.376 1.25392 27.651 1.91778 27.651 2.61C27.651 3.30221 27.376 3.96608 26.8865 4.45555C26.3971 4.94502 25.7332 5.22 25.041 5.22H2.61C1.91778 5.22 1.25392 4.94502 0.764451 4.45555C0.274981 3.96608 7.77841e-08 3.30221 7.77841e-08 2.61C7.77841e-08 1.91778 0.274981 1.25392 0.764451 0.764451C1.25392 0.274981 1.91778 0 2.61 0ZM2.61 9.39H25.041C25.3838 9.39 25.7231 9.45751 26.0398 9.58867C26.3565 9.71984 26.6442 9.91209 26.8865 10.1545C27.1289 10.3968 27.3212 10.6845 27.4523 11.0012C27.5835 11.3179 27.651 11.6572 27.651 12C27.651 12.3428 27.5835 12.6821 27.4523 12.9988C27.3212 13.3155 27.1289 13.6032 26.8865 13.8455C26.6442 14.0879 26.3565 14.2802 26.0398 14.4113C25.7231 14.5425 25.3838 14.61 25.041 14.61H2.61C2.26725 14.61 1.92786 14.5425 1.6112 14.4113C1.29454 14.2802 1.00681 14.0879 0.764451 13.8455C0.52209 13.6032 0.329839 13.3155 0.198674 12.9988C0.0675097 12.6821 2.27824e-08 12.3428 2.27824e-08 12C2.27824e-08 11.6572 0.0675097 11.3179 0.198674 11.0012C0.329839 10.6845 0.52209 10.3968 0.764451 10.1545C1.00681 9.91209 1.29454 9.71984 1.6112 9.58867C1.92786 9.45751 2.26725 9.39 2.61 9.39ZM2.61 18.781H25.041C25.3838 18.781 25.7231 18.8485 26.0398 18.9797C26.3565 19.1108 26.6442 19.3031 26.8865 19.5455C27.1289 19.7878 27.3212 20.0755 27.4523 20.3922C27.5835 20.7089 27.651 21.0483 27.651 21.391C27.651 21.7338 27.5835 22.0731 27.4523 22.3898C27.3212 22.7065 27.1289 22.9942 26.8865 23.2365C26.6442 23.4789 26.3565 23.6712 26.0398 23.8023C25.7231 23.9335 25.3838 24.001 25.041 24.001H2.61C2.26725 24.001 1.92786 23.9335 1.6112 23.8023C1.29454 23.6712 1.00681 23.4789 0.764451 23.2365C0.52209 22.9942 0.329839 22.7065 0.198674 22.3898C0.0675096 22.0731 0 21.7338 0 21.391C0 21.0483 0.0675096 20.7089 0.198674 20.3922C0.329839 20.0755 0.52209 19.7878 0.764451 19.5455C1.00681 19.3031 1.29454 19.1108 1.6112 18.9797C1.92786 18.8485 2.26725 18.781 2.61 18.781Z" fill="currentColor"/>
          </svg>
        </div>
      </div>

      <div class="profile">
        <div class="profile-image"></div>
        <div class="profile-role">Admin Profile</div>
        <div class="profile-name"><?php echo htmlspecialchars($_SESSION["username"]); ?></div>
      </div>

      <nav class="navigation">
        <a href="index.php" class="nav-link">
          <div class="nav-icon">
            <svg width="26" height="30" viewBox="0 0 26 30" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0 28.05V11.1011C0 10.6019 0.106476 10.1293 0.319428 9.68346C0.532381 9.23757 0.825809 8.87033 1.19971 8.58174L11.2004 0.629835C11.7241 0.209946 12.3221 0 12.9944 0C13.6667 0 14.2684 0.209946 14.7996 0.629835L24.8003 8.57979C25.1754 8.86838 25.4689 9.23627 25.6806 9.68346C25.8935 10.1293 26 10.6019 26 11.1011V28.05C26 28.5726 25.8149 29.0283 25.4447 29.417C25.0745 29.8057 24.6406 30 24.1429 30H17.8583C17.4324 30 17.0758 29.8492 16.7886 29.5476C16.5013 29.2447 16.3577 28.8703 16.3577 28.4244V19.1251C16.3577 18.6792 16.2141 18.3055 15.9269 18.0039C15.6384 17.701 15.2818 17.5496 14.8571 17.5496H11.1429C10.7182 17.5496 10.3622 17.701 10.075 18.0039C9.78652 18.3055 9.64229 18.6792 9.64229 19.1251V28.4264C9.64229 28.8723 9.49867 29.246 9.21143 29.5476C8.92419 29.8492 8.56824 30 8.14357 30H1.85714C1.35943 30 0.925476 29.8057 0.555285 29.417C0.185095 29.0283 0 28.5726 0 28.05Z" fill="currentColor"/>
            </svg>
          </div>
          <span class="nav-text">Home</span>
        </a>
        <a href="user.php" class="nav-link">
          <div class="nav-icon">
            <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22.9998 7.66666C25.0332 7.66666 26.9832 8.47439 28.421 9.91217C29.8588 11.3499 30.6665 13.3 30.6665 15.3333C30.6665 17.3666 29.8588 19.3167 28.421 20.7545C26.9832 22.1923 25.0332 23 22.9998 23C20.9665 23 19.0165 22.1923 17.5787 20.7545C16.1409 19.3167 15.3332 17.3666 15.3332 15.3333C15.3332 13.3 16.1409 11.3499 17.5787 9.91217C19.0165 8.47439 20.9665 7.66666 22.9998 7.66666ZM22.9998 26.8333C31.4715 26.8333 38.3332 30.2642 38.3332 34.5V38.3333H7.6665V34.5C7.6665 30.2642 14.5282 26.8333 22.9998 26.8333Z" fill="currentColor"/>
            </svg>
          </div>
          <span class="nav-text">Users</span>
        </a>
        <a href="order.php" class="nav-link">
          <div class="nav-icon">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.6665 33.3333V11.8333L3.4165 4.75001L6.4165 3.33334L10.3332 11.75H29.6665L33.5832 3.33334L36.5832 4.75001L33.3332 11.8333V33.3333H6.6665ZM16.6665 21.6667H23.3332C23.8054 21.6667 24.2015 21.5067 24.5215 21.1867C24.8415 20.8667 25.0009 20.4711 24.9998 20C24.9987 19.5289 24.8387 19.1333 24.5198 18.8133C24.2009 18.4933 23.8054 18.3333 23.3332 18.3333H16.6665C16.1943 18.3333 15.7987 18.4933 15.4798 18.8133C15.1609 19.1333 15.0009 19.5289 14.9998 20C14.9987 20.4711 15.1587 20.8672 15.4798 21.1883C15.8009 21.5095 16.1965 21.6689 16.6665 21.6667Z" fill="currentColor"/>
            </svg>
          </div>
          <span class="nav-text">Orders</span>
        </a>
        <a href="add_product.php" class="nav-link active">
          <div class="nav-icon">
            <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M33.9168 10.7916L18.5002 3.08331L3.0835 10.7916V26.2083L18.5002 33.9166L33.9168 26.2083V10.7916Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <path d="M3.0835 10.7916L18.5002 18.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M18.5 33.9167V18.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M33.9167 10.7916L18.5 18.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M26.2087 6.9375L10.792 14.6458" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <span class="nav-text">Add Product</span>
        </a>
        <a href="logout.php" class="nav-link">
          <div class="nav-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 5H11C11.55 5 12 4.55 12 4C12 3.45 11.55 3 11 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H11C11.55 21 12 20.55 12 20C12 19.45 11.55 19 11 19H5V5Z" fill="currentColor"/>
              <path d="M20.65 11.65L17.86 8.86C17.54 8.54 17 8.76 17 9.21V11H10C9.45 11 9 11.45 9 12C9 12.55 9.45 13 10 13H17V14.79C17 15.24 17.54 15.46 17.85 15.14L20.64 12.35C20.84 12.16 20.84 11.84 20.65 11.65Z" fill="currentColor"/>
            </svg>
          </div>
          <span class="nav-text">Logout</span>
        </a>
      </nav>
    </aside>

    <div class="menu-icon" id="open-menu">
      <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2.61 0H25.041C25.7332 0 26.3971 0.274981 26.8865 0.764451C27.376 1.25392 27.651 1.91778 27.651 2.61C27.651 3.30221 27.376 3.96608 26.8865 4.45555C26.3971 4.94502 25.7332 5.22 25.041 5.22H2.61C1.91778 5.22 1.25392 4.94502 0.764451 4.45555C0.274981 3.96608 7.77841e-08 3.30221 7.77841e-08 2.61C7.77841e-08 1.91778 0.274981 1.25392 0.764451 0.764451C1.25392 0.274981 1.91778 0 2.61 0ZM2.61 9.39H25.041C25.3838 9.39 25.7231 9.45751 26.0398 9.58867C26.3565 9.71984 26.6442 9.91209 26.8865 10.1545C27.1289 10.3968 27.3212 10.6845 27.4523 11.0012C27.5835 11.3179 27.651 11.6572 27.651 12C27.651 12.3428 27.5835 12.6821 27.4523 12.9988C27.3212 13.3155 27.1289 13.6032 26.8865 13.8455C26.6442 14.0879 26.3565 14.2802 26.0398 14.4113C25.7231 14.5425 25.3838 14.61 25.041 14.61H2.61C2.26725 14.61 1.92786 14.5425 1.6112 14.4113C1.29454 14.2802 1.00681 14.0879 0.764451 13.8455C0.52209 13.6032 0.329839 13.3155 0.198674 12.9988C0.0675097 12.6821 2.27824e-08 12.3428 2.27824e-08 12C2.27824e-08 11.6572 0.0675097 11.3179 0.198674 11.0012C0.329839 10.6845 0.52209 10.3968 0.764451 10.1545C1.00681 9.91209 1.29454 9.71984 1.6112 9.58867C1.92786 9.45751 2.26725 9.39 2.61 9.39ZM2.61 18.781H25.041C25.3838 18.781 25.7231 18.8485 26.0398 18.9797C26.3565 19.1108 26.6442 19.3031 26.8865 19.5455C27.1289 19.7878 27.3212 20.0755 27.4523 20.3922C27.5835 20.7089 27.651 21.0483 27.651 21.391C27.651 21.7338 27.5835 22.0731 27.4523 22.3898C27.3212 22.7065 27.1289 22.9942 26.8865 23.2365C26.6442 23.4789 26.3565 23.6712 26.0398 23.8023C25.7231 23.9335 25.3838 24.001 25.041 24.001H2.61C2.26725 24.001 1.92786 23.9335 1.6112 23.8023C1.29454 23.6712 1.00681 23.4789 0.764451 23.2365C0.52209 22.9942 0.329839 22.7065 0.198674 22.3898C0.0675096 22.0731 0 21.7338 0 21.391C0 21.0483 0.0675096 20.7089 0.198674 20.3922C0.329839 20.0755 0.52209 19.7878 0.764451 19.5455C1.00681 19.3031 1.29454 19.1108 1.6112 18.9797C1.92786 18.8485 2.26725 18.781 2.61 18.781Z" fill="currentColor"/>
      </svg>
    </div>

    <main class="main-content">
      <div class="page-header">
        <h1>Add New Product</h1>
      </div>

      <?php if (!empty($message)): ?>
        <div class="message message-<?php echo $messageType; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label for="price">Price (₱)</label>
            <input type="number" id="price" name="price" class="form-control" min="0.01" step="0.01" value="<?php echo htmlspecialchars($price ?? ''); ?>" required>
          </div>

          <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control" required>
              <option value="" disabled selected>Select a category</option>
              <option value="Artwork" <?php echo (isset($category) && $category == 'Artwork') ? 'selected' : ''; ?>>Artwork</option>
              <option value="Handcraft" <?php echo (isset($category) && $category == 'Handcraft') ? 'selected' : ''; ?>>Handcraft</option>
              <option value="Jewelry" <?php echo (isset($category) && $category == 'Jewelry') ? 'selected' : ''; ?>>Jewelry</option>
              <option value="Clothing" <?php echo (isset($category) && $category == 'Clothing') ? 'selected' : ''; ?>>Clothing</option>
              <option value="Home Decor" <?php echo (isset($category) && $category == 'Home Decor') ? 'selected' : ''; ?>>Home Decor</option>
              <option value="Other" <?php echo (isset($category) && $category == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($city ?? ''); ?>" required>
          </div>

          <?php if (!empty($vendors)): ?>
          <div class="form-group">
            <label for="vendor_id">Vendor</label>
            <select id="vendor_id" name="vendor_id" class="form-control" required>
              <option value="" disabled selected>Select a vendor</option>
              <?php foreach ($vendors as $vendor): ?>
                <option value="<?php echo $vendor['id']; ?>" <?php echo (isset($vendor_id) && $vendor_id == $vendor['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($vendor['username']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <label for="image">Product Image</label>
            <div class="file-upload">
              <button type="button" class="btn">Select Image</button>
              <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif" required>
            </div>
            <div class="file-name" id="file-name">No file selected</div>
          </div>

          <div class="form-group">
            <button type="submit" class="btn">Add Product</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script>
    // Display selected filename
    document.getElementById('image').addEventListener('change', function() {
      const fileName = this.files[0] ? this.files[0].name : 'No file selected';
      document.getElementById('file-name').textContent = fileName;
    });

    // Mobile menu toggle
    const sidebar = document.getElementById('sidebar');
    const openMenuBtn = document.getElementById('open-menu');
    const closeMenuBtn = document.getElementById('close-menu');

    // Open sidebar when menu button is clicked
    if (openMenuBtn) {
      openMenuBtn.addEventListener('click', function() {
        sidebar.classList.add('active');
      });
    }

    // Close sidebar when close button is clicked
    if (closeMenuBtn) {
      closeMenuBtn.addEventListener('click', function() {
        sidebar.classList.remove('active');
      });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const isClickInsideSidebar = sidebar.contains(event.target);
      const isClickOnOpenMenu = openMenuBtn.contains(event.target);

      if (!isClickInsideSidebar && !isClickOnOpenMenu && window.innerWidth <= 768) {
        sidebar.classList.remove('active');
      }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        sidebar.classList.remove('active');
      }
    });
  </script>
</body>
</html>