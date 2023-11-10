<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

.msform {
    max-width: 600px; /* Adjust the form width */
    margin: 0 auto; /* Center the form */
    padding: 20px;
    background: #f7f7f7; /* Form background color */
    border-radius: 8px; /* Rounded corners */
}

.msform fieldset {
    border: none;
    margin: 0;
    padding: 0;
}

.msform .form-group {
    margin-bottom: 20px; /* Space between inputs */
}

.msform .form-control {
    border-radius: 4px; /* Rounded corners for inputs */
    border: 1px solid #ced4da; /* Border color */
}

.msform .form-control:focus {
    border-color: #80bdff; /* Border color on focus */
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Shadow for focused inputs */
}

.msform .btn-primary {
    background-color: #007bff; /* Primary button color */
    border-color: #007bff; /* Primary button border color */
    padding: 10px 15px; /* Button padding */
    font-size: 16px; /* Button text size */
    border-radius: 4px; /* Rounded corners for button */
}

.msform .btn-primary:hover {
    background-color: #0056b3; /* Button hover color */
    border-color: #0056b3; /* Button hover border color */
}
</style>

<body>
<?php
session_start();

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
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <form id="msform" method="post">
            <fieldset>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="FirstName">First Name</label>
                    <input type="text" name="first_name" class="form-control" id="FirstName" placeholder="First Name">
                </div>
                <div class="form-group col-md-6">
                    <label for="LastName">Last Name</label>
                    <input type="text" name="last_name" class="form-control" id="LastName" placeholder="Last Name">
                </div>
            </div>
            <div class="form-group">
                <label for="ContactNum">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" id="ContactNum" placeholder="09XXXXXXXX">
            </div> 
            <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="district">District</label>
                        <select id="district" name="district" class="form-control">
                            <option selected>Choose...</option>
                                <?php foreach ($districts as $district): ?>
                                    <option value="<?php echo htmlspecialchars($district['district_name']); ?>">
                                    <?php echo htmlspecialchars($district['district_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="city">City</label>
                        <select id="city" name="city" class="form-control">
                            <option selected>Choose...</option>
                                <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city['city_name']); ?>">
                                <?php echo htmlspecialchars($city['city_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                        <label for="pronouns">Pronouns</label>
                        <select id="pronouns" name="pronouns" class="form-control">
                            <option selected>Choose...</option>
                            <?php foreach ($pronouns as $pronoun): ?>
                                    <option value="<?php echo htmlspecialchars($pronoun['pronoun_name']); ?>">
                                    <?php echo htmlspecialchars($pronoun['pronoun_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            </fieldset>
        </form>
    </div>
</div>
<!-- End of Form -->

</body>
</html>