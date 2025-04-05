<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'artisell_db';

// Create connection with error handling
try {
    // Create mysqli connection
    $conn = mysqli_connect($host, $username, $password, $database);

    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }

    // Set character set
    mysqli_set_charset($conn, "utf8");

    // Set error reporting mode
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

} catch (Exception $e) {
    // Log the error (in production, use proper logging)
    error_log("Database Connection Error: " . $e->getMessage());

    // Display user-friendly message
    $error_message = "We're experiencing database connection issues. Please try again later.";

    // Only show detailed error in development environment
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        $error_message .= "<br>Error: " . $e->getMessage();
    }

    // Initialize $conn as null to allow checking if connection exists
    $conn = null;
}
?>


