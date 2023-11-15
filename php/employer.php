<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
</head>
<?php
// Start the session and include your database connection file here
session_start();
// Database connection
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

// Get company_id from URL
$companyId = $_GET['company_id'] ?? null;
$job = [];
// SQL for company profile
$stmt = $pdo->prepare("
        SELECT 
            company_name, 
            company_desc, 
            company_phone, 
            CONCAT(district, ', ', city) AS location, 
            company_website 
        FROM 
            company_profile 
        WHERE 
            company_id = :companyId
    ");
    
// Execute the prepared statement
$stmt->execute(['companyId' => $companyId]);
    
// Fetch the result
$companyProfile = $stmt->fetch(PDO::FETCH_ASSOC);

$fullUrl = $companyProfile['company_website'];

// Use parse_url() to get the components of the URL
$urlComponents = parse_url($fullUrl);

// Extract the host part
$domain = $urlComponents['host'] ?? $fullUrl;
// Prepare SQL to get user_id from company_profile table
if ($companyId) {
    $stmt = $pdo->prepare("SELECT user_id FROM company_profile WHERE company_id = :companyId");
    $stmt->execute(['companyId' => $companyId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $row['user_id'] ?? null;

// Now get all available jobs linked to this user_id
    if ($userId) {
        $jobsStmt = $pdo->prepare("SELECT job_id, job_title, job_desc FROM jobs WHERE user_id = :userId");
        $jobsStmt->execute(['userId' => $userId]);
        $jobs = $jobsStmt->fetchAll(PDO::FETCH_ASSOC);

        }
    }

?>
<style>
.profile-head {
    transform: translateY(7rem);
    margin-bottom: 2rem;
}

.cover {
    background-color: #7200F3;
    padding-bottom: 2rem;
}

body {
    background: #654ea3;
    background: linear-gradient(to right, #e96443, #904e95);
    min-height: 100vh;
    overflow-x: hidden;
}

.square-card {
    width: 350px; /* Define square size here */
    height: 350px;
    margin: 20px auto; /* To center the card in the column */
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* This will space out the card title and the button */
  }
.square-card .btn {
    width: 80%; /* Make button width smaller for aesthetics */
    margin: 0 auto; /* Center the button inside the card */
}

a:hover{
box-shadow: 9px 6px 19px 0px rgba(176,176,176,1);
-webkit-box-shadow: 9px 6px 19px 0px rgba(176,176,176,1);
-moz-box-shadow: 9px 6px 19px 0px rgba(176,176,176,1);
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

    <!--Nav Ends Here-->
<div class="row py-5 px-4"> 
    <div class="col-md-9 mx-auto"> 
        <!-- Profile widget --> 
        <div class="bg-white shadow rounded overflow-hidden"> 
            <div class="px-4 pt-0 pb-4 cover"> 
                <div class="media align-items-end profile-head"> 
                    <div class="profile mr-3">
                        <img src="https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=80" width="130" class="rounded mb-2 img-thumbnail">
                    </div> 
                        <div class="media-body mb-8 text-black"> 
                            <h4 class="mt-0 mb-0" id="Company Name"><?= htmlspecialchars($companyProfile['company_name']) ?></h4> 
                            </div> 
                        </div> 
                    </div> 
                                    <div class="px-4 mt-5 py-3"> 
                                        <div class="p4"> 
                                        <p class="mb-3"><?= htmlspecialchars($companyProfile['company_desc']) ?></p> 
                                        <h5 class="mb-0">Company Phone</h5> 
                                        <div class="p-0"> 
                                            <p class="mb-3"><?= htmlspecialchars($companyProfile['company_phone']) ?></p> 
                                        </div> 

                                        <h6 class="mb-0">Location: <?= htmlspecialchars($companyProfile['location']) ?></h6> 
                                    </div> 
                                    <div class="py-4 px-4"> 
                                        <div class="d-flex align-items-center justify-content-between mb-3"> 
                                            <h5 class="mb-0 p-0">Recent Job Listing</h5>
                                            <a href="<?= htmlspecialchars($fullUrl) ?>" class="btn btn-link btn-lg">Company Website</a> 
                                        </div> 
                                        <div class="row"> 
                                            <div class="col-lg-6 mb-3 pr-lg-1">
                                                <!-- Job Card Starts Here here -->
                                                <?php foreach ($jobs as $job): ?>
                                                    <?php
                                                        // If job_desc is longer than a certain length, truncate it and add ellipsis
                                                        $jobDesc = $job['job_desc'];
                                                        if (strlen($jobDesc) > 200) { // Assuming 100 characters is roughly two rows
                                                        $jobDesc = substr($jobDesc, 0, 200) . '...';
                                                        }
                                                        ?>
                                                <div class="card square-card">
                                                    <div class="card-body d-flex flex-column justify-content-center">
                                                        <h2 class="card-title text-center"><?= htmlspecialchars($job['job_title']) ?></h2>
                                                        <p class="card-text text-center"><?= htmlspecialchars($jobDesc) ?></p>
                                                        <a href="job-detail.php?job-id=<?= $job['job_id'] ?>" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Read More</a>
                                                    </div>
                                                </div>
                                             <!-- Job Card Ends here -->
                                         </div>
                                         <?php endforeach; ?> 
                                        </div> 
                                    </div>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>