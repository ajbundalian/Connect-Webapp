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

$user_id = $_SESSION['user_id'];

$pdo = new PDO($dsn, $user, $pass, $options);
$stmt = $pdo->prepare("SELECT * FROM company_profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("SELECT COUNT(job_id) as job_count FROM jobs WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $stmt = $pdo->prepare("SELECT j.job_id, COUNT(a.user_id) as applicant_count FROM jobs j LEFT JOIN applicant_list a ON j.job_id = a.job_id WHERE j.user_id = :user_id GROUP BY j.job_id");
    $stmt->execute(['user_id' => $user_id]);

    $app_list = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($app_list === false) {
        // Set applicant_count to 0 if no rows are found
        $app_list = ['applicant_count' => 0];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try{
    $stmt= $pdo->prepare("SELECT job_id, job_title, job_desc FROM jobs WHERE user_id = :userId");
    $stmt->execute(['userId' => $user_id]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($jobs)) {
        $noJobsHtml = '<div class="jumbotron jumbotron-fluid bg-light bg-gradient">
                          <div class="container text-center">
                            <h1 class="display-4">No jobs were found</h1>
                            <a href="add-joblisting.php" class="btn btn-light btn-dark" role="button" aria-pressed="true">Create a Job Listing</a>
                          </div>
                       </div>';
    } else {
        $noJobsHtml = '';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Profile</title>
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

<!-- Profile -->
<div class="row py-5 px-4">
    <div class="col-xl-8 col-md-6 col-sm-10 mx-auto">

        <!-- Profile widget -->
        <div class="bg-white shadow rounded overflow-hidden">
            <div class="px-4 pt-0 pb-4 bg-dark">
                <div class="media align-items-end profile-header">
                    <div class="profile mr-3 mt-3"><img src="../image/<?php echo htmlspecialchars($userInfo['company_pic']); ?>" alt="..." height="150" width="150" class="rounded mb-2 img-thumbnail"><a href="employer-edit-profile.php" class="btn btn-dark btn-sm btn-block">Edit profile</a></div>
                    <div class="media-body mb-5 text-white">
                        <h4 class="mt-0 mb-0"><?php echo htmlspecialchars($userInfo['company_name']); ?></h4>
                        <p class="small mb-4"><ion-icon name="location-outline"></ion-icon><?php echo htmlspecialchars($userInfo['district']); ?>, <?php echo htmlspecialchars($userInfo['city']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-light p-4 d-flex justify-content-end text-center">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <h5 class="font-weight-bold mb-0 d-block"><?php echo htmlspecialchars($result['job_count']); ?></h5><small class="text-muted">Job Listing</small>
                    </li>
                    <li class="list-inline-item">
                        <h5 class="font-weight-bold mb-0 d-block"><?php echo htmlspecialchars($app_list['applicant_count']); ?></h5><small class="text-muted">Applicants</small>
                    </li>
                </ul>
            </div>

            <div class="py-4 px-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">Recent Listings</h5><a href="employer-jobs.php" class="btn btn-link text-muted">Show all</a>
                </div>
                <div class="row">
                    <?php foreach ($jobs as $job): ?>
                        <?php
                            // If job_desc is longer than a certain length, truncate it and add ellipsis
                            $jobDesc = $job['job_desc'];
                            if (strlen($jobDesc) > 200) { // Assuming 100 characters is roughly two rows
                            $jobDesc = substr($jobDesc, 0, 200) . '...';
                            }
                            ?>
                        <div class="col-lg-6 mb-2 pr-lg-1 job-card">
                            <div class="card-body rounded-3 job-card-content">
                                <h2 class="card-title text-center"><?= htmlspecialchars($job['job_title']) ?></h2>
                                <p class="card-text text-center"><?= htmlspecialchars($jobDesc) ?></p>
                                <a href="employer-joblisting.php?job_id=<?php echo $job['job_id'];?>" class="btn btn-light btn-lg" role="button" aria-pressed="true">View Listing</a>
                            </div>
                        </div>
                    <?php endforeach; ?> 

                        <?php echo $noJobsHtml; ?>
                </div>
            </div>
        </div><!-- End profile widget -->

    </div>
</div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</html>