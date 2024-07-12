<?php
session_start();

require('functions.php');
if (isset($_SESSION["logged_in_user"])) {
     header("Location: menu.php");
    exit();
}
/*else{
        $_SESSION["logged_in_user"] = 'kalle';
	exit();
	}*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = hash("sha3-512",$_POST["password"]);
    $row = selectPwd($username); //hämtar från databasen

    if ($row){
      $validPassword = $row['password'];
      if ($password === $validPassword) {
        if ($row["logged_in"] != 1) {
          $_SESSION["logged_in_user"] = $username;

          // Sätter användaren till inloggad
          $db = new SQLite3('../database/database.db');
          $removeQuery = $db->prepare('UPDATE users SET logged_in = 1 WHERE username = :username');
          $removeQuery->bindValue(':username', $username, SQLITE3_TEXT);
          $removeQuery->execute();

          header("Location: menu.php");
          exit();
        } else {
          $errorMessage = "<p style='background-color:Tomato;'>Already logged in!</p>";
        }
          
      } else {
          $errorMessage = "<p style='background-color:Tomato;'>Wrong password</p>";
      }
    } else {
      $errorMessage = "<p style='background-color:Tomato;'>User dosen't exist</p>";
    }
    
}
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
    <title>Please sign in</title>
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
      <div class="row">
        <div class="col-12 text-center">
          <a
            href="registration.php"
            class="btn btn-secondary btn-sm active"
            role="button"
            aria-pressed="true"
          >
            Registration
          </a>
        </div>
      </div>
      <div class="row">
        <div class="col-12 text-center mt-4">
          <form class="form-signin" method="post" action="index.php">
	  <?php
	  if(isset($errorMessage))
		print($errorMessage);
		unset($errorMessage);
	  ?>
	  <h2 class="form-signin-heading">Please sign in</h2>
	    
            <div id="error" class="alert alert-danger" role="alert">felllll</div>
            <div id="success" class="alert alert-success" role="alert">success</div>
            <p>
              <label for="username" class="sr-only">Username</label>
              <input
                type="text"
                id="username"
                name="username"
                class="form-control"
                placeholder="Username"
                required
                autofocus
              />
            </p>
            <p>
              <label for="password" class="sr-only">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                class="form-control"
                placeholder="Password"
                required
              />
            </p>
            <button class="btn btn-lg btn-primary btn-block" type="submit">
              Sign in
            </button>
          </form>
        </div>
      </div>
    </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>

