


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="../css/applicant-profilepage.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="/image/Connect Favicon (1).svg">
    <title>Home</title>
</head>
<body>
<div class="mother-ship">

<!--Navigation Starts Here Here-->
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
                <a class="nav-link mx-2 h5" href="#">Company</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link mx-2 dropdown-toggle h5" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Account
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <li><a class="dropdown-item h5" href="#">Profile</a></li>
                  <li><a class="dropdown-item h5" href="#">Logout</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
        </nav>
<!--Navigation Ends Here-->


<?php

session_start(); // Start the session at the beginning of the script


$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "connect";

// Create connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

// Check if the session variable for profile_id is set, otherwise redirect or handle the error
if (!isset($_SESSION['profile_id'])) {
    // Redirect to the login page or handle the error as appropriate
    exit('Error: Profile ID is not set in the session. Please log in.'); // Or use header('Location: login.php');
}

$profileId = $_SESSION['profile_id']; // Use the profile_id from the session



// Fetch user profile data
$profileStmt = $conn->prepare("SELECT first_name, last_name, city, district FROM 'profile' WHERE profile_id = :profileId"); // 
$profileStmt->bindParam(':profileId', $profileId);
$profileStmt->execute();
// ...

// Fetch mobility impairment data
$mobilityImpairmentStmt = $conn->prepare("
    SELECT d.disability_name 
    FROM profile_disability_junction pdj
    JOIN disability d ON pdj.disability_id = d.disability_id
    WHERE pdj.profile_id = :profileId
");
$mobilityImpairmentStmt->bindParam(':profileId', $profileId);
$mobilityImpairmentStmt->execute();
$mobilityImpairments = $mobilityImpairmentStmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch work experience data
$workExpStmt = $conn->prepare("SELECT company_name, position, start_year, end_year, job_description FROM work_exp WHERE profile_id = :profileId");
$workExpStmt->bindParam(':profileId', $profileId);
$workExpStmt->execute();
$workExperiences = $workExpStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch education data
$educExpStmt = $conn->prepare("SELECT school, degree, field_study, graduation_year FROM educ_exp WHERE profile_id = :profileId");
$educExpStmt->bindParam(':profileId', $profileId);
$educExpStmt->execute();
$educationEntries = $educExpStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch skills data
$skillsStmt = $conn->prepare("SELECT skill_name FROM skills WHERE profile_id = :profileId");
$skillsStmt->bindParam(':profileId', $profileId);
$skillsStmt->execute();
$skills = $skillsStmt->fetchAll(PDO::FETCH_ASSOC);

// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission to update profile data
    // Example: Update profile information
    // ... Update profile_table ...

    // Update work experience
    // Assuming a single work experience entry for demonstration
    $updateWorkExpStmt = $conn->prepare("UPDATE work_exp SET company_name=:company_name, position=:position, start_year=:start_year, end_year=:end_year, job_description=:job_description WHERE profile_id = :profileId AND work_id=:work_id");
    // Bind parameters and execute
    // ... Bind parameters from $_POST for work experience ...

    // Update education
    // Assuming a single education entry for demonstration
    $updateEducExpStmt = $conn->prepare("UPDATE educ_exp SET school=:school, degree=:degree, field_study=:field_study, graduation_year=:graduation_year WHERE profile_id = :profileId AND educ_id=:educ_id");
    // Bind parameters and execute
    // ... Bind parameters from $_POST for education ...

    // Update skills
    // Assuming a single skill for demonstration
    $updateSkillsStmt = $conn->prepare("UPDATE skills SET skill_name=:skill_name WHERE profile_id = :profileId AND skill_id=:skill_id");
    // Bind parameters and execute
    // ... Bind parameters from $_POST for skills ...

    // Redirect or display a success message
    // ... Refresh the data or redirect ...
}

?>



        <!-- Profile Header -->
        <div class="profile-header">
        <img src="../image/Connect Favicon (2).svg" alt="Profile Picture" class="profile-picture">
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($profile['first_name']) . " " . htmlspecialchars($profile['last_name']); ?></h2>
            <p><?php echo htmlspecialchars($profile['city']) . ", " . htmlspecialchars($profile['district']); ?></p>
            <?php foreach ($mobilityImpairments as $impairment): ?>
                <p><?php echo htmlspecialchars($impairment['disability_name']); ?></p>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Edit Profile Button -->
    <button onclick="document.getElementById('editProfileForm').style.display='block';">Edit Profile</button>

    <!-- Edit Profile Form (hidden initially) -->
    <div id="editProfileForm" style="display:none;">
        <form method="post">
            <h3>Edit Profile</h3>
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($profile['first_name']); ?>">
            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($profile['last_name']); ?>">
            <label>City:</label>
            <input type="text" name="city" value="<?php echo htmlspecialchars($profile['city']); ?>">
            <label>District:</label>
            <input type="text" name="district" value="<?php echo htmlspecialchars($profile['district']); ?>">
            <!-- Include other fields for work experience, education, skills, etc. -->
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <!-- Work Experience Section -->
    <div class="work-experience-section">
        <h3>Work Experience</h3>
        <?php foreach ($workExperiences as $work): ?>
            <div class="job-entry">
                <h4><?php echo htmlspecialchars($work['position']); ?> <span><?php echo htmlspecialchars($work['start_year']) . " - " . htmlspecialchars($work['end_year']); ?></span></h4>
                <p><?php echo htmlspecialchars($work['company_name']); ?></p>
                <p><?php echo htmlspecialchars($work['job_description']); ?></p>
            </div>1
        <?php endforeach; ?>
    </div>

    <!-- Education Section -->
    <div class="education-section">
        <h3>Education</h3>
        <?php foreach ($educationEntries as $education): ?>
            <div class="education-entry">
                <h4><?php echo htmlspecialchars($education['degree']); ?> <span><?php echo htmlspecialchars($education['graduation_year']); ?></span></h4>
                <p><?php echo htmlspecialchars($education['school']); ?>, <?php echo htmlspecialchars($education['field_study']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Skills Section -->
    <div class="skills-section">
        <h3>Skills</h3>
        <ul>
            <?php foreach ($skills as $skill): ?>
                <li><?php echo htmlspecialchars($skill['skill_name']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Personal Story Section -->
    <div class="story-section">
        <p>Their personal story text...</p>
    </div>




    </div>

        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>