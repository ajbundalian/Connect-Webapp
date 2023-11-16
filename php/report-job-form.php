<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../image/Connect Favicon (1).svg">
    <title>Report Job</title>
</head>
<style>
@import url(https://fonts.googleapis.com/css?family=Montserrat);
* {
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Monteserrat', sans-serif;
}

h1 {
    margin-bottom: 40px;
}

label {
    color: #333;
}

.btn-send {
    font-weight: 300;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    width: 80%;
    margin-left: 3px;
    }

.help-block.with-errors {
    color: #ff5050;
    margin-top: 5px;

}

.card{
	margin-left: 10px;
	margin-right: 10px;
}
</style>
<body>
<?php
session_start(); // Start the session if not already started

$jobId = $_GET['job_id'] ?? null; // Get job_id from URL
$userId = $_SESSION['user_id'] ?? null; // Get user_id from session

$jobTitle = '';
$success = false;
if ($jobId) {
    // Database connection
    $host = 'localhost'; // replace with your host
    $dbname = 'connect'; // replace with your database name
    $username = 'root'; // replace with your database username
    $password = '12345'; // replace with your database password

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT job_title FROM jobs WHERE job_id = :jobId");
        $stmt->execute(['jobId' => $jobId]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $jobTitle = $row['job_title']; // Fetch the job title
        }
    } catch(PDOException $e) {
        // Handle error
        echo "Error: " . $e->getMessage();
    }

}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reportMessage = $_POST['report_message'] ?? '';

    if ($jobId && $userId && $reportMessage) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reported_jobs (user_id, job_id, report_message) VALUES (:userId, :jobId, :reportMessage)");
            $stmt->execute(['userId' => $userId, 'jobId' => $jobId, 'reportMessage' => $reportMessage]);
            header('Location: applicant-homepage.php');
            exit;
        } catch(PDOException $e) {
            // Handle error
            echo "Error: " . $e->getMessage();
        }
    }
}

?>
<div class="container">
        <div class=" text-center mt-5 ">
            <h1 >Job Report Form</h1>
        </div>

    <div class="row ">
      <div class="col-lg-7 mx-auto">
        <div class="card mt-2 mx-auto p-4 bg-light">
            <div class="card-body bg-light">
            <div class = "container">
        <form id="contact-form" role="form" method="post">
            <div class="controls">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="form_name">Job Title</label>
                            <input id="JobTitle" type="text" name="job_title" readonly class="form-control-plaintext" value="<?php echo htmlspecialchars($jobTitle); ?>" required="required">
                            
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="form_message">Report Message</label>
                            <textarea id="ReportMessage" name="report_message" class="form-control form-control-lg" placeholder="Write your report here." rows="4" required="required"></textarea>
                            </div>

                        </div>


                    <div class="col-md-12">
                        <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" value="Report" >
                    
                </div>
          
                </div>


        </div>
         </form>
        </div>
            </div>


    </div>
        <!-- /.8 -->

    </div>
    <!-- /.row-->

</div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>