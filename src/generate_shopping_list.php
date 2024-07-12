
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new SQLite3('../database/database.db');

    $getMax = $db->prepare("SELECT MAX (list_id) AS max FROM list");
    $result = $getMax->execute();
    $max = $result->fetchArray();

    
    foreach ($_POST as $key => $value){
        $insertQuery = $db->prepare('INSERT INTO list (list_id, product_id, username) VALUES (:list_id, :product_id, :username)');
        $insertQuery->bindValue(':list_id', $max["max"]+1, SQLITE3_TEXT);
        $insertQuery->bindValue(':product_id', $key, SQLITE3_TEXT);
        $insertQuery->bindValue(':username', $loggedInUser, SQLITE3_TEXT);
        $result = $insertQuery->execute();
    }
    echo $max["max"]+1;
    header("Location: confirm_list.php?list=" . ($max["max"]+1));
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
    <script>
      function addProduct(){
        product = document.getElementById("product")
        console.log(product)
        id = product.value
        name = product.options[product.selectedIndex].text
        elem = document.createElement("div");
        elem.innerHTML = '<label for="' + id + '">' + name + '</label><input type=checkbox name="' + id+ '" id="' + id + '" checked></input>'
        document.getElementById("generate_list").appendChild(elem)
      }
    </script>
  </head>
  <body>
    <div class="container">
    <a href="logout.php" class="btn btn-lg btn-primary">Log out</a>
    <a href="menu.php" class="btn btn-lg btn-primary">Menu</a>
      <div class="row">
        <div class="col-12 text-center mt-4">
            <h2 class="form-signin-heading">Add product</h2>
            <div class="w-100 row justify-content-center">
              <div class="input-group w-25 text-center">
                <select name="product" id="product" class="custom-select">
                  <option selected>Choose a product</option>
                  <?php
                    $db = new SQLite3('../database/database.db');
                    $getQuery = $db->prepare("SELECT product_id, name FROM products");
                    $getQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);

                    // Utför förfrågan
                    $result = $getQuery->execute();
                    ?>
                        <?php
                            while ($row = $result->fetchArray(SQLITE3_ASSOC)){
                                echo '<option value="' . $row["product_id"] . '">' . ucfirst($row["name"]) . '</option>';
                            }
                    ?>
                </select>
                <div class="input-group-append">
                  <button onclick="addProduct()" class="btn  btn-primary" type="submit">
                    Add
                  </button>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="row mt-5">
        <?php
            $db = new SQLite3('../database/database.db');
            $getQuery = $db->prepare("SELECT product_id, name FROM products WHERE products.product_id NOT IN (SELECT bought_products.product_id FROM bought_products WHERE username = :username)");
            $getQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);

            // Utför förfrågan
            $notBoughtProducts = $getQuery->execute();

            $boughtQuery = $db->prepare("SELECT bought_products.product_id, name, purchase_date 
                                          FROM bought_products 
                                          INNER JOIN products ON bought_products.product_id = products.product_id 
                                          WHERE username = :username ORDER BY bought_products.product_id, purchase_date DESC");

            $boughtQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);

            $boughtProducts = $boughtQuery->execute();
            // $boughtProducts = $result;

            $suggestions = array();
            $currentProduct = $boughtProducts->fetchArray(SQLITE3_ASSOC);

            if ($currentProduct) {
              $sum = 0;
              $i = 1;
              $firstDate = new DateTime($currentProduct["purchase_date"]);
              while ($row = $boughtProducts->fetchArray(SQLITE3_ASSOC)){
                if ($row["product_id"] === $currentProduct["product_id"]) {
                  $date1 = new DateTime($row["purchase_date"]);
                  $date2 = new DateTime($currentProduct["purchase_date"]);
                  $diff = $date1->diff($date2);
                  // $currentAverage = (2 * $currentAverage + $diff->d) / 3;
                  // echo ;
                  $days = $diff->d;
                  $sum = $sum + $days;
                  $currentProduct = $row;
                } else {
                  // Lägga till i en lista om average är lägre än tiden från senaste köpet
                  if ($i > 1) {
                    $average = $sum / ($i - 1);
                    $today = new DateTime();
                    $firstDate->modify('+'. $average . ' days');
                    if ($firstDate <= $today){
                      $suggestions[] = $currentProduct;
                    }
                  }
                  
                  $sum = 0;
                  $i = 0;
                  $currentProduct = $row;
                  $firstDate = new DateTime($currentProduct["purchase_date"]);
                }
                $i += 1;
              }
              if ($i > 1){
                $average = $sum / ($i - 1);
                $today = new DateTime();
                $firstDate->modify('+'. $average . ' days');
                if ($firstDate < $today){
                  $suggestions[] = $currentProduct;
                }
              }
            }

            // echo "Förslag:";
            // echo sizeof($suggestions);

          ?>
            <form method="post" action="generate_shopping_list.php" id="generate_list" class="col-12 text-center">
                <button type="submit" class="btn btn btn-success form-group">Create list</button>
                
                <?php
                    while ($row = $notBoughtProducts->fetchArray(SQLITE3_ASSOC)){
                        echo '<div class="form-group"><label for="' . $row['product_id'] . '" class="mr-2">' . ucfirst($row['name']) . '</label><input type=checkbox name="' . $row['product_id'] . '" id="' . $row['product_id'] . '" checked></input></div>';
                    }
                ?>
                <?php
                    foreach ($suggestions as $product){
                        echo '<div class="form-group"><label for="' . $product['product_id'] . '" class="mr-2">' . ucfirst($product['name']) . '</label><input type=checkbox name="' . $product['product_id'] . '" id="' . $product['product_id'] . '" checked></input></div>';
                    }
                ?>

            </form> 
          </div>
          </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>

