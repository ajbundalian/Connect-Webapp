<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Company</title>
</head>
<?php
// Database connection variables
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

if ($_SESSION['status'] !== 1) {
  header('Location: employer-homepage.php');
  exit(); 
}
$host = 'localhost';
$db = 'connect';
$user = 'root';
$pass = '12345';
$charset = 'utf8mb4';
// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
  // Create a new PDO instance
  $pdo = new PDO($dsn, $user, $pass, $options);
  
  // SQL query to fetch all rows from company_profile table
  $stmt = $pdo->query("SELECT company_id, company_pic, company_name FROM company_profile");
  echo '<section class="company-grid">';
  $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  // Handle any exceptions/errors
  echo "Connection failed: " . $e->getMessage();
}
?>
<style>
  .square-card .btn {
      width: 80%; /* Make button width smaller for aesthetics */
      margin: 0 auto; /* Center the button inside the card */
  }
  .company-card {
  width: 350px; /* Define square size here */
  height: 350px;
  margin: 20px auto; /* To center the card in the column */
  justify-content: space-between; /* This will space out the card title and the button */
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  text-align: center;
  }

  .card:hover{
  -webkit-box-shadow: 9px 5px 17px -8px #000000; 
  box-shadow: 9px 5px 17px -8px #000000;
  }

  .company-card img {
  max-width: 80%; /* Adjust as needed */
  margin-bottom: auto; /* Pushes everything else down */
  }

  .company-card p {
  margin-top: auto; /* Pushes the paragraph to the bottom */
  }
</style>
<body>
<!-- Nav Starts Here -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark p-2">
        <div class="container-fluid">
          <a class="navbar-brand" href="applicant-homepage.php">
            <img src="../image/Connect Favicon (1).svg" alt="Connect Logo" height="50" width="50">
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        
          <div class=" collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto ">
              <li class="nav-item">
                <a class="nav-link mx-2 active h5" aria-current="page" href="applicant-homepage.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link mx-2 h5" href="jobs.php">Jobs</a>
              </li>
              <li class="nav-item">
                <a class="nav-link mx-2 h5" href="company-directory.php">Company</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link mx-2 dropdown-toggle h5" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Account
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li><a class="dropdown-item h5" href="applicant-profilepage.php">Profile</a></li>
                  <li><a class="dropdown-item h5" href="logout.php">Logout</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
</nav>
<!-- Nav Ends Here -->
<div class="container mt-5">
  <h3 class="display-4 text-center mb-5">Company Directory</h3>
  <div class="row">
    <?php foreach ($companies as $company): ?>
      <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="card">
          <a class="company-card" href="employer.php?company_id=<?php echo $company['company_id'];?>" company-id="<?php echo ($company['company_id']); ?>">
            <img src="../image/<?php echo htmlspecialchars($company['company_pic']); ?>" alt="Company Logo">
            <p><?php echo htmlspecialchars($company['company_name']); ?></p>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>