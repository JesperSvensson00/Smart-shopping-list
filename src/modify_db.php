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
    $product = $_POST["product"];
    $getQuery = $db->prepare("SELECT name FROM products WHERE name = :name");
    $getQuery->bindValue(":name", $product, SQLITE3_TEXT);
    // Utför förfrågan
    $result = $getQuery->execute();
    // Hämta raden från resultatet
    $row = $result->fetchArray();

    if (!$row){
    $insertQuery = $db->prepare('INSERT INTO products (name) VALUES (:name)');

    $insertQuery->bindValue(':name', strtolower($product), SQLITE3_TEXT);

    $result = $insertQuery->execute();
    if ($result){
        echo "New product added";
    } else {
        echo "Couldn't add product to database";
    }
    } else {
      $errorMessage = "<p style='background-color:Tomato;'>Product already exists</p>";
    }

    header("Location: modify_db.php");
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
    <title>Lägg till eller ändra produkt</title>
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
      async function removeProduct(id){
        console.log(id)
        let res = await fetch("delete_item.php?product_id=" + id, {method:"POST",
        })
        console.log(res)
        // let data = await res.json();
        // console.log("Data", data);
      }
    </script>
  </head>
  <body>
    <div class="container">
    <a href="logout.php" class="btn btn-lg btn-primary">Logga ut</a>
    <a href="menu.php" class="btn btn-lg btn-primary">Menu</a>
      <div class="row">
        <div class="col-12 text-center mt-4">
          <form class="form-signin" method="post" action="modify_db.php">
            <?php
            if(isset($errorMessage))
            print($errorMessage);
            unset($errorMessage);
            ?>
            <h2 class="form-signin-heading">Add product</h2>
	    
            <div id="error" class="alert alert-danger" role="alert">felllll</div>
            <div id="success" class="alert alert-success" role="alert">success</div>
            <p>
              <label for="product" class="sr-only">Product</label>
              <input
                type="text"
                id="product"
                name="product"
                class="form-control"
                placeholder="Product"
                required
                autofocus
              />
            </p>
            <button class="btn btn-lg btn-primary btn-block" type="submit">
              Add
            </button>
          </form>
        </div>
      </div>

        <?php
          $db = new SQLite3('../database/database.db');
          //$getQuery = $db->prepare("SELECT bought_products.product_id, products.name, bought_products.date FROM products LEFT JOIN products ON products.product_id = bought_products.product_id WHERE username = :username");
          //$getQuery->bindValue(":username", $loggedInUser, SQLITE3_TEXT);
          $getQuery = $db->prepare("SELECT product_id, name FROM products");
          // Utför förfrågan
          $result = $getQuery->execute();
          // Hämta raden från resultatet
          //$row = $result->fetchArray();

          while ($row = $result->fetchArray(SQLITE3_ASSOC)){
            // echo '<div class="row">' . $row['name'] . '<button onclick="removeProduct(' . $row['product_id'] . ')">Remove</button></div>';
            echo '<div class="d-flex"><form class="form-signin" method="post" action="delete_item.php" class="w-10"><input hidden name="product_id" value="' . $row['product_id'] . '"></input><label class="mr-4">' . ucfirst($row['name']) . '</label><button type="submit" class="btn btn-danger btn-sm">Remove</button></form></div>';
          }
        ?>
    </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>

