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
       <title>Menu page</title>
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
    <h1> Menu</h1>
    <ol type="1">
      <li><a href="generate_shopping_list.php">Create shopping list</a>
      </li>
      <li><a href="modify_db.php">Modify product list</a></li>
      <li><a href="lists.php">Shopping lists</a></li>
    </ol>
  </div>
  </body>
</html>
