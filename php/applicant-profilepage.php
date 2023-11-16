<?php 
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['status'] !== 1) {
    header('Location: employer-homepage.php'); 
    exit();
}
$user_id = $_SESSION['user_id'];

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
$pdo = new PDO($dsn, $user, $pass, $options);
// Databse Connection
//SQL Query for email
$query = "SELECT email FROM login WHERE user_id = :user_id";
$stmt =  $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$email = $stmt->fetch(PDO::FETCH_ASSOC);




    // Prepare the SQL query to get the profile_id from the profiles table using the user_id
    $query = "SELECT profile_id FROM `profile` WHERE user_id = :user_id";
    
    // Prepare the statement to prevent SQL injection
    $stmt = $pdo->prepare($query);
    
    // Bind the user_id parameter
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if a profile was found
    if($result) {
        $profileId = $result['profile_id'];
        // You can now use $profileId for further operations
    } else {
        // Handle the case where no profile is associated with the user_id
        echo "No profile found for the given user ID.";
        exit();
    }

// Check if the profile ID is available
if(isset($profileId)) {
    // Prepare the SQL query to get all data from the profiles table using the profile_id
    $query = "SELECT * FROM profile WHERE profile_id = :profileId";

    // Prepare the statement to prevent SQL injection
    $stmt = $pdo->prepare($query);

    // Bind the profile_id parameter
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch all the profile data
    $profileData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if profile data was found
    if($profileData) {
        // You can now use $profileData which is an associative array of all the profile data
    } else {
        // Handle the case where no data was found for the profile_id
        echo "No profile data found for the given profile ID.";
    }
} else {
    // Handle the case where profile_id is not set
    echo "Profile ID is not set.";
}
//query for pronouns, city, district
$cityStmt = $pdo->query("SELECT city_name FROM city"); 
$cities = $cityStmt->fetchAll();
// Fetch districts for dropdown
$districtStmt = $pdo->query("SELECT district_name FROM district");
$districts = $districtStmt->fetchAll();
//Fetch Pronoun for dropdown
$pronounStmt = $pdo->query("SELECT pronoun_name FROM pronoun");
$pronouns = $pronounStmt->fetchAll();

// PHP for updating general info
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_id'])) {
    $profileId = $_POST['profile_id'];

    if (isset($_POST['form_type']) && $_POST['form_type'] == 'general_info') {
        // General Info Update Code
        $stmt = $pdo->prepare("UPDATE profile SET first_name = ?, last_name = ?, contact_number = ?, district = ?, city = ?, headline = ?, pronouns = ? WHERE profile_id = ?");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['contact_number'],
            $_POST['district'],
            $_POST['city'],
            $_POST['headline'],
            $_POST['pronouns'],
            $profileId
        ]);
        // Output success or error message
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] == 'story_info') {
        // Story Info Update Code
        $stmt = $pdo->prepare("UPDATE profile SET story = ? WHERE profile_id = ?");
        $stmt->execute([
            $_POST['story'],
            $profileId
        ]);
        // Output success or error message
    }

    // Check if the update was successful
    if ($stmt->rowCount()) {
        echo 'Profile updated successfully.';
    } else {
        echo 'An error occurred. Profile not updated.';
    }
}

// Fetch work experience entries for the profile
$stmt = $pdo->prepare("SELECT * FROM work_exp WHERE profile_id = ?");
$stmt->execute([$profileId]);
$workExperiences = $stmt->fetchAll(PDO::FETCH_ASSOC);
//

