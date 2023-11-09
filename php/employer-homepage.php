<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="../css/employer.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="/image/Connect Favicon (1).svg">
</head>
<body>
  <!--Nav Starts Here-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-2">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="/image/Connect Favicon (1).svg" alt="Connect Logo" height="50" width="50">
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

    <!--Nav Ends Here-->

    <div class="company">
        <div class="company-header">
            <div class="company-logo">
                <!-- Added 'id' for image -->
                <img id="company-logo" src="default_logo.jpg" alt="Company Logo">
            </div>
            <!-- Added 'id' for company name -->
            <div class="company-name" id="company-name">Default Name</div>
            <!-- Added 'id' for job link -->
            <a href="#" class="view-jobs" id="job-link">View Jobs</a>
        </div>

        <div class="overview-section">
            <div class="overview-title">Overview</div>
            <!-- Added 'id' for overview content -->
            <p id="overview-content">Default overview content...</p>
            <!-- Added 'id' for website link -->
            <a href="#" class="visit-website" id="website-link">Visit Website</a>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>