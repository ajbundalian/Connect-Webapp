<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
  </head>

<?php
session_start();
// Redirect to login if not logged in
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

// Fetch user information
$pdo = new PDO($dsn, $user, $pass, $options);
$stmt = $pdo->prepare("SELECT * FROM company_profile WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
// Fetching Job info
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($jobs) === 0) {
  // No jobs found for this user_id
  echo "<tr><td colspan='4'>No jobs found.</td></tr>";
} else {
  foreach ($jobs as $job) {
      // Count the number of applicants for the job
      $applicantStmt = $pdo->prepare("SELECT COUNT(*) as applicant_count FROM applicant_list WHERE job_id = :job_id");
      $applicantStmt->bindParam(':job_id', $job['job_id']);
      $applicantStmt->execute();
      $applicantCount = $applicantStmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['job_id'])) {
      $job_id = $_POST['job_id'];

      // Prepare the statement to prevent SQL injection
      $stmt = $pdo->prepare("DELETE FROM jobs WHERE job_id = :job_id");
      $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);

      if ($stmt->execute()) {
          // Send a JSON response back to the AJAX request
          echo json_encode(['success' => true]);
      } else {
          echo json_encode(['success' => false]);
      }
  } else {
      echo json_encode(['success' => false]);
  }
  exit();
}

}

?>

