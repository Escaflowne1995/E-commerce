<?php
session_start();
require_once '../db_connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php"); // Redirect to login page if not logged in or not an admin
    exit;
}

// Initialize messages
$message = '';
$messageType = '';

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $newRole = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    if ($userId && in_array($newRole, ['customer', 'vendor', 'admin'])) {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $newRole, $userId);

        if (mysqli_stmt_execute($stmt)) {
            $message = "User role updated successfully!";
            $messageType = "success";
        } else {
            $message = "Error updating user role: " . mysqli_error($conn);
            $messageType = "error";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Invalid input data";
        $messageType = "error";
    }
}

// Search functionality
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? trim($_GET['role']) : '';

// Build the query with possible search and filters
$sqlQuery = "SELECT id, username, email, role, created_at FROM users WHERE 1=1";
$queryParams = [];

if (!empty($searchQuery)) {
    $sqlQuery .= " AND (username LIKE ? OR email LIKE ?)";
    $searchParam = "%{$searchQuery}%";
    $queryParams[] = $searchParam;
    $queryParams[] = $searchParam;
}

if (!empty($roleFilter)) {
    $sqlQuery .= " AND role = ?";
    $queryParams[] = $roleFilter;
}

$sqlQuery .= " ORDER BY id DESC";

// Get all users with filters applied
$users = [];

