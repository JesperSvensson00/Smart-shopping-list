
<?php

session_start();
if (!isset($_SESSION["logged_in_user"])) {
    header("Location: index.php"); 
    exit();
}

// Om användaren är inloggad, hämta användarnamnet från sessionen
$loggedInUser = $_SESSION["logged_in_user"];
require('functions.php');

header("Access-Control-Allow-Origin: *");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["product_name"])) {
        checkProduct($_GET["product_name"]);
        header("Location: confirm_list.php?list=" . $_GET["list"]);
        exit();
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new SQLite3('../database/database.db');

    $rows = $_POST["row"];

    // Lägger till allt i bought_products
    $replacement_id=NULL;
    $today = date("Y-m-d H:i:s");
    foreach ($rows as $index => $row){
      if (isset($row['checked'])) {
        $insertQuery = $db->prepare('INSERT INTO bought_products (product_id, purchase_date, username, replacement_id) VALUES (:product_id, :purchase_date, :username, :replacement_id)');
        $insertQuery->bindValue(':product_id', $index, SQLITE3_TEXT);
        $insertQuery->bindValue(':purchase_date', $today, SQLITE3_TEXT);
        $insertQuery->bindValue(':username', $loggedInUser, SQLITE3_TEXT);
        $insertQuery->bindValue(':replacement_id', $row['replaced'], SQLITE3_TEXT);
        $result = $insertQuery->execute();
      }
    }
    
    header("Location: menu.php");
    exit();
    
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
    <title>Generate shopping list</title>
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
      <div class="row justify-content-center">
        <div class="col-md-6 text-center mt-4">
            <h2 class="form-signin-heading">Add product</h2>
              <form method="GET" action="confirm_list.php" class="text-center">
                  <?php
                      echo '<input type="text" hidden name="list" value="' . $_GET['list'] . '">'
                  ?>
                  <div class="input-group">
                    <input type="text" name="product_name" id="product" class="form-control" placeholder="Product name">
                    <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        Add
                    </button>
                    </div>
                  </div>
              </form>
            </div>
        </div>
      </div>
      <div class="row text-center mt-5">
        <?php
          $db = new SQLite3('../database/database.db');
          $getQuery = $db->prepare("SELECT name, list.product_id FROM list INNER JOIN products ON list.product_id = products.product_id WHERE list_id = :list_id AND username = :username");
          $getQuery->bindValue(":list_id", $_GET['list'], SQLITE3_TEXT);
          $getQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);

          // Utför förfrågan
          $listedProducts = $getQuery->execute();


          // Hämtar alla produkter
          $db = new SQLite3('../database/database.db');
          $getQuery = $db->prepare("SELECT product_id, name FROM products");
          $getQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);

          // Utför förfrågan
          $result = $getQuery->execute();
          $allProducts = [];
          while ($row = $result->fetchArray(SQLITE3_ASSOC)){
            $allProducts[] = $row;
          }
        ?>
        <form method="post" action="confirm_list.php" id="confirm_list" class="col">
          <button type="submit" class="btn btn btn-success form-group">Confirm list</button>
          <?php
              while ($row = $listedProducts->fetchArray(SQLITE3_ASSOC)){
                  echo '<div class="form-group row justify-content-center align-items-center">';
                  echo '<label for="' . $row['product_id'] . '" class="mr-2 mb-0">' . ucfirst($row['name']) . '</label>';
                  echo '<input type=checkbox name="row[' . $row['product_id'] . '][checked]" id="' . $row['product_id'] . '" class="mr-4"></input>';
                  echo '<div class="w-25 row justify-content-center">
                          <select name="row[' . $row['product_id'] . '][replaced]" id="product" class="custom-select w-75">
                            <option selected value="null">Choose replacement</option>';
                
                      foreach ($allProducts as $products){
                          echo '<option value="' . $products["product_id"] . '">' . ucfirst($products["name"]) . '</option>';
                      }

                  echo '</select></div>';
                  echo '<a class="btn btn-sm btn-danger" href="approve.php?list=' . $_GET["list"] . '&product_id=' . $row["product_id"] . '&product_name=' . $row["name"] . '">Remove</a>';
                  echo '</div>';
              }
          ?>
        </form>
      </div> 
    </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>

