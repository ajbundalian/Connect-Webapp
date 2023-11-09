<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/c-icon" href="../image/Connect Favicon (1).svg">
    <link rel="stylesheet" type="text/css" href="../css/signup.css">
    <title>Signup - Connect</title>
</head>
<body>

<!-- Login Form-->
<div class="container">
    <div class="logo">
        <img class="img-logo" src="../image/Connect Favicon (2).svg" alt="Connect Logo">
    </div>
    <h1>CONNECT</h1>

    <form id="signupForm" method="post">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>

        <div class="user-type">
            <input type="radio" id="applicant" name="userType" value="1" required>
            <label for="applicant">Applicant</label>

            <input type="radio" id="employer" name="userType" value="2" required>
            <label for="employer">Employer</label>
        </div>

        <button type="submit">Sign Up</button>
    </form>

    <div class="links">
        <div class="txt1">Already have an account? <a class="txt1" href="../php/login.php">Login now</a></div>
        <a class="txt2" href="#">Privacy Policy</a>
    </div>
</div>

<!-- PHP CODE -->
<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    // Retrieve the form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $status = $_POST['userType'];
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Get the current date in MM-DD-YYYY format
    $date_created = date("m-d-Y");

    // Database connection details
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

    try {
        // Create a new PDO instance
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO login (email, password, status, date_created) VALUES (:email, :password, :status, :date_created)");

        // Bind the parameters
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':date_created', $date_created);

        // Execute the statement
        if ($stmt->execute()) {

            header('Location: login.php');
            exit; // Prevent the script from running any further
        }
    } catch (\PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
    } 
?>

<script src="/js/signup.js"></script>
    
</body>
</html>