// Debug connection
if (!$conn) {
    $message = "Database connection error: " . mysqli_connect_error();
    $messageType = "error";
} else {
    // Prepare statement with error handling
    $stmt = mysqli_prepare($conn, $sqlQuery);

    if (!$stmt) {
        $message = "Query preparation failed: " . mysqli_error($conn);
        $messageType = "error";
    } else {
        // Bind parameters if they exist
        if (!empty($queryParams)) {
            $types = str_repeat("s", count($queryParams));
            mysqli_stmt_bind_param($stmt, $types, ...$queryParams);
        }

        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $users[] = $row;
                }
                mysqli_free_result($result);
            } else {
                $message = "Error getting result: " . mysqli_stmt_error($stmt);
                $messageType = "error";
            }
        } else {
            $message = "Error executing statement: " . mysqli_stmt_error($stmt);
            $messageType = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

// Count total users
$totalUsers = 0;
$countQuery = "SELECT COUNT(*) as total FROM users";
$countResult = mysqli_query($conn, $countQuery);
if ($countResult) {
    $countData = mysqli_fetch_assoc($countResult);
    $totalUsers = $countData['total'];
    mysqli_free_result($countResult);
}

// Get role counts
$roleCounts = [];
$roleQuery = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$roleResult = mysqli_query($conn, $roleQuery);
if ($roleResult) {
    while ($row = mysqli_fetch_assoc($roleResult)) {
        $roleCounts[$row['role']] = $row['count'];
    }
    mysqli_free_result($roleResult);
} else {
    // Fallback if the query fails
    $message = ($message ? $message . "<br>" : "") . "Error fetching role counts: " . mysqli_error($conn);
    $messageType = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Users - Artisell Dashboard</title>
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #6366f1;
      --primary-hover: #4f46e5;
      --success-color: #10b981;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --radius-sm: 0.25rem;
      --radius: 0.375rem;
      --radius-md: 0.5rem;
      --radius-lg: 0.75rem;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background-color: var(--gray-50);
      color: var(--gray-800);
      line-height: 1.5;
    }

    .message {
      padding: 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-weight: 500;
      font-size: 0.875rem;
      border-left: 4px solid transparent;
    }

    .message-success {
      background-color: rgba(16, 185, 129, 0.1);
      color: #047857;
      border-left-color: var(--success-color);
    }

    .message-error {
      background-color: rgba(239, 68, 68, 0.1);
      color: #b91c1c;
      border-left-color: var(--danger-color);
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
      z-index: 50;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(4px);
    }

    .modal-content {
      background-color: white;
      padding: 2rem;
      border-radius: var(--radius-md);
      max-width: 500px;
      width: 100%;
      position: relative;
      box-shadow: var(--shadow-lg);
      animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .modal-header {
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-header h2 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--gray-900);
    }

    .modal-close {
      background: transparent;
      border: none;
      color: var(--gray-500);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
    }

    .modal-close:hover {
      color: var(--gray-700);
      background-color: var(--gray-100);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      font-size: 0.875rem;
      color: var(--gray-700);
    }

    .form-group select, .form-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid var(--gray-300);
      border-radius: var(--radius);
      font-size: 0.875rem;
      transition: all 0.2s;
      color: var(--gray-800);
      background-color: white;
    }

    .form-group select:focus, .form-group input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .button-container {
      display: flex;
      justify-content: flex-end;
      gap: 0.75rem;
    }

    .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      font-weight: 500;
      font-size: 0.875rem;
      line-height: 1.25rem;
      text-align: center;
      transition: all 0.2s;
      cursor: pointer;
      border: none;
      box-shadow: var(--shadow-sm);
      text-decoration: none;
      height: 2.5rem;
    }

    .button-primary {
      background-color: var(--primary-color);
      color: white;
    }

    .button-primary:hover {
      background-color: var(--primary-hover);
    }

    .button-secondary {
      background-color: white;
      color: var(--gray-700);
      border: 1px solid var(--gray-300);
    }

    .button-secondary:hover {
      background-color: var(--gray-100);
    }

    .search-form {
      display: flex;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      background-color: white;
      padding: 1.25rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
    }

    .search-input {
      flex: 1;
      min-width: 200px;
    }

    .filter-select {
      min-width: 150px;
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .stat-card {
      background-color: white;
      padding: 1.25rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      display: flex;
      flex-direction: column;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .stat-title {
      font-size: 0.875rem;
      margin-bottom: 0.5rem;
      color: var(--gray-500);
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    .stat-value {
      font-size: 1.875rem;
      font-weight: 600;
      color: var(--gray-900);
      line-height: 1;
    }

    .users-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-top: 1rem;
      overflow: hidden;
      border-radius: var(--radius);
    }

    .users-table th,
    .users-table td {
      padding: 1rem;
      text-align: left;
      vertical-align: middle;
    }

    .users-table th {
      background-color: white;
      font-weight: 500;
      color: var(--gray-600);
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.025em;
      border-bottom: 1px solid var(--gray-200);
    }

    .users-table th:first-child {
      border-top-left-radius: var(--radius);
    }

    .users-table th:last-child {
      border-top-right-radius: var(--radius);
    }

    .users-table tbody tr {
      background-color: white;
      border-bottom: 1px solid var(--gray-200);
      transition: background-color 0.2s;
    }

    .users-table tbody tr:last-child td:first-child {
      border-bottom-left-radius: var(--radius);
    }

    .users-table tbody tr:last-child td:last-child {
      border-bottom-right-radius: var(--radius);
    }

    .users-table tbody tr:hover {
      background-color: var(--gray-50);
    }

    .users-table td {
      font-size: 0.875rem;
      color: var(--gray-700);
      border-bottom: 1px solid var(--gray-200);
    }

    .users-table tbody tr:last-child td {
      border-bottom: none;
    }

    .action-button {
      padding: 0.5rem 0.75rem;
      border-radius: var(--radius);
      font-size: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      border: none;
    }

    .edit-button {
      background-color: rgba(99, 102, 241, 0.1);
      color: var(--primary-color);
    }

    .edit-button:hover {
      background-color: rgba(99, 102, 241, 0.2);
    }

    .data-container {
      background-color: white;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
    }

    .responsive-table {
      overflow-x: auto;
    }

    .main-content {
      padding: 1.5rem;
    }

    .page-header {
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .page-header-content h1 {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--gray-900);
      margin: 0 0 0.25rem 0;
    }

    .page-header-content p {
      color: var(--gray-500);
      font-size: 0.875rem;
      margin: 0;
    }

    .role-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 500;
      text-transform: capitalize;
    }

    .role-admin {
      background-color: rgba(99, 102, 241, 0.1);
      color: var(--primary-color);
    }

    .role-vendor {
      background-color: rgba(245, 158, 11, 0.1);
      color: var(--warning-color);
    }

    .role-customer {
      background-color: rgba(16, 185, 129, 0.1);
      color: var(--success-color);
    }

    .empty-state {
      text-align: center;
      padding: 3rem 1.5rem;
      color: var(--gray-500);
    }

    .empty-state p {
      margin-top: 0.5rem;
      font-size: 0.875rem;
    }

    @media (max-width: 768px) {
      .stats-cards {
        grid-template-columns: 1fr;
      }

      .search-form {
        flex-direction: column;
      }

      .page-header {
        flex-direction: column;
        align-items: flex-start;
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
        <div class="profile-role">admin profile</div>
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
        <a href="user.php" class="nav-link active">
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
        <a href="add_product.php" class="nav-link">
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
        <div class="page-header-content">
          <h1>Users Management</h1>
          <p>Manage and monitor your system users</p>
        </div>
        <div class="page-header-actions">
          <a href="../signup.php" class="button button-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
              <path d="M8 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-1zM2 1a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm0 8a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H2zm.854-3.646a.5.5 0 0 1-.708 0l-1-1a.5.5 0 1 1 .708-.708l.646.647 1.646-1.647a.5.5 0 1 1 .708.708l-2 2zm0 8a.5.5 0 0 1-.708 0l-1-1a.5.5 0 0 1 .708-.708l.646.647 1.646-1.647a.5.5 0 0 1 .708.708l-2 2zM7 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-1zm0-5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 8a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
            </svg>
            Add New User
          </a>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="message <?php echo $messageType === 'success' ? 'message-success' : 'message-error'; ?>">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <!-- User Statistics -->
      <div class="stats-cards">
        <div class="stat-card">
          <h3 class="stat-title">Total Users</h3>
          <div class="stat-value"><?php echo $totalUsers; ?></div>
        </div>
        <div class="stat-card">
          <h3 class="stat-title">Admins</h3>
          <div class="stat-value"><?php echo isset($roleCounts['admin']) ? $roleCounts['admin'] : 0; ?></div>
        </div>
        <div class="stat-card">
          <h3 class="stat-title">Vendors</h3>
          <div class="stat-value"><?php echo isset($roleCounts['vendor']) ? $roleCounts['vendor'] : 0; ?></div>
        </div>
        <div class="stat-card">
          <h3 class="stat-title">Customers</h3>
          <div class="stat-value"><?php echo isset($roleCounts['customer']) ? $roleCounts['customer'] : 0; ?></div>
        </div>
      </div>

      <!-- Search and Filters -->
      <form class="search-form" method="GET" action="">
        <div class="form-group search-input" style="margin-bottom: 0;">
          <input type="text" name="search" class="form-control" placeholder="Search by username or email" value="<?php echo htmlspecialchars($searchQuery); ?>">
        </div>

        <div class="form-group filter-select" style="margin-bottom: 0;">
          <select name="role" class="form-control">
            <option value="">All Roles</option>
            <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="vendor" <?php echo $roleFilter === 'vendor' ? 'selected' : ''; ?>>Vendor</option>
            <option value="customer" <?php echo $roleFilter === 'customer' ? 'selected' : ''; ?>>Customer</option>
          </select>
        </div>

        <button type="submit" class="button button-primary">Filter</button>
        <a href="user.php" class="button button-secondary">Reset</a>
      </form>

      <div class="data-container">
        <div class="responsive-table">
          <table class="users-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($users)): ?>
                <tr>
                  <td colspan="6">
                    <div class="empty-state">
                      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-1zM2 1a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm0 8a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H2zm.854-3.646a.5.5 0 0 1-.708 0l-1-1a.5.5 0 1 1 .708-.708l.646.647 1.646-1.647a.5.5 0 1 1 .708.708l-2 2zm0 8a.5.5 0 0 1-.708 0l-1-1a.5.5 0 0 1 .708-.708l.646.647 1.646-1.647a.5.5 0 0 1 .708.708l-2 2zM7 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-1zm0-5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 8a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                      </svg>
                      <p>No users found</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                  <td><?php echo htmlspecialchars($user['id']); ?></td>
                  <td><?php echo htmlspecialchars($user['username']); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td>
                    <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>">
                      <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                    </span>
                  </td>
                  <td><?php echo isset($user['created_at']) && !empty($user['created_at']) ? htmlspecialchars(date('M d, Y', strtotime($user['created_at']))) : 'Unknown'; ?></td>
                  <td>
                    <button class="action-button edit-button" onclick="openRoleModal(<?php echo $user['id']; ?>, '<?php echo $user['role']; ?>')">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                      </svg>
                      Edit Role
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Role Edit Modal -->
  <div class="modal" id="roleModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Change User Role</h2>
        <button type="button" class="modal-close" onclick="closeRoleModal()">×</button>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="user_id" id="roleUserId">
        <div class="form-group">
          <label for="role">Select Role:</label>
          <select name="role" id="roleSelect" class="form-control">
            <option value="customer">Customer</option>
            <option value="vendor">Vendor</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="button-container">
          <button type="button" class="button button-secondary" onclick="closeRoleModal()">Cancel</button>
          <button type="submit" name="update_role" class="button button-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
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

    // Role modal functions
    function openRoleModal(userId, currentRole) {
      document.getElementById('roleModal').style.display = 'flex';
      document.getElementById('roleUserId').value = userId;
      document.getElementById('roleSelect').value = currentRole;
    }

    function closeRoleModal() {
      document.getElementById('roleModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('roleModal');
      if (event.target === modal) {
        closeRoleModal();
      }
    }
  </script>
</body>
</html>