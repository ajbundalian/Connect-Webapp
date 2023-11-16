<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  }
  
  if ($_SESSION['status'] !== 1) {
    header('Location: employer-homepage.php');
    exit(); 
}
$user_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'];
include 'job-save-ajax.php';
include 'job-connect-ajax.php';
include 'job-report-ajax.php';
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
// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
$stmt->bindParam(':job_id', $job_id);
$stmt->execute();
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    exit(); // Or handle appropriately
}

// Fetch additional job details
// Assuming $pdo is your PDO database connection instance and $job_id is the ID of the job
$stmt = $pdo->prepare("
    SELECT jc.job_cat
    FROM jobs j
    JOIN job_category jc ON j.job_cat_id = jc.job_cat_id
    WHERE j.job_id = :job_id
");
$stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
$stmt->execute();
$jobCategory = $stmt->fetch(PDO::FETCH_ASSOC);



// If the job category is not found, you may want to handle that case appropriately.
if (!$jobCategory) {
    $jobCategoryName = "Unknown Category"; // Default or error handling
} else {
    $jobCategoryName = $jobCategory['job_cat']; // Your actual job category name
}

$stmt = $pdo->prepare("
    SELECT jt.job_t
    FROM jobs j
    JOIN job_type jt ON j.job_type_id = jt.job_type_id
    WHERE j.job_id = :job_id
");
$stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
$stmt->execute();
$jobType = $stmt->fetch(PDO::FETCH_ASSOC);

// If the job type is not found, you may want to handle that case appropriately.
if (!$jobType) {
    $jobTypeName = "Unknown Type"; // Default or error handling
} else {
    $jobTypeName = $jobType['job_t']; // Your actual job type name
}

$stmt = $pdo->prepare("
    SELECT el.exp_lvl
    FROM jobs j
    JOIN exp_level el ON j.exp_level_id = el.exp_level_id
    WHERE j.job_id = :job_id
");
$stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
$stmt->execute();
$experienceLevel = $stmt->fetch(PDO::FETCH_ASSOC);

// If the experience level is not found, handle appropriately.
if (!$experienceLevel) {
    $experienceLevelName = "Unknown Experience Level"; // Default or error handling
} else {
    $experienceLevelName = $experienceLevel['exp_lvl']; // The actual experience level
}

// Fetch disabilities associated with the job
$disabilityStmt = $pdo->prepare("
    SELECT d.disability_name
    FROM job_disability_junction jdj
    JOIN disability d ON jdj.disability_id = d.disability_id
    WHERE jdj.job_id = :job_id
");
$disabilityStmt->bindParam(':job_id', $job_id);
$disabilityStmt->execute();
$disabilities = $disabilityStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch accessibility features associated with the job
$accessibilityStmt = $pdo->prepare("
    SELECT a.accessibility_tag
    FROM job_accessibility_junction jaj
    JOIN accessibility a ON jaj.accessibility_id = a.accessibility_id
    WHERE jaj.job_id = :job_id
");
$accessibilityStmt->bindParam(':job_id', $job_id);
$accessibilityStmt->execute();
$accessibilities = $accessibilityStmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has already connected to the job
$connectedStmt = $pdo->prepare("SELECT COUNT(*) FROM applicant_list WHERE user_id = :user_id AND job_id = :job_id");
$connectedStmt->bindParam(':user_id', $user_id);
$connectedStmt->bindParam(':job_id', $job_id);
$connectedStmt->execute();
$isConnected = $connectedStmt->fetchColumn() > 0;

// Check if the user has already saved the job
$savedStmt = $pdo->prepare("SELECT COUNT(*) FROM saved_jobs WHERE user_id = :user_id AND job_id = :job_id");
$savedStmt->bindParam(':user_id', $user_id);
$savedStmt->bindParam(':job_id', $job_id);
$savedStmt->execute();
$isSaved = $savedStmt->fetchColumn() > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="../css/job-listing.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Job Details</title>
</head>
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
    
<div class="job-listing-container">
    <header class="job-header">
        <img src="path_to_company_logo.jpg" alt="Company Logo" id="company-logo">
        <h1 id="company-name">Spotify</h1>
    </header>

    <h2 class="job-title"><?php echo htmlspecialchars($job['job_title']); ?></h2>
    
    <div class="job-details">
        <span class="job-type">Working Arrangement: <?php echo htmlspecialchars($job['job_type']); ?>.</span>
        <span class="job-location">Job Location: <?php echo htmlspecialchars($job['district']); ?>, <?php echo htmlspecialchars($job['city']); ?>.</span>
        <span class="job_category">Job Category: <?php echo htmlspecialchars($jobCategoryName); ?>.</span>
        <span class="job_type">Job Type: <?php echo htmlspecialchars($jobTypeName); ?>.</span>
        <span class="exp_level">Experience Required: <?php echo htmlspecialchars($experienceLevelName); ?>.</span>
        <span class="job-type">Salary Range: <?php echo htmlspecialchars($job['salary_range']); ?>.</span>
        <span class="job-type">Overtime Availability: <?php echo htmlspecialchars($job['overtime']); ?>.</span>
    </div>

    <div class="job-details-2">
            <?php foreach ($disabilities as $disability): ?>
                <span class="disability">Disability Supported: <?php echo htmlspecialchars($disability['disability_name']); ?></span>
            <?php endforeach; ?>
    </div>

    <div class="job-details-3">
            <span class="date-posted">Date Posted: <?php echo htmlspecialchars($job['date_posted']); ?></span>
    </div>

    <div class="action-buttons">
    <button class="btn-save-btn" data-job-id="<?php echo htmlspecialchars($job_id); ?>">
                <?php echo $isSaved ? 'Unsave' : 'Save'; ?>
            </button>
            <button class="btn-connect-btn" data-job-id= "<?php echo $job['job_id']; ?>" <?php if ($isConnected) echo 'disabled'; ?>>
                <?php echo $isConnected ? 'Connected' : 'Connect'; ?>
            </button>
            <button  class="btn-report-btn" data-job-id= "<?php echo $job['job_id']; ?>">Report</button>

    </div>

    <section class="job-description">
        <h3>About the Job</h3>
        <p><?php echo nl2br(htmlspecialchars($job['job_desc'])); ?></p>
        <h3>Role Description</h3>
        <p><?php echo nl2br(htmlspecialchars($job['role_desc'])); ?></p>
        <h3>Role Requirements</h3>
        <p><?php echo nl2br(htmlspecialchars($job['role_req'])); ?></p>
    </section>

    <section class="accessibility-features">
        <h3>Accessibility</h3>
        <ul>
            <li>Accessible Devices</li>
            <li>Accessible Building</li>
            <li>Accessible Work Environment</li>
            <li>In-Person Assistance</li>
        </ul>
    </section>
</div>
<script>
    var userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
</script>
<script src="job-listing.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>