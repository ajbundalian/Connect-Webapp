<?php

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  }
  
if ($_SESSION['status'] !== 2) {
    header('Location: applicant-homepage.php');
    exit(); 
  }

$job_id = $_GET['job_id'];

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

$stmt = $pdo->prepare("SELECT * FROM company_profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$companyProfile = $stmt->fetch(PDO::FETCH_ASSOC);

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


$applicantStmt = $pdo->prepare("SELECT COUNT(*) as applicant_count FROM applicant_list WHERE job_id = :job_id");
$applicantStmt->bindParam(':job_id', $job_id);
$applicantStmt->execute();
$applicantCount = $applicantStmt->fetch(PDO::FETCH_ASSOC);

$applicantStmt = $pdo->prepare("SELECT COUNT(*) as applicant_count FROM applicant_list WHERE job_id = :job_id AND status = 'Pending'");
$applicantStmt->bindParam(':job_id', $job_id);
$applicantStmt->execute();
$applicantCount2 = $applicantStmt->fetch(PDO::FETCH_ASSOC);


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

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $sql = "
        SELECT al.user_id, p.first_name, p.last_name, al.status, al.date_applied
        FROM applicant_list al
        JOIN profile p ON al.user_id = p.user_id
        WHERE al.job_id = :job_id
        ORDER BY al.date_applied DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
    $stmt->execute();

    $applicants = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id']) && isset($_POST['action'])) {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];
    $newStatus = ($action == 'interested') ? 'Interested' : 'Not Interested';

    // Prepare your update statement
    $stmt = $pdo->prepare("UPDATE applicant_list SET status = :status WHERE user_id = :user_id");
    $stmt->execute(['status' => $newStatus, 'user_id' => $userId]);

    // You can echo a success message or handle it in your AJAX response
    echo "Status updated successfully.";

    // Prevent further execution of the script (important for AJAX requests)
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
<style>
    body{
    background-color: #545454;
    font-family: "DM Sans", sans-serif;
    font-weight: 400;
    }


    .edit{
    color: white;

    }

    .panel{
    font-family: 'DM Sanse', sans-serif;
    padding: 0;
    border: none;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
  }
  .panel .panel-heading{
      background: #1434A4;
      padding: 15px;
      border-radius: 0;
  }

  .panel .panel-body .table thead tr th:nth-of-type(1){ width: 30%; }
  .panel .panel-body .table thead tr th:nth-of-type(2){ width: 30%; }
  .panel .panel-body .table thead tr th:nth-of-type(3){ width: 10%; }
  .panel .panel-body .table tbody tr td{
      color: #555;
      background: #fff;
      font-size: 20px;
      font-weight: 500;
      padding: 20px;
      vertical-align: middle;
      border-color: #e7e7e7;
  }

  @media (max-width: 768px) {
    .table th, .table td {
        padding: 0.1rem; /* Smaller padding */
        font-size: 0.8rem; /* Smaller font size */
    }
    /* Any other styles you want to adjust for mobile */
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
<div class="container d-flex flex-column justify-content-center align-items-center">
    <div class="card col-10 col-xs-12 mb-2 mt-2 pb-2">
        <div class="user text-center">

            <div class="mt-5 text-center">

                <h4 class="mb-0"><?php echo htmlspecialchars($job['job_title']); ?></h4>
                <span class="text-muted d-block mb-2"><?php echo htmlspecialchars($companyProfile['company_name']); ?></span>
                <span class="text-muted d-block mb-2"><?php echo htmlspecialchars($job['date_posted']); ?></span>
                <a class="btn btn-outline-dark btn-lg edi" href="employer-edit-joblisting.php?job_id=<?php echo $job_id; ?>">Edit Job</a>


            <div class="d-flex justify-content-between align-items-center mt-4 px-4">

                <div class="stats">
                <h5 class="mb-0">Pending Applicants</h5>
                <p><?php echo htmlspecialchars($applicantCount2['applicant_count']); ?></p>
                </div>


                <div class="stats">
                <h5 class="mb-0">Applicants</h5>
                <p><?php echo htmlspecialchars($applicantCount['applicant_count']); ?></p>
                </div>
                
            </div>



    </div>
    </div>
    </div>

    <div class="card col-10 pb-5 pl-4 pr-4 pt-5">
        <h3>Working Arrangement</h3>
        <p><?php echo nl2br(htmlspecialchars($job['job_type'])); ?></p>
        <h3>Location</h3>
        <p><?php echo nl2br(htmlspecialchars($job['district'])); ?>, <?php echo nl2br(htmlspecialchars($job['city'])); ?> </p>
        <h3>Job Description</h3>
        <p><?php echo nl2br(htmlspecialchars($job['job_desc'])); ?></p>
        <h3>Role Description</h3>
        <p><?php echo nl2br(htmlspecialchars($job['role_desc'])); ?></p>
        <h3>Role Requirements</h3>
        <p><?php echo nl2br(htmlspecialchars($job['role_req'])); ?></p>
        <h3>Qualification</h3>
        <p><?php echo nl2br(htmlspecialchars($job['qualification'])); ?></p>
        <h3>Salary Range</h3>
        <p><?php echo nl2br(htmlspecialchars($job['salary_range'])); ?></p>
        <h3>Overtime</h3>
        <p><?php echo nl2br(htmlspecialchars($job['overtime'])); ?></p>
        <h3>Job Category</h3>
        <p><?php echo htmlspecialchars($jobCategoryName); ?></p>
        <h3>Job Type</h3>
        <p><?php echo htmlspecialchars($jobTypeName); ?></p>
        <h3>Experience Required</h3>
        <p><?php echo htmlspecialchars($experienceLevelName); ?></p>
        <h3>Disability Supported</h3>
        <?php foreach ($disabilities as $disability): ?>
            <p><?php echo htmlspecialchars($disability['disability_name']); ?></p>
        <?php endforeach; ?>
        <h3>Accessibility Supported</h3>
        <?php foreach ($accessibilities as $accessibility): ?>
                <i><?php echo htmlspecialchars($accessibility['accessibility_tag']); ?></i>
        <?php endforeach; ?>
    </div>

    <div class="card col-10  pb-5 pl-2 pr-2 pt-5 mb-5 mt-2">
    <div class="panel">
        <div class="panel-body table-responsive rounded-3">
        <div class="table-responsive">
            <table id="yourTableId" class="table rounded-3 text-center">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th id="sortApplicant">Applicant Name<ion-icon name="chevron-expand-sharp"></ion-icon></th>
                        <th id="sortStatus">Status<ion-icon name="chevron-expand-sharp"></ion-icon></th>
                        <th id="sortDate">Date Applied<ion-icon name="chevron-expand-sharp"></ion-icon></th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($applicants as $applicants): ?>
                    <tr applicant-id="<?php echo htmlspecialchars($applicants['user_id']); ?>">
                        <td>
                            <ul class="action-list">
                            <a href="" class="btn btn-primary rounded-3 btn-sm action-button" data-action="interested" data-user-id="<?php echo htmlspecialchars($applicants['user_id']); ?>">Interested</a>
                            <a href="" class="btn btn-danger rounded-3 btn-sm action-button" data-action="not-interested" data-user-id="<?php echo htmlspecialchars($applicants['user_id']); ?>">Not Interested</a>
                            </ul>
                        </td>
                        <td class="applicants"><?php echo htmlspecialchars($applicants['last_name']); ?>, <?php echo htmlspecialchars($applicants['first_name']); ?></td>
                        <td class=""><?php echo htmlspecialchars($applicants['status']); ?></td>
                        <td class=""><?php echo htmlspecialchars($applicants['date_applied']); ?></td>
                        <td><a href="applicant-profile?user_id=<?php echo $applicants['user_id']; ?>" class="btn btn-sm btn-success"><ion-icon name="search"></ion-icon></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>
        <div class="panel-footer rounded-3 text-center">
            <div class="row">
                <div class="col-12 col-sm-12 col-xs-12">Showing All Applicants</div>
            </div>
        </div>
    </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$('.action-button').click(function(e) {
    e.preventDefault();
    var userId = $(this).data('user-id');
    var action = $(this).data('action');

    $.ajax({
        url: '', // Keep this empty to refer to the same page
        type: 'POST',
        data: {
            user_id: userId,
            action: action
        },
        success: function(response) {
            location.reload();
            // Handle the response, like updating the status on the page
        },
        error: function(xhr, status, error) {
            // Handle errors
        }
    });
});
</script>

<script>
    function parseDate(str) {
        var parts = str.trim().split("/");
        if (parts.length === 3) {
            return new Date(parts[2], parts[0] - 1, parts[1]);
        } else {
            return new Date(0); // Invalid date
        }
    }

    function sortTable(tableId, columnNumber, isDate) {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById(tableId);
        switching = true;

        while (switching) {
            switching = false;
            rows = table.getElementsByTagName("TR");

            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[columnNumber];
                y = rows[i + 1].getElementsByTagName("TD")[columnNumber];

                if (isDate) {
                    var date1 = parseDate(x.innerHTML);
                    var date2 = parseDate(y.innerHTML);

                    if (date1 > date2) {
                        shouldSwitch = true;
                        break;
                    }
                } else {
                    if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                        shouldSwitch = true;
                        break;
                    }
                }
            }

            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    document.getElementById('sortApplicant').addEventListener('click', function() {
        sortTable('yourTableId', 1, false);
    });

    document.getElementById('sortStatus').addEventListener('click', function() {
        sortTable('yourTableId', 2, false);
    });

    document.getElementById('sortDate').addEventListener('click', function() {
        sortTable('yourTableId', 3, true);
    });
</script>
</body>
</html>