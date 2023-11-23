<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  }
  
if ($_SESSION['status'] !== 2) {
    header('Location: applicant-homepage.php');
    exit(); 
  }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $job_id = $_POST['job_id'] ?? null; // Use POST data if form is submitted
} else {
    $job_id = $_GET['job_id'] ?? null; // Use GET data for initial page load
}


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

try {
    // Prepare the SQL query to check if the job_id belongs to the user_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE job_id = :job_id AND user_id = :user_id");
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the count
    $count = $stmt->fetchColumn();

    // Check if the count is 0, which means no matching record found
    if ($count == 0) {
        // Redirect to the homepage if the user_id does not match
        header("Location: employer-homepage.php");
        exit;
    }

    // Continue with your code if the user_id matches

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Handle the exception or redirect to an error page
}

$stmt = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
$stmt->bindParam(':job_id', $job_id);
$stmt->execute();
$job = $stmt->fetch(PDO::FETCH_ASSOC);

$categoriesStmt = $pdo->query("SELECT * FROM job_category");
$categories = $categoriesStmt->fetchAll();

$stmt = $pdo->query('SELECT district_id, district_name FROM district');
$options1 ='';
while ($row = $stmt->fetch()) {
    $options1 .= '<option value="'. $row['district_name'] .'">'. htmlspecialchars($row['district_name']) .'</option>';
}

$stmt = $pdo->query('SELECT city_id, city_name FROM city');
$options2 ='';
while ($row = $stmt->fetch()) {
    $options2 .= '<option value="'. $row['city_name'] .'">'. htmlspecialchars($row['city_name']) .'</option>';
}

$options3 = "";
foreach ($categories as $category) {
    $selected = ($category['job_cat_id'] == $job['job_cat_id']) ? 'selected' : '';
    $options3 .= "<option value='{$category['job_cat_id']}' {$selected}>{$category['job_cat']}</option>";
}

$typesStmt = $pdo->query("SELECT * FROM job_type");
$types = $typesStmt->fetchAll();

$options4 = "";
foreach ($types as $type) {
    $selected = ($type['job_type_id'] == $job['job_type_id']) ? 'selected' : '';
    $options4 .= "<option value='{$type['job_type_id']}' {$selected}>{$type['job_t']}</option>";
}

$levelsStmt = $pdo->query("SELECT * FROM exp_level");
$levels = $levelsStmt->fetchAll();

$options5 = "";
foreach ($levels as $level) {
    $selected = ($level['exp_level_id'] == $job['exp_level_id']) ? 'selected' : '';
    $options5 .= "<option value='{$level['exp_level_id']}' {$selected}>{$level['exp_lvl']}</option>";
}

$selectedDisabilities = [];
$disabilityStmt = $pdo->prepare("SELECT disability_id FROM job_disability_junction WHERE job_id = :job_id");
$disabilityStmt->bindParam(':job_id', $job_id);
$disabilityStmt->execute();

while ($row = $disabilityStmt->fetch(PDO::FETCH_ASSOC)) {
    $selectedDisabilities[] = $row['disability_id'];
}

$selectedAccessibility = [];
$accessibilityStmt = $pdo->prepare("SELECT accessibility_id FROM job_accessibility_junction WHERE job_id = :job_id");
$accessibilityStmt->bindParam(':job_id', $job_id);
$accessibilityStmt->execute();

while ($row = $accessibilityStmt->fetch(PDO::FETCH_ASSOC)) {
    $selectedAccessibility[] = $row['accessibility_id'];
}

$options6 = '';
$allDisabilitiesStmt = $pdo->query("SELECT * FROM disability");
while ($disability = $allDisabilitiesStmt->fetch(PDO::FETCH_ASSOC)) {
    $selected = in_array($disability['disability_id'], $selectedDisabilities) ? 'selected' : '';
    $options6 .= "<option value='{$disability['disability_id']}' {$selected}>{$disability['disability_name']}</option>";
}

