<?php
@session_start();
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
// Database connection - replace with your actual connection details
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

$job_id = $_POST['job_id']; // job_id sent from AJAX request

// Check if the job is already saved
$stmt = $pdo->prepare("SELECT * FROM saved_jobs WHERE job_id = ? AND user_id = ?");
$stmt->bindValue(1, $job_id, PDO::PARAM_INT);
$stmt->bindValue(2, $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    // Job is already saved, so unsave it
    $delete_stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    $delete_stmt->bindValue(1, $job_id, PDO::PARAM_INT);
    $delete_stmt->bindValue(2, $user_id, PDO::PARAM_INT);
    $delete_stmt->execute();
    echo "Job unsaved successfully";
    header("job-detail.php");
} else {
    // Job is not saved, so save it
    $insert_stmt = $pdo->prepare("INSERT INTO saved_jobs (job_id, user_id) VALUES (?, ?)");
    $insert_stmt->bindValue(1, $job_id, PDO::PARAM_INT);
    $insert_stmt->bindValue(2, $user_id, PDO::PARAM_INT);
    $insert_stmt->execute();
    echo "Job saved successfully";
    header("job-detail.php");
}
$pdo = null; // Close the database connection
}
?>