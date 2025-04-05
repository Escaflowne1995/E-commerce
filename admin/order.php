<?php
session_start();
require_once '../db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php"); // Redirect to login page if not logged in or not an admin
    exit;
}

// Handle database connection error
if (!$conn) {
    $error_message = $error_message ?? "Database connection failed. Please try again later.";
}

// Initialize variables
$success_message = '';
$error_message = $error_message ?? '';

// Handle status update if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $newStatus = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($orderId && in_array($newStatus, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        // Capitalize first letter of status for display
        $displayStatus = ucfirst($newStatus);

        try {
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $displayStatus, $orderId);

            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Order status updated successfully!";
            } else {
                $error_message = "Error updating order status: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Invalid input data";
    }
}

// Initialize arrays for orders and stats
$orders = [];
$total_orders = 0;
$pending_orders = 0;
$processing_orders = 0;
$shipped_orders = 0;
$delivered_orders = 0;
$cancelled_orders = 0;

// Only proceed with database operations if connection is valid
if ($conn) {
    // Get filter values
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Build query with possible filters
    $sqlQuery = "SELECT o.*, u.username, u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE 1=1";
    $queryParams = [];

    if (!empty($status)) {
        $sqlQuery .= " AND o.status = ?";
        $queryParams[] = $status;
    }

    if (!empty($search)) {
        $sqlQuery .= " AND (u.username LIKE ? OR o.shipping_name LIKE ? OR o.id LIKE ?)";
        $searchParam = "%{$search}%";
        $queryParams[] = $searchParam;
        $queryParams[] = $searchParam;
        $queryParams[] = $searchParam;
    }

    $sqlQuery .= " ORDER BY o.created_at DESC";

    // Prepare and execute statement
    try {
        $stmt = mysqli_prepare($conn, $sqlQuery);

        if ($stmt) {
            if (!empty($queryParams)) {
                $types = str_repeat("s", count($queryParams));
                mysqli_stmt_bind_param($stmt, $types, ...$queryParams);
            }

            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $orders[] = $row;
                    }
                    mysqli_free_result($result);
                }
            }
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        $error_message = "Error fetching orders: " . $e->getMessage();
    }

    // Get order statistics
    try {
        $statsQuery = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $statsResult = mysqli_query($conn, $statsQuery);
        if ($statsResult) {
            while ($row = mysqli_fetch_assoc($statsResult)) {
                $status = strtolower($row['status']);
                $count = $row['count'];
                $total_orders += $count;

                if ($status === 'pending') {
                    $pending_orders = $count;
                } elseif ($status === 'processing') {
                    $processing_orders = $count;
                } elseif ($status === 'shipped') {
                    $shipped_orders = $count;
                } elseif ($status === 'delivered') {
                    $delivered_orders = $count;
                } elseif ($status === 'cancelled') {
                    $cancelled_orders = $count;
                }
            }
            mysqli_free_result($statsResult);
        }
    } catch (Exception $e) {
        // If stats fail, don't show an error, just display zeros
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders Management - Admin Dashboard</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/orders.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">
        <h2>ARTISELL</h2>
      </div>
      <div class="admin-profile">
        <div class="profile-image">
          <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-details">
          <span class="profile-label">admin profile</span>
          <span class="profile-name"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'defaultadmin'; ?></span>
        </div>
      </div>
      <ul class="nav">
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="user.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="order.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="product.php"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="category.php"><i class="fas fa-list"></i> Categories</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>

    <div class="main">
      <div class="header">
        <div class="toggle-menu">
          <i class="fas fa-bars"></i>
        </div>
        <div class="header-content">
          <div class="profile">
            <?php if (isset($_SESSION['user_name'])): ?>
              <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <?php endif; ?>
            <a href="../index.php" class="home-link"><i class="fas fa-globe"></i> View Website</a>
          </div>
        </div>
      </div>

      <div class="main-content">
        <div class="page-header">
          <div class="page-header-content">
            <h1>Orders Management</h1>
            <p>View and manage customer orders</p>
          </div>
          <div class="db-connection-status">
            <?php if ($conn): ?>
              <span class="status-dot status-connected" title="Database Connected"></span>
              <span class="status-text">DB Connected</span>
            <?php else: ?>
              <span class="status-dot status-disconnected" title="Database Disconnected"></span>
              <span class="status-text">DB Disconnected</span>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!$conn): ?>
          <div class="message message-error">
            Error fetching orders: Unknown column 'first_name' in 'field list'
          </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
          <div class="message message-success">
            <?php echo $success_message; ?>
            <button id="applyChangesBtn" class="button button-primary" style="float: right; padding: 5px 10px; margin-top: -5px;">Apply</button>
          </div>
        <?php endif; ?>

        <?php if (!empty($error_message) && $conn): ?>
          <div class="message message-error">
            <?php echo $error_message; ?>
          </div>
        <?php endif; ?>

        <div class="stats-cards">
          <div class="stat-card">
            <div class="stat-title">Total Orders</div>
            <div class="stat-value"><?php echo $total_orders; ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-title">Pending</div>
            <div class="stat-value"><?php echo $pending_orders; ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-title">Processing</div>
            <div class="stat-value"><?php echo $processing_orders; ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-title">Shipped</div>
            <div class="stat-value"><?php echo $shipped_orders; ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-title">Delivered</div>
            <div class="stat-value"><?php echo $delivered_orders; ?></div>
          </div>
          <div class="stat-card">
            <div class="stat-title">Cancelled</div>
            <div class="stat-value"><?php echo $cancelled_orders; ?></div>
          </div>
        </div>

        <form class="search-form" method="GET" action="order.php">
          <div class="form-group search-input">
            <input type="text" name="search" placeholder="Search by order ID, customer name or email" value="<?php echo htmlspecialchars($search ?? ''); ?>">
          </div>
          <div class="form-group filter-select">
            <select name="status">
              <option value="">All Statuses</option>
              <option value="pending" <?php echo isset($status) && $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="processing" <?php echo isset($status) && $status === 'processing' ? 'selected' : ''; ?>>Processing</option>
              <option value="shipped" <?php echo isset($status) && $status === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
              <option value="delivered" <?php echo isset($status) && $status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
              <option value="cancelled" <?php echo isset($status) && $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
          </div>
          <button type="submit" class="button button-primary">Filter</button>
          <a href="order.php" class="button button-secondary">Reset</a>
        </form>

        <div class="data-container">
          <div class="responsive-table">
            <?php if (!$conn): ?>
              <div class="empty-state">
                <h3>Database Connection Issue</h3>
                <p>We're unable to retrieve orders at this time. Please try again later.</p>
                <button id="retryConnectionBtn" class="button button-primary" style="margin-top: 15px;">Retry Connection</button>
              </div>
            <?php elseif (count($orders) > 0): ?>
              <table class="orders-table">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $order): ?>
                    <tr>
                      <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                      <td>
                        <?php echo htmlspecialchars($order['username'] ?? $order['shipping_name'] ?? 'Guest'); ?><br>
                        <span class="text-muted"><?php echo htmlspecialchars($order['user_email'] ?? ''); ?></span>
                      </td>
                      <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                      <td class="price-tag">$<?php echo number_format($order['total'] ?? 0, 2); ?></td>
                      <td>
                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                          <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                      </td>
                      <td>
                        <button onclick="showEditModal(<?php echo $order['id']; ?>, '<?php echo strtolower($order['status']); ?>')" class="action-button edit-button">Update Status</button>
                        <a href="view_order.php?id=<?php echo $order['id']; ?>" class="action-button view-button">View Details</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="empty-state">
                <h3>No orders found</h3>
                <p>There are no orders matching your criteria.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Status Modal -->
  <div id="updateStatusModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Update Order Status</h2>
        <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
      </div>
      <form id="updateStatusForm" method="POST">
        <input type="hidden" id="orderIdInput" name="order_id" value="">
        <div class="form-group">
          <label for="status">New Status:</label>
          <select id="statusInput" name="status" class="form-control">
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="button-container">
          <button type="button" class="button button-secondary" onclick="closeModal()">Cancel</button>
          <button type="submit" class="button button-primary">Update Status</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="loadingOverlay" class="loading-overlay">
    <div class="loading-spinner"></div>
    <p>Processing...</p>
  </div>

  <script>
    // Toggle sidebar
    document.querySelector('.header .toggle-menu').addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('show');
    });

    // Check screen size and add appropriate class to sidebar
    function checkScreenSize() {
      if (window.innerWidth <= 768) {
        document.querySelector('.sidebar').classList.remove('show');
      } else {
        document.querySelector('.sidebar').classList.add('show');
      }
    }

    // Run on page load
    window.addEventListener('load', checkScreenSize);

    // Run when window is resized
    window.addEventListener('resize', checkScreenSize);

    // Modal functionality
    function showEditModal(orderId, currentStatus) {
      document.getElementById('orderIdInput').value = orderId;
      document.getElementById('statusInput').value = currentStatus;
      document.getElementById('updateStatusModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('updateStatusModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
      var modal = document.getElementById('updateStatusModal');
      if (event.target == modal) {
        closeModal();
      }
    }

    // Add apply button functionality
    document.addEventListener('DOMContentLoaded', function() {
      var successMessage = document.querySelector('.message-success');
      var applyBtn = document.getElementById('applyChangesBtn');

      if (successMessage) {
        // Only auto-hide if apply button isn't clicked
        let shouldAutoHide = true;

        if (applyBtn) {
          applyBtn.addEventListener('click', function() {
            shouldAutoHide = false;
            successMessage.style.opacity = '0';
            setTimeout(function() {
              successMessage.style.display = 'none';
              window.location.reload();
            }, 500);
          });
        }

        // Auto-hide after 3 seconds if apply button isn't clicked
        setTimeout(function() {
          if (shouldAutoHide) {
            successMessage.style.opacity = '0';
            setTimeout(function() {
              successMessage.style.display = 'none';
            }, 500);
          }
        }, 3000);
      }

      // Add retry connection button functionality
      var retryBtn = document.getElementById('retryConnectionBtn');
      if (retryBtn) {
        retryBtn.addEventListener('click', function() {
          window.location.reload();
        });
      }
    });

    // Show loading overlay on form submit
    document.getElementById('updateStatusForm').addEventListener('submit', function() {
      document.getElementById('loadingOverlay').style.display = 'flex';
    });
  </script>
</body>
</html>