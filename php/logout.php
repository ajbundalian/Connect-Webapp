<?php
session_start(); // Start or resume the session

// Check if the user is logged in (you can adjust this condition based on your session variable)
if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header("Location: login.php");
    exit(); // Ensure script execution stops after redirection
} else {
    // User was not logged in, redirect to the login page anyway (optional)
    header("Location: login.php");
    exit();
}
?>