$options7 = '';
$allAccessibilityStmt = $pdo->query("SELECT * FROM accessibility");
while ($accessibility = $allAccessibilityStmt->fetch(PDO::FETCH_ASSOC)) {
    $selected = in_array($accessibility['accessibility_id'], $selectedAccessibility) ? 'selected' : '';
    $options7 .= "<option value='{$accessibility['accessibility_id']}' {$selected}>{$accessibility['accessibility_tag']}</option>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assume $job_id is already defined and valid
    $job_title = $_POST['job_title'] ?? '';
    $job_type = $_POST['job_type'] ?? '';
    $district = $_POST['district'] ?? '';
    $city = $_POST['city'] ?? '';
    $job_desc = $_POST['job_desc'] ?? '';
    $role_desc = $_POST['role_desc'] ?? '';
    $role_req = $_POST['role_req'] ?? '';
    $qualification = $_POST['qualification'] ?? '';
    $salary_range = $_POST['salary_range'] ?? '';
    $overtime = $_POST['overtime'] ?? '';
    $job_cat_id = $_POST['job_cat_id'] ?? '';
    $job_type_id = $_POST['job_type_id'] ?? '';
    $exp_level_id = $_POST['exp_level_id'] ?? '';

    // Update job details
    $updateStmt = $pdo->prepare("UPDATE jobs SET job_title = ?, job_type = ?, district = ?, city = ?, job_desc = ?, role_desc = ?, role_req = ?, qualification = ?, salary_range = ?, overtime = ?, job_cat_id = ?, job_type_id = ?, exp_level_id = ? WHERE job_id = ?");
    $updateStmt->execute([$job_title, $job_type, $district, $city, $job_desc, $role_desc, $role_req, $qualification, $salary_range, $overtime, $job_cat_id, $job_type_id, $exp_level_id, $job_id]);

    $disabilitySupported = $_POST['disabilitySupported'] ?? [];
    $accessibilitySupported = $_POST['accessibilitySupported'] ?? [];

    // Update disabilities
    foreach ($disabilitySupported as $disabilityId) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM job_disability_junction WHERE job_id = :job_id AND disability_id = :disability_id");
        $checkStmt->execute(['job_id' => $job_id, 'disability_id' => $disabilityId]);
        if ($checkStmt->fetchColumn() == 0) {
            $insertStmt = $pdo->prepare("INSERT INTO job_disability_junction (job_id, disability_id) VALUES (:job_id, :disability_id)");
            $insertStmt->execute(['job_id' => $job_id, 'disability_id' => $disabilityId]);
        }
    }

    // Update accessibility
    foreach ($accessibilitySupported as $accessibilityId) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM job_accessibility_junction WHERE job_id = :job_id AND accessibility_id = :accessibility_id");
        $checkStmt->execute(['job_id' => $job_id, 'accessibility_id' => $accessibilityId]);
        if ($checkStmt->fetchColumn() == 0) {
            $insertStmt = $pdo->prepare("INSERT INTO job_accessibility_junction (job_id, accessibility_id) VALUES (:job_id, :accessibility_id)");
            $insertStmt->execute(['job_id' => $job_id, 'accessibility_id' => $accessibilityId]);
        }
    }

    // Send a JSON response or perform other actions as needed
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <title>Job Listing</title>
</head>
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
<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card col-lg-8 px-1 py-4">
        <div class="card-body">
        <form id="jobform" method="post">
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job_id); ?>">
        <h4 class="card-title mb-3 text-center">Job Listing</h4>
            <div class="row">
                <div class="col-12 col-sm-12">

                    <div class="form-group">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="job_title" class="form-control mb-1" value="<?php echo htmlspecialchars($job['job_title']); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Working Arrangement</label>
                        <select name="job_type" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['job_type']); ?>" disabled selected>Working Arrangement</option>
                            <option value="Remote Work">Remote Work</option>
                            <option value="On-Site Work">On-Site Work</option>
                            <option value="Hybrid Work">Hybrid Work</option>
                        </select>
                    </div>

                    <div class="form-row"> 
                        <div class="form-group col-md-6"> 
                        <label for="inputDistrict">District</label>
                            <select id="inputDistrict" name="district" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['district']); ?>" selected><?php echo htmlspecialchars($job['district']); ?></option>
                                <?php echo $options1;?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputCity">City</label>
                            <select id="inputCity" name="city" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['city']); ?>" selected><?php echo htmlspecialchars($job['city']); ?></option>
                                <?php echo $options2;?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Job Description</label>
                        <textarea class="form-control" name="job_desc" rows="3"><?php echo htmlspecialchars($job['job_desc']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Role Description</label>
                        <textarea class="form-control" name="role_desc" rows="3"><?php echo htmlspecialchars($job['role_desc']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Role Requirements</label>
                        <textarea class="form-control" name="role_req" rows="3"><?php echo htmlspecialchars($job['role_req']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Qualification</label>
                        <textarea class="form-control" name="qualification" rows="3"><?php echo htmlspecialchars($job['qualification']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Salary Range</label>
                        <input type="text" name="salary_range" class="form-control mb-1" value="<?php echo htmlspecialchars($job['salary_range']); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Overtime</label>
                        <select name="overtime" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['overtime']); ?>" selected><?php echo htmlspecialchars($job['overtime']); ?></option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inputCity">Job Category</label>
                            <select id="inputCity" name="job_cat_id" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['job_cat_id']); ?>" disabled selected>Job Category</option>
                                <?php echo $options3;?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="inputCity">Job Type</label>
                            <select id="inputCity" name="job_type_id" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['job_type_id']); ?>" disabled selected>Job Type</option>
                                <?php echo $options4;?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="inputCity">Experience Level</label>
                            <select id="inputCity" name="exp_level_id" class="form-control mb-1">
                            <option value="<?php echo htmlspecialchars($job['exp_level_id']); ?>" disabled selected>Experience Level</option>
                                <?php echo $options5;?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="disabilitySupported" class="form-label">Disability Supported</label>
                        <select id="disabilitySupported" name="disabilitySupported[]" class="form-select" multiple>
                            <option value="" disabled>Select Disability</option>
                            <?php echo $options6; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="accessibilitySupported" class="form-label">Accessibility Supported</label>
                        <select id="accessibilitySupported" name="accessibilitySupported[]" class="form-select" multiple>
                            <option value="" disabled>Select Accessibility</option>
                            <?php echo $options7; ?>
                        </select>
                    </div>


            <div class=" d-flex flex-column text-center px-5 mt-3 mb-3"> <small class="agree-text">By Posting this job you agree to our </small> <a href="#" class="terms">Terms & Conditions</a> </div> <button value="submit" class="btn btn-primary btn-block confirm-button">Update</button>
        </div>
    </div>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#disabilitySupported').select2({
        placeholder: "Select Disability",
        allowClear: true
    });
    $('#accessibilitySupported').select2({
        placeholder: "Select Accessibility",
        allowClear: true
    });
});


</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('jobform');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(form);

        fetch('employer-edit-joblisting.php', { // Replace with your PHP file name
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Job listing updated successfully');
                // Redirect or update the UI as needed
            } else {
                alert('Error updating job listing');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});

</script>

</html>