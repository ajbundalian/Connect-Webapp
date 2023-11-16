<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="../css/saved-jobs.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="/image/Connect Favicon (1).svg">
    <title>Saved Jobs</title>
</head>

<body>
<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

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
$user_id = $_SESSION['user_id'];

// Fetch saved job IDs for the user
$stmt = $pdo->prepare("SELECT job_id FROM saved_jobs WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$saved_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Array to hold all saved job details
$saved_job_details = [];

// Loop through each saved job and fetch its details
foreach ($saved_jobs as $saved_job) {
    $jobStmt = $pdo->prepare("
        SELECT j.job_id, j.job_title, j.district, j.city, cp.company_name
        FROM jobs j
        JOIN company_profile cp ON j.user_id = cp.user_id
        WHERE j.job_id = :job_id
    ");
    $jobStmt->bindParam(':job_id', $saved_job['job_id']);
    $jobStmt->execute();
    $jobDetails = $jobStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch the disabilities accommodated for the job
    $disabilityStmt = $pdo->prepare("
        SELECT d.disability_name
        FROM job_disability_junction jdj
        JOIN disability d ON jdj.disability_id = d.disability_id
        WHERE jdj.job_id = :job_id
    ");
    $disabilityStmt->bindParam(':job_id', $saved_job['job_id']);
    $disabilityStmt->execute();
    $disabilities = $disabilityStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add disabilities to job details
    $jobDetails['disability'] = $disabilities;

    // Add the job details to the saved job details array
    $saved_job_details[] = $jobDetails;
}

// Now $saved_job_details contains all the information needed to display saved jobs
?>
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

<div class="container">
    <h1>Saved Jobs</h1>
    <p>You have <span id="jobCount"><?php echo count($saved_job_details); ?></span> saved jobs.</p>

    <div class="job-grid">
        <?php foreach ($saved_job_details as $job): ?>
            <a href="job-detail.php?job_id=<?php echo $job['job_id']; ?>" data-job-id="<?php echo $job['job_id']; ?>" class="job-card">
                <img src="path_to_company_image.jpg" alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="company-pic">
                <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                <p><strong><?php echo htmlspecialchars($job['company_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($job['district'] . ', ' . $job['city']); ?></p>
                <p>Disability Accommodation:</p>
                    <?php foreach ($job['disability'] as $disability): ?>
                        <p class="disability-name"> <?php echo htmlspecialchars($disability['disability_name']) . ' '; ?><br></p>
                    <?php endforeach; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>
