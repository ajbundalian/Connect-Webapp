<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information</title>
</head>
<style>

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
    width: 80%;
    margin: 50px auto;
}

#msform fieldset {
    background: white;
    border: 0 none;
    border-radius: 0px;
    box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
    padding: 20px 30px;
    box-sizing: border-box;
    width: 100%;
    margin: 0 auto;

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
    display: block;
    width: 100px;
    background: #7200F3;
    font-weight: bold;
    color: white;
    border: 0 none;
    border-radius: 25px;
    cursor: pointer;
    padding: 10px 10px;
    margin: 10px auto;
}

#msform .action-button:hover, #msform .action-button:focus {
    box-shadow: 0 0 0 2px white, 0 0 0 3px #7200F3;
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
<?php

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error
    header("Location: login.php");
    exit;
}

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
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Fetch cities for dropdown
    $cityStmt = $pdo->query("SELECT city_name FROM city"); 
    $cities = $cityStmt->fetchAll();
    // Fetch districts for dropdown
    $districtStmt = $pdo->query("SELECT district_name FROM district");
    $districts = $districtStmt->fetchAll();
    //Fetch Pronoun for dropdown
    $pronounStmt = $pdo->query("SELECT pronoun_name FROM pronoun");
    $pronouns = $pronounStmt->fetchAll();

} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Retrieve and sanitize form data
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $contact_number = htmlspecialchars($_POST['contact_number']);
        $district = htmlspecialchars($_POST['district']);
        $city = htmlspecialchars($_POST['city']);
        $pronouns = htmlspecialchars($_POST['pronouns']);
        $user_id = $_SESSION['user_id']; // Retrieve user_id from session

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO profile (user_id, first_name, last_name, contact_number, district, city, pronouns) VALUES (:user_id, :first_name, :last_name, :contact_number, :district, :city, :pronouns)");

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':district', $district);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':pronouns', $pronouns);

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
        echo "Error: " . $e->getMessage();
    }
}
?>
<!-- Form for additional info -->
<div class="row justify-content-center">
    <div class="col-md-6 col-md-offset-3">
        <form id="msform" method="post">
            <fieldset>
                <h2 class="fs-title">Personal Details</h2>
                <h3 class="fs-subtitle">Tell us something more about you</h3>

                <label for="FirstName">First Name</label>
                <input type="text" name="first_name" class="form-control" id="FirstName" placeholder="First Name">

                <label for="LastName">Last Name</label>
                <input type="text" name="last_name" class="form-control" id="LastName" placeholder="Last Name">

                <label for="ContactNum">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" id="ContactNum" placeholder="09XXXXXXXX">

                <label for="district">District</label>
                    <select id="district" name="district" class="form-control">
                        <option selected>Choose...</option>
                            <?php foreach ($districts as $district): ?>
                                <option value="<?php echo htmlspecialchars($district['district_name']); ?>">
                                <?php echo htmlspecialchars($district['district_name']); ?>
                                </option>
                                <?php endforeach; ?>
                    </select>

                <label for="city">City</label>
                    <select id="city" name="city" class="form-control">
                        <option selected>Choose...</option>
                                <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city['city_name']); ?>">
                                <?php echo htmlspecialchars($city['city_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                <label for="pronouns">Pronouns</label>
                    <select id="pronouns" name="pronouns" class="form-control">
                        <option selected>Choose...</option>
                                    <?php foreach ($pronouns as $pronoun): ?>
                                    <option value="<?php echo htmlspecialchars($pronoun['pronoun_name']); ?>">
                                    <?php echo htmlspecialchars($pronoun['pronoun_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>

                <input type="submit" name="submit" class="submit action-button" value="Submit"/>
            </fieldset>
        </form>
    </div>
</div>
<!-- End of Form -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>