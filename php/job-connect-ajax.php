<?php
    @session_start();
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        echo "Session user_id is not set";
        exit; // Exit the script or handle this situation appropriately
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $host = 'localhost';
    $db   = 'connect';
    $user = 'root';
    $pass = '12345';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, 
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $job_id = $_POST['job_id'];
    $status = 'pending';
    $date_applied = date('m/d/Y');

    // Insert the connection
    $insert_stmt = $pdo->prepare("INSERT INTO applicant_list (user_id, job_id, status, date_applied) VALUES (?, ?, ?, ?)");
    $insert_stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $insert_stmt->bindValue(2, $job_id, PDO::PARAM_INT);
    $insert_stmt->bindValue(3, $status, PDO::PARAM_STR);
    $insert_stmt->bindValue(4, $date_applied, PDO::PARAM_STR);
    $insert_stmt->execute();

    echo "Connected successfully";
} 
$pdo = null; // Close the database connection
?>