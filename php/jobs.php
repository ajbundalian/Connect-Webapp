<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="../css/jobs.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Jobs</title>
    <style>
        .dropdown-menu {
            padding: 10px;
        }
        .dropdown-menu label {
            cursor: pointer; /* Makes the whole label clickable, not just the checkbox */
        }
        .dropdown-menu label:hover {
            background-color: #f8f9fa;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f8f8f8;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h3{
            text-align: center;
        }

        p {
            text-align: center;
        }

        .job-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .job-card {
            display: block;
            width: 350px;
            height: 350px;
            margin: 15px;
            padding: 20px;
            border: 1px solid transparent;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit; /* Ensures the text inside anchor remains unchanged */
        }

        .job-card:hover {
            border-color: #666;
            text-decoration: none; /* Removes the underline from the link */
        }

        .company-pic {
            width: 100%;
            height: 200px;
            display: block;
            margin: 0 auto 15px;
        }

        /* For Responsiveness */
        @media screen and (max-width: 768px) {
            .job-grid {
                grid-template-columns: 1fr;
            }
            .job-card{
                margin: auto;
                width: 90%;
            }
}
    </style>
</head>
<body>
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
//For Job Categories
// Prepare the SQL statement
$stmt = $pdo->query('SELECT job_cat_id, job_cat FROM job_category');

// Fetch and iterate through each row to build the dropdown options
$options1 = '';
while ($row = $stmt->fetch()) {
    $options1 .= '<option value="'. $row['job_cat_id'] .'">'. htmlspecialchars($row['job_cat']) .'</option>';
}
//For Experience Level
$stmt = $pdo->query('SELECT exp_level_id, exp_lvl FROM exp_level');
$options2 ='';
while ($row = $stmt->fetch()) {
    $options2 .= '<option value="'. $row['exp_level_id'] .'">'. htmlspecialchars($row['exp_lvl']) .'</option>';
}

$stmt = $pdo->query('SELECT job_type_id, job_t FROM job_type');
$options3 ='';
while ($row = $stmt->fetch()) {
    $options3 .= '<option value="'. $row['job_type_id'] .'">'. htmlspecialchars($row['job_t']) .'</option>';
}

$stmt = $pdo->query('SELECT district_id, district_name FROM district');
$options4 ='';
while ($row = $stmt->fetch()) {
    $options4 .= '<option value="'. $row['district_id'] .'">'. htmlspecialchars($row['district_name']) .'</option>';
}

$stmt = $pdo->query('SELECT disability_id, disability_name FROM disability');
$options5 ='';
while ($row = $stmt->fetch()) {
    $options5 .= '<option value="'. $row['disability_id'] .'">'. htmlspecialchars($row['disability_name']) .'</option>';
}

$stmt = $pdo->query('SELECT accessibility_id, accessibility_tag FROM accessibility');
$options6 ='';
while ($row = $stmt->fetch()) {
    $options6 .= '<div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="accessibility[]" id="inlineCheckbox" value="'. $row['accessibility_id'] .'">
                    <label class="form-check-label" for="inlineCheckbox">' . htmlspecialchars($row['accessibility_tag']) . '</label>
                </div>';
}

//For the search filter
// Begin the query with a base where we know it will always have a condition
$query = "SELECT DISTINCT j.job_id FROM jobs j";

// For joining with many-to-many relationship tables
$conditions = [];
$params = [];

//Check for disability
if (!empty($_POST['disability'])) {
    $query .= " JOIN job_disability_junction jdj ON j.job_id = jdj.job_id";
    $conditions[] = " jdj.disability_id = :disability";
    $params[':disability'] = $_POST['disability'];
}

// Check for search input
if (!empty($_POST['search'])) {
    $conditions[] = " j.job_title LIKE :search";
    $params[':search'] = '%' . $_POST['search'] . '%';
}

// Check for job category filter
if (!empty($_POST['job_category'])) {
    $conditions[] = " j.job_cat_id = :job_category";
    $params[':job_category'] = $_POST['job_category'];
}

// Check for experience level filter
if (!empty($_POST['exp_level'])) {
    $conditions[] = " j.exp_level_id = :exp_level";
    $params[':exp_level'] = $_POST['exp_level'];
}

// Check for job type filter
if (!empty($_POST['job_type'])) {
    $conditions[] = " j.job_type_id = :job_type";
    $params[':job_type'] = $_POST['job_type'];
}

// Check for district filter
if (!empty($_POST['district'])) {
    $conditions[] = " j.district = :district";
    $params[':district'] = $_POST['district'];
}


