<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" type="image/c-icon" href="../image/Connect Favicon (1).svg">
    <title>Information</title>
</head>
<style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
/*custom font*/
@import url(https://fonts.googleapis.com/css?family=Montserrat);

/*basic reset*/
* {
    margin: 0;
    padding: 0;
}

html {
    height: 100%;
    background: #7200F3; /* fallback for old browsers */
}

body {
    font-family: montserrat, arial, verdana;
    background: transparent;
}

/*form styles*/
#msform {
    text-align: center;
    position: relative;
    margin-top: 30px;
}

#msform fieldset {
    background: white;
    border: 0 none;
    border-radius: 0px;
    box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
    padding: 20px 30px;
    box-sizing: border-box;
    width: 80%;
    margin: 0 10%;

    /*stacking fieldsets above each other*/
    position: relative;
}

/*Hide all except first fieldset*/
#msform fieldset:not(:first-of-type) {
    display: none;
}

/*inputs*/
#msform input, #msform textarea {
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 0px;
    margin-bottom: 10px;
    padding-bottom: 20px;
    width: 100%;
    box-sizing: border-box;
    font-family: montserrat;
    color: #2C3E50;
    font-size: 13px;
}

#msform input:focus, #msform textarea:focus {
    -moz-box-shadow: none !important;
    -webkit-box-shadow: none !important;
    box-shadow: none !important;
    border: 1px solid #ee0979;
    outline-width: 0;
    transition: All 0.5s ease-in;
    -webkit-transition: All 0.5s ease-in;
    -moz-transition: All 0.5s ease-in;
    -o-transition: All 0.5s ease-in;
}

/*buttons*/
#msform .action-button {
    width: 100px;
    background: #ee0979;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px;
}

#msform .action-button:hover, #msform .action-button:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #ee0979;
}

#msform .action-button-previous {
    width: 100px;
    background: #C5C5F1;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 10px 5px;
    margin: 10px 5px;
}

#msform .action-button-previous:hover, #msform .action-button-previous:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #C5C5F1;
}

/*headings*/
.fs-title {
    font-size: 18px;
    text-transform: uppercase;
    color: #2C3E50;
    margin-bottom: 10px;
    letter-spacing: 2px;
    font-weight: bold;
}

.fs-subtitle {
    font-weight: normal;
    font-size: 13px;
    color: #666;
    margin-bottom: 20px;
}
</style>
<body>
<!-- Form for additional info -->
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <form id="msform" method="post">
            <fieldset>
                <h2 class="fs-title">Personal Details</h2>
                <h3 class="fs-subtitle">Tell us something more about you</h3>
                <label for="companyname">Company Name</label>
                <input type="text" id="companyname" name="company_name" placeholder="Company Name"/>
                <label for="website">Company Website</label>
                <input type="text" id= "website" name="company_website" placeholder="Website"/>
                <label for="companydesc">Describe your Company</label>
                <textarea class="form-control" id="companydesc" name="company_desc" rows="3" placeholder="Tell us about your company"></textarea>
                <label for="phone">Phone</label>
                <input type="text" id= "phone" name="company_phone" placeholder="Phone"/>
                <input type="submit" name="submit" class="submit action-button" value="Submit"/>
            </fieldset>
        </form>
    </div>
</div>
<!-- End of Form -->

<?php
// Start the session
session_start();
// Include your database connection file here
// require 'path/to/your/database/connection/file.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the input data
    $company_name = htmlspecialchars($_POST['company_name']);
    $company_desc = htmlspecialchars($_POST['company_desc']);
    $company_website = htmlspecialchars($_POST['company_website']);
    $company_phone = htmlspecialchars($_POST['company_phone']);

    // Get user_id from the session
    $user_id = $_SESSION['user_id'];

    // Database connection details (replace with your actual details)
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

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM company_profile WHERE user_id = :user_id AND company_name IS NOT NULL");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Check if a company name exists
        if ($stmt->fetchColumn() > 0) {
        // User has already filled up the form with company name, redirect them
        header("Location: employer-homepage.php"); // Redirect to a suitable page
        exit;
        }
        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO company_profile (user_id, company_name, company_desc, company_website, company_phone) VALUES (:user_id, :company_name, :company_desc, :company_website, :company_phone)");

        // Bind the parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':company_desc', $company_desc);
        $stmt->bindParam(':company_website', $company_website);
        $stmt->bindParam(':company_phone', $company_phone);

        // Execute the statement
        $stmt->execute();

        if ($stmt) {
            // Destroy the session
            session_unset(); // Remove all session variables
            session_destroy(); // Destroy the session
    
            // Redirect to login page with a success message query parameter
            header("Location: login.php?status=success");
            exit;
        }

    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
}
?>  
</body>
</html>