<style>
  body{
    font-family: "DM Sans", sans-serif;
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
  .panel .panel-heading .btn{
      color: #1434A4;
      background-color: #000;
      font-size: 14px;
      font-weight: 600;
      padding: 7px 15px;
      border: none;
      border-radius: 0;
      transition: all 0.3s ease 0s;
  }
  .panel .panel-heading .btn:hover{ box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); }
  .panel .panel-heading .form-horizontal .form-group{ margin: 0; }
  .panel .panel-heading .form-horizontal label{
      color: #7200F3;
      margin-right: 10px;
  }
  .panel .panel-heading .form-horizontal .form-control{
      display: inline-block;
      width: 80px;
      border: none;
      border-radius: 0;
  }
  .panel .panel-heading .form-horizontal .form-control:focus{
      box-shadow: none;
      border: none;
  }
  .panel .panel-body{
      padding: 0;
      border-radius: 0;
  }
  .panel .panel-body .table thead tr th{
      color: #fff;
      background: #1434A4;
      font-size: 17px;
      font-weight: 700;
      padding: 12px;
      border-bottom: none;
      vertical-align: middle;
  }
  .panel .panel-body .table thead tr th:nth-of-type(1){ width: 120px; }
  .panel .panel-body .table thead tr th:nth-of-type(2){ width: 50%; }
  .panel .panel-body .table tbody tr td{
      color: #555;
      background: #fff;
      font-size: 20px;
      font-weight: 500;
      padding: 20px;
      vertical-align: middle;
      border-color: #e7e7e7;
  }
  .panel .panel-body .table tbody tr:nth-child(odd) td{ background: #f5f5f5; }
  .panel .panel-body .table tbody .action-list{
      padding: 0;
      margin: 0;
      list-style: none;
  }
  .panel .panel-body .table tbody .action-list li{ display: inline-block; }
  .panel .panel-body .table tbody .action-list li a{
      color: #fff;
      font-size: 20px;
      line-height: 28px;
      height: 28px;
      width: 33px;
      padding: 0;
      border-radius: 0;
      transition: all 0.3s ease 0s;
  }
  .panel .panel-body .table tbody .action-list li a:hover{ box-shadow: 0 0 5px #ddd; }
  .panel .panel-footer{
      color: #fff;
      background: #1434A4;
      font-size: 20px;
      line-height: 33px;
      padding: 25px 15px;
      border-radius: 0;
  }
  th {
  cursor: pointer;
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
            <a class="nav-link mx-2 active h5" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-2 h5" href="#">Jobs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-2 h5" href="#">Applicants</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link mx-2 dropdown-toggle h5" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Account
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item h5" href="#">Profile</a></li>
              <li><a class="dropdown-item h5" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
    </nav>

    <!--Nav Ends Here-->

    <!-- Jumbotron -->
    <div class="jumbotron jumbotron-fluid mt-5 mb-5">
      <div class="container text-center">
        <h1 class="display-4 mb-4">Welcome,</h1>
        <p class="lead display-5"> <?php echo htmlspecialchars($userInfo['company_name']); ?> </p>
          <a class="btn btn-outline-dark btn-lg" href="#" role="button"><ion-icon name="add" size="small"></ion-icon>Add A New Listing</a>
      </div>
    </div>
            
          
    <!-- Jumbotron -->
    <!-- Table for Jobs-->
    <div class="container-fluid col-10 col-xs-12 mb-5 text-center">
      <div class="row">
          <div class="col-12 col-md-offset-1 col-md-12">
              <div class="panel">
                  <div class="panel-body table-responsive rounded-3">
                      <table class="table rounded-3">
                          <thead>
                              <tr>
                                  <th>Action</th>
                                  <th onclick="sortTable('job-title')">Job Title <ion-icon name="chevron-expand-sharp"></ion-icon></th>
                                  <th onclick="sortTable('applicants', true)">Applicants <ion-icon name="chevron-expand-sharp"></ion-icon></th>
                                  <th>View</th>
                              </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($jobs as $jobs): ?>
                              <tr data-job-id="<?php echo htmlspecialchars($jobs['job_id']); ?>">
                                  <td>
                                      <ul class="action-list">
                                      <li><a href= "" class="btn btn-primary rounded-3 btn-lg"><ion-icon name="create-outline"></ion-icon></a></li>
                                      <li><a href="" class="btn btn-danger rounded-3 btn-lg"><ion-icon name="trash-outline"></ion-icon></a></li>
                                      </ul>
                                  </td>
                                  <td class="job-title"><?php echo htmlspecialchars($jobs['job_title']); ?></td>
                                  <td class="applicants"><?php echo htmlspecialchars($applicantCount['applicant_count']); ?></td>
                                  <td><a href="#" class="btn btn-sm btn-success"><ion-icon name="search"></ion-icon></a></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
                  <div class="panel-footer rounded-3">
                      <div class="row">
                          <div class="col-12 col-sm-12 col-xs-12">Showing All Job Listing</div>
                      </div>
                  </div>

              </div>
          </div>
      </div>
  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<!-- Script for sorting the jobs -->
<script>
  function sortTable(column, isNumeric = false) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  var table = document.querySelector('.table.rounded');
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until no switching has been done: */
  while (switching) {
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      /* Get the two elements you want to compare, one from current row and one from the next: */
      x = rows[i].getElementsByClassName(column)[0];
      y = rows[i + 1].getElementsByClassName(column)[0];
      /* Check if the two rows should switch place, based on the direction, asc or desc: */
      if (isNumeric) {
        if ((dir == "asc" && parseInt(x.innerHTML) > parseInt(y.innerHTML)) || (dir == "desc" && parseInt(x.innerHTML) < parseInt(y.innerHTML))) {
          shouldSwitch = true;
          break;
        }
      } else {
        if ((dir == "asc" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || (dir == "desc" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase())) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount++;
    } else {
      /* If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>

<!-- For deleting job listing --> 
<script>
  window.addEventListener('DOMContentLoaded', (event) => {
  // Get all delete buttons
    const deleteButtons = document.querySelectorAll('.btn-danger');

    // Add a click event listener to each delete button
    deleteButtons.forEach(function(button) {
      button.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the default link behavior
        const jobId = this.closest('tr').dataset.jobId; // Get the job_id from the data attribute of the tr

        if (confirm('Are you sure you want to delete this job?')) {
          // Send an AJAX request to the PHP script to delete the job
          fetch('employer-homepage.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'job_id=' + jobId
          })
          .then(response => response.json())
          .then(data => {
            if(data.success) {
              // If the deletion was successful, remove the tr from the table
              this.closest('tr').remove();
            } else {
              alert('There was an error deleting the job.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
        }
      });
    });
  });
</script>
</html>