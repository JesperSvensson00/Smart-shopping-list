
<?php

session_start();
if (!isset($_SESSION["logged_in_user"])) {
    header("Location: index.php"); 
    exit();
}

// Om användaren är inloggad, hämta användarnamnet från sessionen
$loggedInUser = $_SESSION["logged_in_user"];
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Shopping lists</title>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
      crossorigin="anonymous"
    />
    <link
      href="https://getbootstrap.com/docs/4.0/examples/signin/signin.css"
      rel="stylesheet"
      crossorigin="anonymous"
    />
  </head>
  <body>
    <div class="container">
    <a href="logout.php" class="btn btn-lg btn-primary">Log out</a>
    <a href="menu.php" class="btn btn-lg btn-primary">Menu</a>
      <div class="row">
        <div class="col-12 text-center mt-4">
            <?php
            if(isset($errorMessage))
            print($errorMessage);
            unset($errorMessage);
            ?>
	    
            <div id="error" class="alert alert-danger" role="alert">felllll</div>
            <div id="success" class="alert alert-success" role="alert">success</div>
        </div>
      </div>
        
        <?php
            $db = new SQLite3('../database/database.db');
            $getQuery = $db->prepare("SELECT DISTINCT list_id FROM list WHERE username = :username");
            $getQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);

            // Utför förfrågan
            $result = $getQuery->execute();
            // Hämta raden från resultatet
            //$row = $result->fetchArray();
            ?>
            <h2>Your shopping lists</h2>
            <ul>
                <?php
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                        echo '<li><a href="confirm_list.php?list=' . $row["list_id"] . '">List ' . $row["list_id"] . '</a></li>';
                    }
            ?>
            </ul>
            
    </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>