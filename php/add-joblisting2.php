<?php 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  }
  
if ($_SESSION['status'] !== 2) {
    header('Location: applicant-homepage.php');
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
$job_id = $_SESSION['job_id'];
$pdo = new PDO($dsn, $user, $pass, $options);

$stmt = $pdo->query('SELECT disability_id, disability_name FROM disability');
$options5 ='';
while ($row = $stmt->fetch()) {
    $options5 .= '<option value="'. $row['disability_id'] .'">'. htmlspecialchars($row['disability_name']) .'</option>';
}

$stmt = $pdo->query('SELECT accessibility_id, accessibility_tag FROM accessibility');
$options6 ='';
while ($row = $stmt->fetch()) {
    $options6 .= '<option value="'. $row['accessibility_id'] .'">'. htmlspecialchars($row['accessibility_tag']) .'</option>';
}

if ($job_id && $_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Insert selected disabilities into job_disability_junction
    if (isset($_POST['disabilitySupported'])) {
        $disabilityStatement = $pdo->prepare("INSERT INTO job_disability_junction (job_id, disability_id) VALUES (?, ?)");
        foreach ($_POST['disabilitySupported'] as $disabilityId) {
            $disabilityStatement->execute([$job_id, $disabilityId]);
        }
    }

    // Insert selected accessibility options into job_accessibility_junction
    if (isset($_POST['accessibilitySupported'])) {
        $accessibilityStatement = $pdo->prepare("INSERT INTO job_accessibility_junction (job_id, accessibility_id) VALUES (?, ?)");
        foreach ($_POST['accessibilitySupported'] as $accessibilityId) {
            $accessibilityStatement->execute([$job_id, $accessibilityId]);
        }
    }

    // Assuming you want to return a JSON response
    header('Content-Type: application/json');
    echo json_encode(["status" => "success"]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <title>Add Job Listing</title>
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
        <h4 class="card-title mb-3 text-center">Additional Information</h4>
            <div class="row">
                <div class="col-12 col-sm-12">

                <div class="form-group">
                    <label for="disabilitySupported" class="form-label">Disability Supported</label>
                    <select id="disabilitySupported" name="disabilitySupported[]" class="form-select" multiple>
                        <option value="" disabled>Select Disability</option>
                        <?php echo $options5; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="accessibilitySupported" class="form-label">Accessibility Supported</label>
                    <select id="accessibilitySupported" name="accessibilitySupported[]" class="form-select" multiple>
                        <option value="" disabled>Select Accessibility</option>
                        <?php echo $options6; ?>
                    </select>
                </div>


                <div class=" d-flex flex-column text-center px-5 mt-3 mb-3"> <small class="agree-text">By Posting this job you agree to our </small> <a href="#" class="terms">Terms & Conditions</a> </div> <button value="submit" class="btn btn-primary btn-block confirm-button">Submit</button>
                </div>
            </div>
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

    form.onsubmit = function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way

        var formData = new FormData(form);

        fetch('add-joblisting2.php', { // Replace with the path to your PHP script
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log('Disabilities and accessibility options added successfully');
                // Handle successful insertion here, such as redirecting to another page
                window.location.href = 'employer-homepage.php';
            } else {
                // Handle error here
                console.error('An error occurred:', data);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    };
});
</script>
</html>