// Fetch education experiences entris for the profile
$stmt = $pdo->prepare("SELECT * FROM educ_exp WHERE profile_id = ?");
$stmt->execute([$profileId]);
$educExperiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch skills from skill table
$stmt = $pdo->prepare("SELECT * FROM skills WHERE profile_id = ?");
$stmt->execute([$profileId]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch disability from disability table
// SQL to join the tables and fetch the disability names for the profile
    $sql = "SELECT d.disability_name, pdj.profile_disability_id 
    FROM disability d 
    INNER JOIN profile_disability_junction pdj ON d.disability_id = pdj.disability_id 
    WHERE pdj.profile_id = :profileId";

    // Prepare the SQL statement
    $stmt = $pdo->prepare($sql);

    // Bind the $profileId parameter
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch all the matching disability names and their junction IDs
    $disabilities = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetching disability to populate the select tag
$stmt = $pdo->prepare("SELECT disability_id, disability_name FROM disability");
$stmt->execute();
$disabilities1 = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Deleting Work Experience and Educational and Disability
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['work_id'])) {
        $workId = $_POST['work_id'];

        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM work_exp WHERE work_id = ?");
        $stmt->execute([$workId]);

        // Check if the delete was successful
        if ($stmt->rowCount()) {
            echo 'Work experience deleted successfully.';
        } else {
            echo 'An error occurred. Work experience not deleted.';
        }
    } elseif (isset($_POST['educ_id'])) {
        $educId = $_POST['educ_id'];

        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM educ_exp WHERE educ_id = ?");
        $stmt->execute([$educId]);

        // Check if the delete was successful
        if ($stmt->rowCount()) {
            echo 'Educational experience deleted successfully.';
        } else {
            echo 'An error occurred. Education experience not deleted.';
        }
    }
    elseif (isset($_POST['skills_id'])) {
        $skillId = $_POST['skills_id'];

        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM skills WHERE skills_id = ?");
        $stmt->execute([$skillId]);

        // Check if the delete was successful
        if ($stmt->rowCount()) {
            echo 'Skill deleted successfully.';
        } else {
            echo 'An error occurred. Skill not deleted.';
        }
    }
    elseif (isset($_POST['profile_disability_id'])) {
        $profileDisabilityId = $_POST['profile_disability_id'];

        // Prepare and execute the delete statement for disability
        $stmt = $pdo->prepare("DELETE FROM profile_disability_junction WHERE profile_disability_id = ?");
        $stmt->execute([$profileDisabilityId]);

        // Check if the delete was successful
        if ($stmt->rowCount()) {
            echo 'Disability entry deleted successfully.';
        } else {
            echo 'An error occurred. Disability entry not deleted.';
        }
    }
}

