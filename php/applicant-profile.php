<?php
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

if ($_SESSION['status'] !== 2) {
header('Location: applicant-homepage.php'); 
exit();
}

if (isset($_SESSION['job_id'])) {
unset($_SESSION['job_id']);
exit();
}

$user_id1 = $_GET['user_id'] ?? null;

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

try {
  // Fetch data from the profile table
  $infoStmt = $pdo->prepare("SELECT * FROM login WHERE user_id = :user_id");
  $infoStmt->bindParam(':user_id', $user_id1);
  $infoStmt->execute();
  $info = $infoStmt->fetch(PDO::FETCH_ASSOC);


  $profileStmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = :user_id");
  $profileStmt->bindParam(':user_id', $user_id1);
  $profileStmt->execute();
  $profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

  if ($profile) {
      $profile_id = $profile['profile_id'];

      // Fetch work experience
      $workExpStmt = $pdo->prepare("SELECT * FROM work_exp WHERE profile_id = :profile_id");
      $workExpStmt->bindParam(':profile_id', $profile_id);
      $workExpStmt->execute();
      $workExperiences = $workExpStmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch educational experience
      $educExpStmt = $pdo->prepare("SELECT * FROM educ_exp WHERE profile_id = :profile_id");
      $educExpStmt->bindParam(':profile_id', $profile_id);
      $educExpStmt->execute();
      $educationalExperiences = $educExpStmt->fetchAll(PDO::FETCH_ASSOC);

      // Fetch skills
      $skillsStmt = $pdo->prepare("SELECT * FROM skills WHERE profile_id = :profile_id");
      $skillsStmt->bindParam(':profile_id', $profile_id);
      $skillsStmt->execute();
      $skills = $skillsStmt->fetchAll(PDO::FETCH_ASSOC);

      $disabilitiesStmt = $pdo->prepare("
            SELECT d.disability_name
            FROM profile_disability_junction pdj
            JOIN disability d ON pdj.disability_id = d.disability_id
            WHERE pdj.profile_id = :profile_id
        ");
        $disabilitiesStmt->bindParam(':profile_id', $profile_id);
        $disabilitiesStmt->execute();
        $disabilities = $disabilitiesStmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      echo "Profile not found for the given user ID.";
  }

} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
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
    <title>Applicant Profile</title>
</head>
<style>
    body{
        font-family: 'DM Sans', sans-serif;
    }
    .profile-header {
    transform: translateY(5rem);
    margin-bottom: 2rem;
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
            <a class="nav-link mx-2 h5" href="#">Applicants</a>
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
<div class="row py-5 px-4 mb-5">
    <div class="col-xl-8 col-md-6 col-sm-10 mx-auto">

        <!-- Profile widget -->
        <div class="bg-white shadow rounded overflow-hidden">
            <div class="px-4 pt-0 pb-4 bg-dark">
                <div class="media align-items-end profile-header">
                    <div class="profile mr-3 mt-3 mb-5"><img src="../image/<?php echo htmlspecialchars($profile['photo']); ?>" alt="..." height="150" width="150" class="rounded mb-2 img-thumbnail"></div>
                    <div class="media-body mb-5 text-white">
                        <h4 class="mt-0 mb-0"><?php echo htmlspecialchars($profile['last_name']); ?>,  <?php echo htmlspecialchars($profile['first_name']); ?></h4>
                        <h5 class="mt-0 mb-0"><?php echo htmlspecialchars($profile['headline']); ?></h5>
                        <p class="small mb-4"><ion-icon name="location-outline"></ion-icon></p>
                    </div>
                </div>
            </div>

        <div class="card col-12 pb-5 pl-4 pr-4 pt-5">
          <h4><strong>Email</strong></h4>
          <p class="mb-3"><?php echo htmlspecialchars($info['email']); ?></p>
          <h4 class="mt-4"><strong>Contact Number</strong></h4>
          <p class="mb-3"><?php echo htmlspecialchars($profile['contact_number']); ?></p>
          <h4 class="mt-4"><strong>Story</strong></h4>
          <p class="mb-3"><?php echo htmlspecialchars($profile['story']); ?></p>
          <h4 class="mt-4"><strong>Work Experience</strong></h4>
          <?php if (empty($workExperiences)): ?>
            <div class="col text-left p-1"><p class="h5">No work experiences were found.</p></div>
          <?php else: ?>
            <?php foreach ($workExperiences as $workExperience): ?>
              <div class="card rounded-3 text-white bg-dark d-flex justify-content-between align-items-center mb-2" work_id="<?php echo $workExperience['work_id']; ?>">
                <div class="w-100">
                    <div class="container">
                        <div class="row justify-content-between">
                            <div class="col text-left p-1"><p class="h5"><strong><?php echo htmlspecialchars($workExperience['position']); ?></strong></p></div>
                            <div class="col text-right p-1"><p class="h6"><?php echo htmlspecialchars($workExperience['start_year']); ?> &ndash; <?php echo htmlspecialchars($workExperience['end_year']); ?></p></div>
                        </div>
                    </div>
                    <div class="pr-1 pl-1"><p class="h6"><?php echo htmlspecialchars($workExperience['company_name']); ?></p></div>
                    <div class="pr-2 pl-2"><p class="h6"><?php echo htmlspecialchars($workExperience['job_description']); ?></p></div>
                </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <h4 class="mt-4"><strong>Educational Background</strong></h4>
          <?php if (empty($workExperiences)): ?>
            <div class="col text-left p-1"><p class="h5">No educational experiences were found.</p></div>
          <?php else: ?>
            <?php foreach ($educationalExperiences as $educExperience): ?>
              <div class="card rounded-3 text-white bg-dark d-flex justify-content-between align-items-center mb-2" educ_id="<?php echo $educExperience['educ_id']; ?>">
                  <div class="w-100">
                      <div class="container">
                          <div class="row justify-content-between">
                              <div class="col text-left p-1"><p class="h5"><strong><?php echo htmlspecialchars($educExperience['degree']); ?></strong></p></div>
                              <div class="col text-right p-1"><p class="h6"><?php echo htmlspecialchars($educExperience['graduation_year']); ?></p></div>
                          </div>
                      </div>
                      <div class="p-1"><p class="h6"><?php echo htmlspecialchars($educExperience['degree']); ?></p></div>
                      <div class="p-2"><p class="h6"><?php echo htmlspecialchars($educExperience['field_study']); ?></p></div>
                  </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <h4><strong>Skills</strong></h4>
          <?php if (empty($workExperiences)): ?>
            <div class="col text-left p-1"><p class="h5">No skills were found.</p></div>
          <?php else: ?>
            <?php foreach ($skills as $skills): ?>
              <div class="d-flex justify-content-between align-items-center mb-2" skills_id="<?php echo $skills['skills_id']; ?>">
                  <div class="w-100">
                  <div class="row justify-content-between align-items-center">
                          <div class="col pr-5">
                              <p class="h6 pr-4"><strong>• <?php echo htmlspecialchars($skills['skill_name']); ?></strong></p>
                          </div>
                      </div>
                  </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <h4 class="mt-4"><strong>Disability</strong></h4>
          <?php if (empty($workExperiences)): ?>
            <div class="col text-left p-1"><p class="h5">No disability were found.</p></div>
          <?php else: ?>
          <?php foreach ($disabilities as $disabilities): ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="w-100">
                <div class="row justify-content-between align-items-center">
                        <div class="col pr-5">
                            <p class="h6 pr-4"><strong>• <?php echo htmlspecialchars($disabilities['disability_name']); ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div><!-- End profile widget --> 

</div>
</div>



</body>
</html>