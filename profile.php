<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="page-container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="<?php echo $_SESSION['username']; ?>">
        <p>This is your profile page.</p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>