// PHP Query for Work Experience
// Adding New Work Experience
//Adding Education Experience
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['form_type']) && $_POST['form_type'] == 'work_exp') {
        $company_name = $_POST['company_name'];
        $position = $_POST['position'];
        $start_year = $_POST['start_year'];
        $end_year = $_POST['end_year'];
        $job_description = $_POST['job_description'];
        $profile_id = $profileId; // Assuming $profileId is already set
    
        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO work_exp (profile_id, company_name, position, start_year, end_year, job_description) VALUES (:profile_id, :company_name, :position, :start_year, :end_year, :job_description)";
        $stmt = $pdo->prepare($sql);
    
        // Bind parameters
        $stmt->bindParam(':profile_id', $profile_id);
        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':start_year', $start_year);
        $stmt->bindParam(':end_year', $end_year);
        $stmt->bindParam(':job_description', $job_description);
    
        // Execute query
        try {
            $stmt->execute();
            echo "Record inserted successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] == 'educ_exp') {
        // Extract form data
        $school = $_POST['school'];
        $degree = $_POST['degree'];
        $graduation_year = $_POST['graduation_year'];
        $field_study = $_POST['field_study'];
        $profile_id = $profileId; // Assuming $profileId is already set

        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO educ_exp (profile_id, school, degree, graduation_year, field_study) VALUES (:profile_id, :school, :degree, :graduation_year, :field_study)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':profile_id', $profile_id);
        $stmt->bindParam(':school', $school);
        $stmt->bindParam(':degree', $degree);
        $stmt->bindParam(':graduation_year', $graduation_year);
        $stmt->bindParam(':field_study', $field_study);

        // Execute query
        try {
            $stmt->execute();
            echo "Record inserted successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        }

    elseif (isset($_POST['form_type']) && $_POST['form_type'] == 'skillList') {
        // Extract form data
        $skills = $_POST['skills'];
        $profile_id = $profileId; // Assuming $profileId is already set

        // Prepare SQL statement to prevent SQL injection
        $sql = "INSERT INTO skills (profile_id, skill_name) VALUES (:profile_id, :skill_name)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':profile_id', $profile_id);
        $stmt->bindParam(':skill_name', $skills);


        // Execute query
        try {
            $stmt->execute();
            echo "Record inserted successfully";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        }
    
    elseif (isset($_POST['form_type']) && $_POST['form_type'] == 'disabilityForm'){
        $profileId = $profileId;
        $disabilityId = $_POST['disability'];

    try {
        // Prepare the insert statement
        $stmt = $pdo->prepare("INSERT INTO profile_disability_junction (profile_id, disability_id) VALUES (:profile_id, :disability_id)");
        
        // Bind parameters
        $stmt->bindParam(':profile_id', $profileId, PDO::PARAM_INT);
        $stmt->bindParam(':disability_id', $disabilityId, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Check if the insert was successful
        if ($stmt->rowCount()) {
            echo 'Disability added successfully.';
        } else {
            echo 'An error occurred. Disability not added.';
        }
    } catch (PDOException $e) {
        // Handle potential errors, such as trying to add a duplicate entry
        if ($e->getCode() == 23000) {
            echo 'This disability is already added.';
        } else {
            echo "Error: " . $e->getMessage();
        }
            }
        } else {
            echo 'Invalid request.';
        }
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
<!-- Styling Starts Here -->
<style>
  body {
    background: #f5f5f5;
    margin-top: 0px;
}

.ui-w-80 {
    width : 80px !important;
}

.btn-default {
    border-color: rgba(24, 28, 33, 0.1);
    background  : rgba(0, 0, 0, 0);
    color       : #4E5155;
}

ion-icon {
  color: red;
}

label.btn {
    margin-bottom: 0;
}

.btn-outline-primary {
    border-color: #26B4FF;
    background  : transparent;
    color       : #26B4FF;
}

.btn {
    cursor: pointer;
}

.text-light {
    color: #babbbc !important;
}

.card {
    background-clip: padding-box;
    box-shadow     : 0 1px 4px rgba(24, 28, 33, 0.012);
}

.row-bordered {
    overflow: hidden;
}

.account-settings-fileinput {
    position  : absolute;
    visibility: hidden;
    width     : 1px;
    height    : 1px;
    opacity   : 0;
}

.account-settings-links .list-group-item.active {
    font-weight: bold !important;
}

html:not(.dark-style) .account-settings-links .list-group-item.active {
    background: transparent !important;
}

.account-settings-multiselect~.select2-container {
    width: 100% !important;
}

.light-style .account-settings-links .list-group-item {
    padding     : 0.85rem 1.5rem;
    border-color: rgba(24, 28, 33, 0.03) !important;
}

.light-style .account-settings-links .list-group-item.active {
    color: #4e5155 !important;
}

.material-style .account-settings-links .list-group-item {
    padding     : 0.85rem 1.5rem;
    border-color: rgba(24, 28, 33, 0.03) !important;
}

.material-style .account-settings-links .list-group-item.active {
    color: #4e5155 !important;
}

.dark-style .account-settings-links .list-group-item {
    padding     : 0.85rem 1.5rem;
    border-color: rgba(255, 255, 255, 0.03) !important;
}

.dark-style .account-settings-links .list-group-item.active {
    color: #fff !important;
}

.light-style .account-settings-links .list-group-item.active {
    color: #4E5155 !important;
}

.light-style .account-settings-links .list-group-item {
    padding     : 0.85rem 1.5rem;
    border-color: rgba(24, 28, 33, 0.03) !important;
}

</style>
<!-- Styling Ends Here -->
<body>
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
    <div class="container light-style flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-4">
            Profile
        </h4>
        <div class="card overflow-hidden mb-3">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                <!-- Testing collapsible button-->
                <button class="btn btn-dark btn-lg w-100 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#account-settings-nav" aria-expanded="false" aria-controls="account-settings-nav">
                    Menu
                </button>
                <div class="collapse d-md-block" id="account-settings-nav">
                <!-- Testing collapsible button-->

                    <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action active" data-toggle="list"
                            href="#account-general">General</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list"
                            href="#account-work-experience">Work Experience</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list"
                            href="#account-education">Education</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list"
                            href="#account-other-info">Other Info</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list"
                            href="#account-disability">Disability</a>
                        </div>
                    </div>
                </div>


                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="account-general">
                        <form id="generalInfoForm" method="post" action="applicant-profilepage.php">
                            <div class="card-body media align-items-center">
                                <img src="../image/Connect Favicon (1).svg" alt="photo" class="d-block ui-w-80 mb-2">
                                <div class="media-body ml-4">
                                    <label class="btn btn-outline-primary">
                                        Upload new photo
                                        <input type="file" class="account-settings-fileinput">
                                    </label> &nbsp;
                                    <button type="button" class="btn btn-default md-btn-flat">Reset</button>
                                    <div class="text-light small mt-1">Allowed JPG or PNG. Max size of 800K</div>
                                </div>
                            </div>
                            <hr class="border-light m-0">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" readonly class="form-control mb-1" value="<?php echo htmlspecialchars($email['email']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($profileData['first_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($profileData['last_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="number" name="contact_number" class="form-control mb-1" value="<?php echo htmlspecialchars($profileData['contact_number']); ?>">
                                </div>
                                <div class="form-row"> <!-- This is a flex container in Bootstrap -->
                                  <div class="form-group col-md-6"> <!-- Adjust the col-md-* classes as needed -->
                                    <label for="inputDistrict">District</label>
                                      <select id="inputDistrict" name="district" class="form-control mb-1">
                                        <option selected><?php echo htmlspecialchars($profileData['district']); ?></option>
                                          <?php foreach ($districts as $district): ?>
                                            <option value="<?php echo htmlspecialchars($district['district_name']); ?>">
                                            <?php echo htmlspecialchars($district['district_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                      </select>
                                    </div>
                                  <div class="form-group col-md-6"> <!-- Adjust the col-md-* classes as needed -->
                                    <label for="inputCity">City</label>
                                      <select id="inputCity" name="city" class="form-control mb-1">
                                        <option selected><?php echo htmlspecialchars($profileData['city']); ?></option>
                                          <?php foreach ($cities as $city): ?>
                                            <option value="<?php echo htmlspecialchars($city['city_name']); ?>">
                                            <?php echo htmlspecialchars($city['city_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                      </select>
                                    </div>
                                  </div>
                                <div class="form-group">
                                    <label class="form-label">Headline</label>
                                    <input type="text" name="headline" class="form-control mb-1" value="<?php echo htmlspecialchars($profileData['headline']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pronouns</label>
                                    <select id="inputPronouns" name="pronouns" class="form-control mb-1">
                                        <option selected><?php echo htmlspecialchars($profileData['pronouns']); ?></option>
                                            <?php foreach ($pronouns as $pronoun): ?>
                                            <option value="<?php echo htmlspecialchars($pronoun['pronoun_name']); ?>">
                                            <?php echo htmlspecialchars($pronoun['pronoun_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                      </select>
                                </div>
                                <input type="hidden" name="form_type" value="general_info">
                                <div class="text-right mt-3 mb-3">
                                  <button type="button" type="submit" id="saveGeneralInfo" class="btn btn-primary">Save changes</button>&nbsp;
                                  <button type="button" id="cancelGeneralInfo" class="btn btn-default">Cancel</button>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!-- First Tab Ends Here-->

                        <!-- Second Tab Starts Here-->
                        
                        <div class="tab-pane fade" id="account-work-experience">
                            <div class="card-body pb-2">
                            <h6 class="display-6">Work Experience</h6>
                        <!-- HTML Template for Work Experience Loop for every work_exp -->
                        <?php foreach ($workExperiences as $workExperience): ?>
                        <div class="d-flex justify-content-between align-items-center" work_id="<?php echo $workExperience['work_id']; ?>">
                            <div class="w-100">
                                <div class="container">
                                    <div class="row justify-content-between">
                                        <div class="col text-left p-1"><p class="h4"><strong><?php echo htmlspecialchars($workExperience['position']); ?></strong></p></div>
                                        <div class="col text-right p-1"><p class="h6"><?php echo htmlspecialchars($workExperience['start_year']); ?> &ndash; <?php echo htmlspecialchars($workExperience['end_year']); ?></p></div>
                                    </div>
                                </div>
                                <div class="p-1"><p class="h5"><?php echo htmlspecialchars($workExperience['company_name']); ?></p></div>
                                <div class="p-2"><p class="h6"><?php echo htmlspecialchars($workExperience['job_description']); ?></p></div>
                                <div class="text-right mb-3">
                                    <button onclick="deleteWorkExperience(this.getAttribute('data-work-id'))" data-work-id="<?php echo $workExperience['work_id']; ?>" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete this skill">
                                    <ion-icon name="trash-outline" size="small"></ion-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                            
                         <!-- HTML Template for Work Experience Loop for every work_exp -->

                        <!-- HTML code for work exp form -->
                            <div id="workExperienceForm" class="card mb-3" style="display:block">
                                <div class="card-body" id="workExperienceForm2" class="card" style="display">
                                <h5 class="card-title">Add Work Experience</h5>
                                <form id="workExperience" >
                                    <input type="hidden" name="form_type" value="work_exp">
                                    <input type="text" name="company_name" class="form-control mb-2" placeholder="Company" required>
                                    <input type="text" name="position" class="form-control mb-2" placeholder="Position/Title" required>
                                    <div class="d-flex justify-content-between">
                                    <input type="number" name="start_year" class="form-control mb-2" placeholder="Start Year" required>
                                    <input type="text" name="end_year" class="form-control mb-2" placeholder="End Year">
                                    </div>
                                    <textarea class="form-control mb-2" name="job_description" placeholder="Job Description" rows="3"></textarea>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-secondary">Cancel</button>
                                </form>
                                </div>
                            </div>
                            </div>
                            </div> 


                        <!-- Second Tab Ends Here-->

                        <!-- Third Tab Starts Here-->
                        <div class="tab-pane fade" id="account-education">
                            <div class="card-body pb-6">
                            <h6 class="display-6">Education</h6>
                            <?php foreach ($educExperiences as $educExperience): ?>
                            <div class="d-flex justify-content-between align-items-center" educ_id="<?php echo $educExperience['educ_id']; ?>">
                                <div class="w-100">
                                    <div class="container">
                                        <div class="row justify-content-between">
                                            <div class="col text-left p-1"><p class="h4"><strong><?php echo htmlspecialchars($educExperience['degree']); ?></strong></p></div>
                                            <div class="col text-right p-1"><p class="h6"><?php echo htmlspecialchars($educExperience['graduation_year']); ?></p></div>
                                        </div>
                                    </div>
                                    <div class="p-1"><p class="h5"><?php echo htmlspecialchars($educExperience['degree']); ?></p></div>
                                    <div class="p-2"><p class="h6"><?php echo htmlspecialchars($educExperience['field_study']); ?></p></div>
                                    <div class="text-right mb-3">
                                        <button onclick="deleteEducExperience(this.getAttribute('data-educ-id'))" data-educ-id="<?php echo $educExperience['educ_id']; ?>" class="btn btn-outline-danger btn-sm">
                                        <ion-icon name="trash-outline" size="small"></ion-icon>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        
                            <?php endforeach; ?>
                            <div id="educExperienceForm" class="card mb-3" style="display:block">
                                <div class="card-body" id="educExperienceForm2" class="card" style="display">
                                <h5 class="card-title">Add Education</h5>
                                <form id="educExperience" >
                                    <input type="hidden" name="form_type" value="educ_exp">
                                    <input type="text" name="school" class="form-control mb-2" placeholder="School" required>
                                    <input type="text" name="degree" class="form-control mb-2" placeholder="Degree">
                                    <input type="number" name="graduation_year" class="form-control mb-2" placeholder="graduation year" required>
                                    <input class="form-control mb-2" name="field_study" placeholder="Field of Study">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-secondary">Cancel</button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="account-other-info">
                            <div class="card-body pb-2">
                            <h5 class="card-title">Other Information</h5>
                            <hr class="border-light m-0 mb-2">
                            <form id="storyInfoForm" method="post" action="applicant-profilepage.php">
                                <input type="hidden" name="form_type" value="story_info">
                                <div class="mb-3">
                                    <label for="story" class="form-label">Tell Your Story:</label>
                                    <textarea class="form-control mb-2" name="story" id="story" rows="3"><?php echo htmlspecialchars($profileData['story']); ?></textarea>
                                    <input type="hidden" name="form_type" value="story_info">
                                    <div class="d-flex w-100 justify-content-end">
                                        <button type="button" type="submit" id="saveStory" class="btn btn-primary">Save</button>&nbsp;
                                        <button type="button" id="cancelStory" class="btn btn-default">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            <h5 class="card-title">Skills</h5>
                            <hr class="border-light m-0 mb-2">
                            <?php foreach ($skills as $skills): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2" skills_id="<?php echo $skills['skills_id']; ?>">
                                <div class="w-100">
                                <div class="row justify-content-between align-items-center">
                                        <div class="col p-1">
                                            <p class="h5"><strong><?php echo htmlspecialchars($skills['skill_name']); ?></strong></p>
                                        </div>
                                        <div class="col-auto">
                                            <button onclick="deleteSkill('<?php echo $skills['skills_id']; ?>')" skills_id="<?php echo $skills['skills_id']; ?>" class="btn btn-outline-danger btn-sm">
                                            <ion-icon name="trash-outline" size="small"></ion-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div id="skillListForm" class="card mb-3" style="display:block">
                                <div class="card-body" id="skillListForm2" class="card" style="display">
                                <h5 class="card-title">Add Skills</h5>
                                <form id="skillsForm" >
                                    <input type="hidden" name="form_type" value="skillList">
                                    <input class="form-control mb-2" name="skills" placeholder="Skills" required>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn btn-secondary">Cancel</button>
                                    </form>
                                    </div>
                                </div>

                            </div>
                        </div>
 
                        <div class="tab-pane fade" id="account-disability">
                            <div class="card-body">
                                <h5 class="card-title">Disability</h5>
                                <hr class="border-light m-0 mb-2">
                                <?php foreach ($disabilities as $disabilities): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2" disability_id="<?php echo $disabilities['profile_disability_id']; ?>">
                                        <div class="w-100">
                                        <div class="row justify-content-between align-items-center">
                                                <div class="col p-1">
                                                    <p class="h5"><strong><?php echo htmlspecialchars($disabilities['disability_name']); ?></strong></p>
                                                </div>
                                                <div class="col-auto">
                                                    <button onclick="deleteDisability('<?php echo $disabilities['profile_disability_id']; ?>')" disability_id="<?php echo $disabilities['profile_disability_id']; ?>" class="btn btn-outline-danger btn-sm">
                                                    <ion-icon name="trash-outline" size="small"></ion-icon>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>

                                <div id="DisabilityForm" class="card mb-3" style="display:block">
                                <div class="card-body" id="DisabilityForm2" class="card" style="display">
                                <h5 class="card-title">Add Your Disability</h5>
                                <h6 class="card-title">Adding disability can get you recommended jobs based on your disability</h6>
                                <form id="disabilityForm" >
                                    <input type="hidden" name="form_type" value="disabilityForm">
                                    <select id="inputDisability" name="disability" class="form-control mb-1">
                                        <option selected>Select your disability</option>
                                        <?php foreach ($disabilities1 as $disability): ?>
                                        <option value="<?php echo htmlspecialchars($disability['disability_id']); ?>">
                                            <?php echo htmlspecialchars($disability['disability_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                        </select>
                                        <div class="text-right mt-3 mb-3">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn btn-secondary">Cancel</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                                
                            
                            </div>
                        </div>
                        

                </div>
            </div>
        </div>
    </div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="applicant-profile.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
    // JS for general info
    document.getElementById('saveGeneralInfo').addEventListener('click', function() {
    // Get form data
    var formData = new FormData(document.getElementById('generalInfoForm'));
  
    // Add profileId to formData
    formData.append('profile_id', '<?php echo $profileId; ?>');
  
    // AJAX request to the server
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'applicant-profilepage.php', true);
    xhr.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        // Handle response here
        alert('Profile updated successfully!');
        //location.reload(); // Reload the page to show the updated info
            }
        };
        xhr.send(formData);
    });
  
    document.getElementById('cancelGeneralInfo').addEventListener('click', function() {
    location.reload(); // Simply reload the page
    });

    </script>


    <script>

    function deleteWorkExperience(workId) {
    var workExperienceSection = document.querySelector('#account-work-experience');
    var workExperienceEntry = workExperienceSection.querySelector(`[work_id="${workId}"]`);
    if (confirm('Are you sure you want to delete this work experience?')) {
    // AJAX request to the server for deletion
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'applicant-profilepage.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
    if (this.status == 200) {
        var workExperienceEntry = document.querySelector('[work_id="' + workId + '"]');
        workExperienceEntry.parentNode.removeChild(workExperienceEntry);
        alert('Work experience deleted successfully!');
        location.reload(); // Reload the page to update the list
    }
    else {
        // Handle error here
        alert('Error deleting work experience.');
      }
    };
    xhr.send('work_id=' + workId);
    }
} 

    </script>
<!-- For Adding New Job Experience -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    var workExperienceForm = document.getElementById('workExperienceForm');

    // Handle form submission
    var workExperienceFormElement = document.getElementById('workExperience');
    if (workExperienceFormElement) {
        workExperienceFormElement.onsubmit = function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            fetch('applicant-profilepage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Handle the response here
                location.reload();
            });
        };
    } else {
        console.error('Cannot find the Work Experience form element');
    }
});
    </script>

<script>
    function deleteEducExperience(educId) {
    var educExperienceSection = document.querySelector('#account-education');
    var educExperienceEntry = educExperienceSection.querySelector(`[educ_id="${educId}"]`);
    if (confirm('Are you sure you want to delete this educational experience?')) {
    // AJAX request to the server for deletion
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'applicant-profilepage.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
    if (this.status == 200) {
        var educExperienceEntry = document.querySelector('[educ_id="' + educId + '"]');
        educExperienceEntry.parentNode.removeChild(educExperienceEntry);
        alert('Educational experience deleted successfully!');
        location.reload(); // Reload the page to update the list
    }
    else {
        // Handle error here
        alert('Error deleting educational experience.');
      }
    };
    xhr.send('educ_id=' + educId);
    }
    } 

    </script>

<!-- For New Education Experience -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var educExperienceForm = document.getElementById('educExperienceForm');

    // Handle form submission
    var educExperienceFormElement = document.getElementById('educExperience');
    if (educExperienceFormElement) {
        educExperienceFormElement.onsubmit = function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            fetch('applicant-profilepage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Handle the response here
                location.reload();
            });
        };
    } else {
        console.error('Cannot find the Educational Experience form element');
    }
    });
    </script>

<!-- Updating Your Story -->
<script>
    // JS for your story
    document.getElementById('saveStory').addEventListener('click', function() {
    // Get form data
    var formData = new FormData(document.getElementById('storyInfoForm'));
  
    // Add profileId to formData
    formData.append('profile_id', '<?php echo $profileId; ?>');
  
    // AJAX request to the server
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'applicant-profilepage.php', true);
    xhr.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        // Handle response here
        alert('Story updated successfully!');
        location.reload(); // Reload the page to show the updated info
            }
        };
        xhr.send(formData);
    });
  
    document.getElementById('cancelStory').addEventListener('click', function() {
    location.reload(); // Simply reload the page
    });

    </script>
 <!-- Adding New Skills -->
 <script>
    document.addEventListener('DOMContentLoaded', function () {
    var skillListForm = document.getElementById('skillListForm');

    // Handle form submission
    var skillListFormElement = document.getElementById('skillsForm');
    if (skillListFormElement) {
        skillListFormElement.onsubmit = function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            fetch('applicant-profilepage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Handle the response here
                alert('Skill successfully!');
                location.reload();
            });
        };
    } else {
        console.error('Cannot find the Educational Experience form element');
    }
    });
    </script>
