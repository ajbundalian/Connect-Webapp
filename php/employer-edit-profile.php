<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['status'] !== 2) {
    header('Location: employer-homepage.php'); 
    exit();
}
//Database connection
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

$user_id = $_SESSION['user_id'];



// Query for company profile
$pdo = new PDO($dsn, $user, $pass, $options);
// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // This part handles the AJAX request
    if (!empty($_POST['ajax']) && $_POST['ajax'] == 'true') {
    $company_name = $_POST['company_name'] ?? '';
    $company_desc = $_POST['company_desc'] ?? '';
    $company_website = $_POST['company_website'] ?? '';
    $company_phone = $_POST['company_phone'] ?? '';
    $district = $_POST['district'] ?? '';
    $city = $_POST['city'] ?? '';

    $updateStmt = $pdo->prepare("UPDATE company_profile SET company_name = ?, company_desc = ?, company_website = ?, company_phone = ?, district = ?, city = ? WHERE user_id = ?");
    $updateStmt->execute([$company_name, $company_desc, $company_website, $company_phone, $district, $city, $user_id]);
    header('Content-Type: application/json');
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
    exit(); // Stop further script execution after AJAX response
    // Redirect or handle the response as needed
    }
}

$stmt = $pdo->prepare("SELECT * FROM company_profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

//query for pronouns, city, district
$cityStmt = $pdo->query("SELECT city_name FROM city"); 
$cities = $cityStmt->fetchAll();
// Fetch districts for dropdown
$districtStmt = $pdo->query("SELECT district_name FROM district");
$districts = $districtStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Edit Profile</title>
</head>
<style>
    .profile-header {
    transform: translateY(4rem);
    margin-bottom: 2rem;
    }
    body{
        font-family: 'DM Sans', sans-serif;
    }
</style>
<body>
<!--Nav Starts Here-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark p-2">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../image/Connect Favicon (1).svg" alt="Connect Logo" height="50" width="50">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    
      <div class=" collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ms-auto ">
          <li class="nav-item">
            <a class="nav-link mx-2 active h5" aria-current="page" href="employer-homepage.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-2 h5" href="employer-jobs.php">Jobs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-2 h5" href="employer-applicant.php">Applicants</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link mx-2 dropdown-toggle h5" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Account
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item h5" href="employer-profilepage.php">Profile</a></li>
              <li><a class="dropdown-item h5" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
    </nav>

    <!--Nav Ends Here-->
<div class="row py-5 px-4">
    <div class="col-xl-8 col-md-6 col-sm-10 mx-auto">

        <!-- Profile widget -->
        <div class="bg-white shadow rounded overflow-hidden">
            <div class="px-4 pt-0 pb-4 bg-dark">
                <div class="media align-items-center profile-header">
                    <div class="profile mr-3 mt-3"><img src="../image/Connect Favicon (2).svg" alt="..." height="150" width="150" class="rounded mb-2 img-thumbnail"><a href="#" class="btn btn-dark btn-sm btn-block mb-5">Upload Picture</a></div>
                    <div class="media-body mb-5 text-white">
                        <h4 class="mt-0 mb-0 fs-1"><?php echo htmlspecialchars($userInfo['company_name']); ?></h4>
                        <p class="small mb-4 fs-4"><ion-icon name="location-outline"></ion-icon><?php echo htmlspecialchars($userInfo['district']); ?>, <?php echo htmlspecialchars($userInfo['city']); ?></p>
                    </div>
                </div>
            </div>

            <div class="card col-lg-12 px-1 py-4">
                <div class="card-body">
                <form id="profileform" method="post">
                <h4 class="card-title mb-3 text-center">Edit Profile</h4>
                    <div class="row">
                        <div class="col-12 col-sm-12">
           
            <div class="form-group">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" class="form-control mb-1" value="<?php echo htmlspecialchars($userInfo['company_name']); ?>">
            </div>

            <div class="form-group">
                        <label for="form-label">Company Description</label>
                        <textarea class="form-control" name="company_desc" rows="3" value="<?php echo htmlspecialchars($userInfo['company_desc']); ?>"></textarea>
            </div>

            <div class="form-group">
                        <label class="form-label">Company Website</label>
                        <input type="text" name="company_website" class="form-control mb-1" value="<?php echo htmlspecialchars($userInfo['company_website']); ?>">
            </div>

            <div class="form-group">
                        <label class="form-label">Company Phone</label>
                        <input type="text" name="company_phone" class="form-control mb-1" value="<?php echo htmlspecialchars($userInfo['company_phone']); ?>">
            </div>

            <div class="form-row"> <!-- This is a flex container in Bootstrap -->
                <div class="form-group col-md-6"> <!-- Adjust the col-md-* classes as needed -->
                <label for="inputDistrict">District</label>
                    <select id="inputDistrict" name="district" class="form-control mb-1">
                    <option selected><?php echo htmlspecialchars($userInfo['district']); ?></option>
                        <?php foreach ($districts as $district): ?>
                        <option value="<?php echo htmlspecialchars($district['district_name']); ?>">
                        <?php echo htmlspecialchars($district['district_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6"> <!-- Adjust the col-md-* classes as needed -->
                <label for="inputCity">City</label>
                    <select id="inputCity" name="city" class="form-control mb-1">
                    <option selected><?php echo htmlspecialchars($userInfo['city']); ?></option>
                        <?php foreach ($cities as $city): ?>
                        <option value="<?php echo htmlspecialchars($city['city_name']); ?>">
                        <?php echo htmlspecialchars($city['city_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="text-right mt-3 mb-3">
                        <button type="submit" name="submit" id="saveCompanyInfo" class="btn btn-primary">Save Changes</button>&nbsp;
                        <button type="button" id="cancelCompanylInfo" class="btn btn-default">Cancel</button>
                    </div>

                    </form>
                    </div>
                </div>

                        </div>
                    </div>
                </div>
            </div>
            

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('profileform');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(form);
        formData.append('ajax', 'true'); // Indicate an AJAX request

        fetch(window.location.href, { // Post to the same URL
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                alert('Profile updated successfully');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
</body>
</html>