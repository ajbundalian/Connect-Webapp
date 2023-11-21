<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['status'] !== 2) {
    header('Location: employer-homepage.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];
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
$pdo = new PDO($dsn, $user, $pass, $options);
$user_id = $_SESSION['user_id'];
try {
    // Fetch all jobs posted by this user with their titles
    $jobsStmt = $pdo->prepare("SELECT job_id, job_title FROM jobs WHERE user_id = :user_id");
    $jobsStmt->bindParam(':user_id', $user_id);
    $jobsStmt->execute();
    $jobs = $jobsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if no jobs were found
    if (empty($jobs)) {
        $noJobsHtml = "
            <div class='jumbotron jumbotron-fluid bg-light bg-gradient'>
                <div class='container text-center'>
                    <h1 class='display-4'>No jobs were found</h1>
                    <a href='add-joblisting.php' class='btn btn-light btn-dark' role='button' aria-pressed='true'>Create a Job Listing</a>
                </div>
            </div>";
    } else {
        // Iterate over each job to count applicants
        foreach ($jobs as &$job) {
            // Count total applicants for each job
            $totalApplicantsStmt = $pdo->prepare("SELECT COUNT(*) FROM applicant_list WHERE job_id = :job_id");
            $totalApplicantsStmt->bindParam(':job_id', $job['job_id']);
            $totalApplicantsStmt->execute();
            $job['total_applicants'] = $totalApplicantsStmt->fetchColumn();

            // Count applicants with status '0' for each job
            $statusApplicantsStmt = $pdo->prepare("SELECT COUNT(*) FROM applicant_list WHERE job_id = :job_id AND status = 'Pending'");
            $statusApplicantsStmt->bindParam(':job_id', $job['job_id']);
            $statusApplicantsStmt->execute();
            $job['status_0_applicants'] = $statusApplicantsStmt->fetchColumn();
        }
        unset($job); // Break the reference with the last element

        // Now $jobs contains each job with its title, the total number of applicants, and the count of status '0' applicants
        // You can iterate over $jobs to display this information
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <title>Applicants</title>
</head>
<style>
    body{
        font-family: 'DM Sans', sans-serif;
    }
    .profile-header {
    transform: translateY(5rem);
    }
    body{
        font-family: 'DM Sans', sans-serif;
    }
   .job-card {
    width: 350px; /* Define square size here */
    height: 350px;
    margin: 20px auto;
    }

    .job-card .btn {
        width: 80%; /* Make button width smaller for aesthetics */
        margin: 0 auto; /* Center the button inside the card */
    }

    .job-card:hover {
        box-shadow: 14px 18px 34px -17px rgba(0,0,0,0.4);
        -webkit-box-shadow: 14px 18px 34px -17px rgba(0,0,0,0.4);
        -moz-box-shadow: 14px 18px 34px -17px rgba(0,0,0,0.4);
    }

    .job-card-content {
        background-color: #343A40;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%; /* Ensure full height for proper centering */
        background-image: url( '../image/basic.svg' );
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
    <div class="col-xl-10 col-md-6 col-sm-10 mx-auto">

        <!-- Profile widget -->
        <div class="bg-white shadow rounded overflow-hidden">
            <div class="py-4 px-4">
                <div class="w-100 text-center mb-3">
                    <h3 class="mb-3 text-center">Job Listings</h3>
                </div>
                <div class="row">
                    <?php foreach ($jobs as $job): ?>
                        <div class="col-lg-6 mb-2 pr-lg-1 job-card">
                            <div class="card-body rounded-3 job-card-content">
                                <h2 class="card-title text-center"><?php echo htmlspecialchars($job['job_title']); ?></h2>
                                <div class="justify-content-sapce-between text-center">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item">
                                            <h5 class="font-weight-bold mb-0 d-block"><?php echo htmlspecialchars($job['status_0_applicants']); ?></h5><small class="text-muted">Pending Applicants</small>
                                        </li>
                                        <li class="list-inline-item">
                                            <h5 class="font-weight-bold mb-0 d-block"><?php echo htmlspecialchars($job['total_applicants']); ?></h5><small class="text-muted">Applicants</small>
                                        </li>
                                    </ul>
                                </div>
                                <a href="employer-joblisting.php?job_id=<?php echo $job['job_id'];?>" class="btn btn-light btn-lg" role="button" aria-pressed="true">View Applicants</a>
                            </div>
                        </div>
                    <?php endforeach; ?> 
                <?php echo $noJobsHtml; ?>

            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>