<!-- Deleting Skills -->
<script>
    function deleteSkill(skillId) {
    var skillSection = document.querySelector('#account-other-info');
    var skillEntry = skillSection.querySelector(`[skills_id="${skillId}"]`);
    if (confirm('Are you sure you want to delete this skill?')) {
    // AJAX request to the server for deletion
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'applicant-profilepage.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
    if (this.status == 200) {
        var skillEntry = document.querySelector('[skills_id="' + skillId + '"]');
        skillEntry.parentNode.removeChild(skillEntry);
        alert('Skill deleted successfully!');
        location.reload(); // Reload the page to update the list
    }
    else {
        // Handle error here
        alert('Error deleting educational experience.');
      }
    };
    xhr.send('skills_id=' + skillId);
    }
    } 

    </script>
<!-- JS for deleting disability -->
    <script>
    function deleteDisability(profileDisabilityId) {
        if (confirm('Are you sure you want to delete this disability?')) {
            // AJAX request to the server for deletion
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'applicant-profilepage.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (this.status == 200) {
                    alert('Disability deleted successfully!');
                    location.reload(); // Reload the page to update the list
                } else {
                    // Handle error here
                    alert('Error deleting disability.');
                }
            };
            xhr.send('profile_disability_id=' + profileDisabilityId);
        }
    }
    </script>

<!-- JS to handle disability submission -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var disabilityForm = document.getElementById('disabilityForm');

    // Handle form submission
    var disabilityFormElement = document.getElementById('disabilityForm');
    if (disabilityFormElement) {
        disabilityFormElement.onsubmit = function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            fetch('applicant-profilepage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Handle the response here
                alert('Disability added successfully!');
                location.reload();
            });
        };
    } else {
        console.error('Cannot find the Educational Experience form element');
    }
    });
</script>
</body>
</html>