// Check for accessibility checkboxes
if (!empty($_POST['accessibility'])) {
    $accessibilityIds = $_POST['accessibility'];
    $query .= " JOIN job_accessibility_junction jaj ON j.job_id = jaj.job_id";
    
    // Convert accessibility IDs to named parameters
    $accessibilityConditions = [];
    foreach ($accessibilityIds as $index => $id) {
        $param = ':accessibility' . $index;
        $accessibilityConditions[] = $param;
        $params[$param] = $id;
    }
    // Ensure the WHERE clause is added only once
    $query .= (count($conditions) > 0 ? " AND" : " WHERE") . " jaj.accessibility_id IN (" . implode(',', $accessibilityConditions) . ")";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND', $conditions);
}
// Prepare the statement
$stmt = $pdo->prepare($query);

// Bind parameters
foreach ($params as $key => &$val) {
    $stmt->bindValue($key, $val);
}

// Execute the query
$stmt->execute();

// Fetch all matching job IDs
$matchingJobs = $stmt->fetchAll(PDO::FETCH_COLUMN);
$bannerHtml = '';

if (empty($matchingJobs)) {
    $bannerHtml = '<div class="jumbotron jumbotron-fluid">
                        <div class="container mt-5">
                        <h1 class="display-4">No Jobs Found</h1>
                        <p class="lead">No jobs were a match to your parameters</p>
                        </div>
                    </div>';
} elseif (is_array($matchingJobs) && count($matchingJobs) > 1) {
    // More than one job found

    // Normalize $matchingJobs to ensure it's an array
    $matchingJobs = (array) $matchingJobs;

    // Creating placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($matchingJobs), '?'));

    // SQL query with JOIN
    $sql = "SELECT j.*, c.company_name, c.company_pic 
            FROM jobs j
            JOIN company_profile c ON j.user_id = c.user_id
            WHERE j.job_id IN ($placeholders)";

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare($sql);
    $stmt->execute($matchingJobs);
} elseif (is_array($matchingJobs) && count($matchingJobs) === 1) {
    // Exactly one job found

    $sql = "SELECT j.*, c.company_name, c.company_pic 
            FROM jobs j
            JOIN company_profile c ON j.user_id = c.user_id 
            WHERE j.job_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$matchingJobs[0]]);
}

if (isset($stmt)) {
    // Fetch the job details
    $saved_job_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


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

<!-- Search bar Starts here-->
<div class="container-fluid mt-5">
    <form action="jobs.php" method="post">
    <!-- Main Search Row -->
    <div class="row">
        <div class="col-lg-4 col-md-4 col-12 mb-2">
            <input type="text" class="form-control" name="search"placeholder="Search...">
        </div>
        <div class="col-lg-2 col-md-4 col-12 mb-2">
            <select class="form-control mb-1" name="job_category" id = "job_category">
            <option value="" disabled selected>Category</option>
                <?php echo $options1;?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-2">
            <select class="form-control mb-1" name="exp_level" id = "exp_level">
            <option value="" disabled selected>Experience Level</option>
                <?php echo $options2;?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-2">
            <select class="form-control mb-1"  name="job_type" id="job_type">
            <option value="" disabled selected>Type</option>
                <?php echo $options3;?>
            </select>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-2">
            <select class="form-control" name="district" id="district">
                <option value="" disabled selected>District</option>
                <?php echo $options4;?>
            </select>
        </div>
        <div class="col-lg-2 col-md-3 col-6 mb-2">
            <select class="form-control" name="disability" id="disability">
            <option value="" disabled selected>Disability Supported</option>
                <?php echo $options5;?>
            </select>
        </div>
        <div class="col-lg-8 col-md-6  col-12 mb-3">
            <?php echo $options6;?>
        </div>
        <div class="col-lg-2 col-md-4 col-12 mb-1 d-grid">
            <button type = "submit" class="btn btn-primary mb-1 ">Search</button>
        </div>

    </div>
    </form>
</div>
<!-- Search bar Ends here-->

<?= $bannerHtml ?>
<!-- Job Grid -->
<div class="job-grid">
        <?php foreach ($saved_job_details as $job): ?>
            <a href="job-detail.php?job_id=<?php echo $job['job_id']; ?>" data-job-id="<?php echo $job['job_id']; ?>" class="job-card">
                <img src="../image/<?php echo htmlspecialchars($job['company_pic']); ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="company-pic">
                <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                <p><strong><?php echo htmlspecialchars($job['company_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($job['district'] . ', ' . $job['city']); ?></p>
            </a>
        <?php endforeach; ?>
    </div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script>
    // Show/hide additional filters
    document.getElementById('moreFilters').addEventListener('click', function() {
        var additionalFilters = document.getElementById('additionalFilters');
        additionalFilters.style.display = additionalFilters.style.display === 'none' ? 'flex' : 'none';
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>