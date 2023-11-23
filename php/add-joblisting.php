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
$pdo = new PDO($dsn, $user, $pass, $options);

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

$stmt = $pdo->query('SELECT job_cat_id, job_cat FROM job_category');
$options3 = '';
while ($row = $stmt->fetch()) {
    $options3 .= '<option value="'. $row['job_cat_id'] .'">'. htmlspecialchars($row['job_cat']) .'</option>';
}

$stmt = $pdo->query('SELECT job_type_id, job_t FROM job_type');
$options4 ='';
while ($row = $stmt->fetch()) {
    $options4 .= '<option value="'. $row['job_type_id'] .'">'. htmlspecialchars($row['job_t']) .'</option>';
}

$stmt = $pdo->query('SELECT exp_level_id, exp_lvl FROM exp_level');
$options5 ='';
while ($row = $stmt->fetch()) {
    $options5 .= '<option value="'. $row['exp_level_id'] .'">'. htmlspecialchars($row['exp_lvl']) .'</option>';
}

//Submitting Data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $user_id = $user_id; // assuming user_id is stored in session
    $job_title = $_POST['job_title'];
    $job_type = $_POST['job_type'];
    $district = $_POST['district'];
    $city = $_POST['city'];
    $job_desc = $_POST['job_desc'];
    $role_desc = $_POST['role_desc'];
    $role_req = $_POST['role_req'];
    $qualification = $_POST['qualification'];
    $salary_range = $_POST['salary_range'];
    $overtime = $_POST['overtime'];
    $date_posted = date('n/j/y');
    $job_cat_id = $_POST['job_cat_id'];
    $job_type_id = $_POST['job_type_id'];
    $exp_level_id = $_POST['exp_level_id'];


    // Prepare SQL statement to insert form data into the 'jobs' table
    $sql = "INSERT INTO jobs (user_id, job_title, job_type, district, city, job_desc, role_desc, role_req, qualification, salary_range, overtime, date_posted, job_cat_id, job_type_id, exp_level_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare a statement
    $stmt = $pdo->prepare($sql);

    // Execute the statement with form data
    $stmt->execute([$user_id, $job_title, $job_type, $district, $city, $job_desc, $role_desc, $role_req, $qualification, $salary_range, $overtime, $date_posted, $job_cat_id, $job_type_id, $exp_level_id]);

    // Save the last inserted job_id in the session
    $_SESSION['job_id'] = $pdo->lastInsertId();

    // Send a JSON response back to the AJAX call
    header('Content-Type: application/json');
    echo json_encode(array("status" => "success", "job_id" => $_SESSION['job_id']));
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
    <title>Add Job Listing</title>
</head>
<style>
    body{
        background-color:#FFEBEE;
        
        }

    .card{
        width:400px;background-color:#fff;border:none;border-radius: 12px;
        }


    .form-control{
        margin-top: 10px;
        height: 48px;
        border: 2px solid #eee;
        border-radius: 10px
        }
    
    .form-control:focus{
        box-shadow: none;
        border: 2px solid #039BE5
        }
    
    .agree-text{
        font-size: 12px
        }
    
    .terms{
        font-size: 12px;
        text-decoration: none;
        color: #039BE5
        }
    
    .confirm-button{
        height: 50px;
        border-radius: 10px
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
<div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card col-lg-8 px-1 py-4">
        <div class="card-body">
        <form id="jobform" method="post">
        <h4 class="card-title mb-3 text-center">Job Listing Form</h4>
            <div class="row">
                <div class="col-12 col-sm-12">

                    <div class="form-group">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="job_title" class="form-control mb-1" placeholder="Job Title">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Working Arrangement</label>
                        <select name="job_type" class="form-control mb-1">
                            <option value="" disabled selected>Working Arrangement</option>
                            <option value="Remote Work">Remote Work</option>
                            <option value="On-Site Work">On-Site Work</option>
                            <option value="Hybrid Work">Hybrid Work</option>
                        </select>
                    </div>

                    <div class="form-row"> 
                        <div class="form-group col-md-6"> 
                        <label for="inputDistrict">District</label>
                            <select id="inputDistrict" name="district" class="form-control mb-1">
                            <option value="" disabled selected>District</option>
                                <?php echo $options1;?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                        <label for="inputCity">City</label>
                            <select id="inputCity" name="city" class="form-control mb-1">
                            <option value="" disabled selected>City</option>
                                <?php echo $options2;?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Job Description</label>
                        <textarea class="form-control" name="job_desc" rows="3" placeholder="Job Description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Role Description</label>
                        <textarea class="form-control" name="role_desc" rows="3" placeholder="Role Description"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Role Requirements</label>
                        <textarea class="form-control" name="role_req" rows="3" placeholder="Role Requirements"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="form-label">Qualification</label>
                        <textarea class="form-control" name="qualification" rows="3" placeholder="Qualification"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Salary Range</label>
                        <input type="text" name="salary_range" class="form-control mb-1" placeholder="Salary Range">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Overtime</label>
                        <select name="overtime" class="form-control mb-1">
                            <option value="" disabled selected>Select Overtime</option>
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="inputCity">Job Category</label>
                            <select id="inputCity" name="job_cat_id" class="form-control mb-1">
                            <option value="" disabled selected>Job Category</option>
                                <?php echo $options3;?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="inputCity">Job Type</label>
                            <select id="inputCity" name="job_type_id" class="form-control mb-1">
                            <option value="" disabled selected>Job Type</option>
                                <?php echo $options4;?>
                            </select>
                    </div>

                    <div class="form-group">
                        <label for="inputCity">Experience Level</label>
                            <select id="inputCity" name="exp_level_id" class="form-control mb-1">
                            <option value="" disabled selected>Experience Level</option>
                                <?php echo $options5;?>
                            </select>
                    </div>

            <div class=" d-flex flex-column text-center px-5 mt-3 mb-3"> <small class="agree-text">By Posting this job you agree to our </small> <a href="#" class="terms">Terms & Conditions</a> </div> <button value="submit" class="btn btn-primary btn-block confirm-button">Submit</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('jobform');

    form.onsubmit = function(event) {
        event.preventDefault(); // Prevent the form from submitting the traditional way

        var formData = new FormData(form);

        fetch('add-joblisting.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // The job_id will be in data.job_id
                console.log('Job ID:', data.job_id);
                window.location.href = 'add-joblisting2.php';
                
                // You can now use this job_id to show the next form or do other actions
            } else {
                // Handle error
                console.error('An error occurred while saving the job.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };
});
</script>
</body>

</html>