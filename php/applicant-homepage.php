<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="../css/applicant-homepage.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Home</title>
</head>
<body>
<div class="mother-ship">

<?php
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['status'] !== 1) {
    header('Location: employer-homepage.php'); 
    exit();
}

// Database connection
// 
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

// Fetch the three latest companies
$stmt = $pdo->query("
    SELECT company_id, company_name, company_pic
    FROM company_profile 
    ORDER BY company_id DESC 
    LIMIT 3
");
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user information
$pdo = new PDO($dsn, $user, $pass, $options);
$stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the three latest jobs
$stmt = $pdo->query("
    SELECT j.job_id, j.job_title, cp.company_name, cp.company_pic
    FROM jobs j
    JOIN company_profile cp ON j.user_id = cp.user_id
    ORDER BY j.date_posted DESC
    LIMIT 3
");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($jobs as $index => $job) {
    // Fetch the associated disabilities for each job
    $disabilityStmt = $pdo->prepare("
        SELECT d.disability_name
        FROM job_disability_junction jdj
        JOIN disability d ON jdj.disability_id = d.disability_id
        WHERE jdj.job_id = :job_id
    ");
    $disabilityStmt->bindParam(':job_id', $job['job_id']);
    $disabilityStmt->execute();
    $disabilities = $disabilityStmt->fetchAll(PDO::FETCH_ASSOC);

     // Limit the disabilities to the first two entries
     $limitedDisabilities = array_slice($disabilities, 0, 2);

    // Add the disabilities to the job array
    $jobs[$index]['disabilities'] = $disabilities;
}


// Use $userInfo to populate the HTML template
?>


<!--Navigation Starts Here Here-->
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
<!--Navigation Ends Here-->

        <div class="main" id="maincontent" style="margin-top: 10px;">
            <div class="container">
                <aside class="profile-sidebar">
                    <div class="profile-container">
                        <div class="profile-image-wrapper">
                            <div class="profile-image">
                                <img src="../image/<?php echo $userInfo['photo']; ?>">
                            </div>
                        </div>
                        <p class="profile-text">Welcome, <?php echo htmlspecialchars($userInfo['first_name']); ?>! </p>
                        <p class="profile-text"> <?php echo htmlspecialchars($userInfo['district']); ?>, <?php echo htmlspecialchars($userInfo['city']); ?> </p>
                    </div>
                    <div class="edit-profile-button-container">
                        <a href="applicant-profilepage.php" class="edit-profile-button">
                            <ion-icon name="create-outline"></ion-icon>
                            Edit Profile
                        </a>
                    </div>
                    <div class="profile-links">
                        <a href="recommended-jobs.php"><ion-icon name="briefcase-outline" aria-hidden="true"></ion-icon> Recommended Jobs</a>
                        <a href="connected-jobs.php"><ion-icon name="archive-outline" aria-hidden="true"></ion-icon> Connected Jobs</a>
                        <a href="saved-jobs.php"><ion-icon name="bookmark-outline" aria-hidden="true"></ion-icon> Saved Jobs</a>
                    </div>
                </aside>
            
            
            <div class="right-container">
                <div class="recently-jobs-tag">
                    <h2 class="r-tag">Recently Added Jobs</h2>
                    <a href="jobs.php" class="view-all">View All</a>
                </div>
                <div class="recently-jobs">
    <div class="recently-jobs-container">
        <?php foreach ($jobs as $job): ?>
        <div class="recently-job-card">
            <article class="recently-job-content">
                <a class="job-card" href="job-detail.php?job_id=<?php echo $job['job_id']; ?>" data-job-id="<?php echo $job['job_id']; ?>">
                    <div class="company-picture-container">   
                        <img class="company-picture" src="../image/<?php echo $job['company_pic']; ?>">
                    </div>
                    <p class="job-title"><?php echo htmlspecialchars($job['job_title']); ?> at <?php echo htmlspecialchars($job['company_name']); ?></p>
                    <div class="company-name-container">
                        <p class="company-name"><?php echo htmlspecialchars($job['company_name']); ?></p>
                    </div>
                    <div class="disability-name-container">
                    <?php if (!empty($job['disabilities'])): ?>
                        <?php foreach ($job['disabilities'] as $disability): ?>
                            <p class="disability-name"><?php echo htmlspecialchars($disability['disability_name']); ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                </a>
            </article>
        </div>
        <?php endforeach; ?>
        </div>
        </div>
                <div>
                    <div class="recently-company-tag">
                        <h2 class="c-tag">Recently Added Companies</h2>
                        <a href="company-directory.php" class="view-all">View All</a>
                    </div>
                </div>
                <div class="recently-company">
                    <div class="recently-company-container">
                    <?php foreach ($companies as $company): ?>
            <div class="recently-company-card">
                <article class="recently-company-content">
                    <a class="company-card" href="employer.php?company_id=<?php echo $company['company_id'];?>" company-id="<?php echo ($company['company_id']); ?>">
                        <div class="company-picture-container-parent">
                            <div class="company-picture-container2">
                                <!-- Assuming company_logo contains the path to the logo image -->
                                <img src="../image/<?php echo htmlspecialchars($company['company_pic']); ?>" alt="" class="company-picture2">
                                    </div>
                                </div>
                                <div class="company-name-container2">
                            <p class="company-name2"><?php echo htmlspecialchars($company['company_name']); ?></p>
                                </div>
                                </a>
                            </article>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>

            </div>
        
        
        
        
        </div>
    </div>

        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>