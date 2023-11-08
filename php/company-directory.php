<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="\css\company-directory.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="\image\Connect Favicon (1).svg">
    <title>Company</title>
</head>
<body>
    <style>
        <?php include "company-directory.css" ?>
    </style>
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

    <div class="company-directory">
        <p>Company Directory</p>
    </div>
    <main>
        <section class="company-grid">
        <?php
        // Set database credentials
        $host = 'localhost';
        $db   = 'connect';
        $user = 'root';
        $pass = '12345';
        $charset = 'utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Establish connection
        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

        // Fetch and display company profiles
        try {
         $stmt = $pdo->query("SELECT company_id, company_pic, company_name FROM company_profile");

         while ($row = $stmt->fetch()) {
            echo '<a href="#" class="company-card" company-id="' . htmlspecialchars($row['company_id']) . '">';
            echo '<img src="../image/' . htmlspecialchars($row['company_pic']) . '" alt="Company Logo" class="company-logo">';
            echo '<p>' . htmlspecialchars($row['company_name']) . '</p>';
            echo '</a>';
        }
    } catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    }
    ?>
    </main>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>