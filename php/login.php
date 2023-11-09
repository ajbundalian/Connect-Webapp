<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/c-icon" href="../image/Connect Favicon (1).svg">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <title>Login - Connect</title>
</head>
<body>
<!-- Login Form-->
    <div class="wrapper">
        <img src="/image/Connect Favicon (2).svg" class="logo" alt="Connect Logo">
        <header>CONNECT</header>
        <form method="POST">
          <div class="field email" alt="Email Address Field">
            <div class="input-area" alt="Email Address Field">
              <input type="email" name="email" placeholder="Email Address" required>
              <i class="icon fas fa-envelope"></i>
              <i class="error error-icon fas fa-exclamation-circle"></i>
            </div>
            <div class="error error-txt">Email can't be blank</div>
          </div>
          <div class="field password" alt="Password Field">
            <div class="input-area" alt="Password Field">
              <input type="password" name="password" placeholder="Password" required>
              <i class="icon fas fa-lock"></i>
              <i class="error error-icon fas fa-exclamation-circle"></i>
            </div>
            <div class="error error-txt">Password can't be blank</div>
          </div>
          <div class="pass-txt"><a href="#">Forgot password?</a></div>
          <input type="submit" value="Login" aria-label="Login Button">
        </form>
        <div class="sign-txt">Not yet member? <a href="../php/singup.php">Signup now</a></div>
        <div class="sign-txt2"><a href="#">Privacy Policy</a></div>
      </div>

      <?php
session_start(); // Start the session at the beginning of the script

// Redirect users based on their status
function redirectToDashboard($status) {
    if ($status == 1) {
        header('Location: applicant-homepage.php'); // Redirect to the applicant dashboard
        exit;
    } elseif ($status == 2) {
        header('Location: employer-homepage.php'); // Redirect to the employer dashboard
        exit;
    } else {
        // Handle any other status or errors
        echo 'Error: Unrecognized user status.';
        exit;
    }
}

// Check if the user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['status'])) {
    redirectToDashboard($_SESSION['status']);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection code here
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
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Retrieve the form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare the SQL statement to fetch the user record by email
        $stmt = $pdo->prepare("SELECT * FROM login WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Fetch the user
        $user = $stmt->fetch();

        // Check if user exists and the password is correct
        if ($user && password_verify($password, $user['password'])) {
            // The login is successful. Set session variables.
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['status'] = $user['status'];

            // Redirect user based on their status
            redirectToDashboard($user['status']);
        } else {
            // The credentials are incorrect.
            echo "<script>alert('Login failed: Invalid email or password.'); window.location.href='login.php';</script>";
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
}
// The rest of the page (your HTML form) will be displayed if the user is not redirected
?>
      <!-- <script src="../js/login.js"></script> -->

</body>
</html>