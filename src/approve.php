
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
    $product_id = $_POST["product_id"];
    $db = new SQLite3('../database/database.db');
    $removeQuery = $db->prepare('DELETE FROM bought_products WHERE product_id = :product_id');
    $removeQuery->bindValue(':product_id', $product_id, SQLITE3_TEXT);
    $removeQuery->execute();

    $removeQuery2 = $db->prepare('DELETE FROM list WHERE product_id = :product_id');
    $removeQuery2->bindValue(':product_id', $product_id, SQLITE3_TEXT);
    $removeQuery2->execute();

    $removeQuery3 = $db->prepare('DELETE FROM products WHERE product_id = :product_id');
    $removeQuery3->bindValue(':product_id', $product_id, SQLITE3_TEXT);
    $removeQuery3->execute();

    header("Location: confirm_list.php?list=" . $_POST["list"]);
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
        <div class="text-center mt-5 pt-5">
            <h2>Delete product</h2>
            
            <?php
                echo '<p>' . ucfirst($_GET["product_name"]) . ' will be removed from the catalog!</p>';
            ?>
            <div>
            <form method="post" action="approve.php">
              <button class="btn btn-small btn-primary mr-2" type="submit">Ok</button>
                <?php
                    echo '<input name="product_id" hidden value="' . $_GET["product_id"] . '"></input>';
                    echo '<input name="list" hidden value="' . $_GET["list"] . '"></input>';
                    echo '<a class="btn btn-small btn-danger" href="confirm_list.php?list=' . $_GET["list"] . '">Cancel</a>';
                ?>
            </form>
            
            </div>
        </div>

            
    </div>
    <script type="module" src="../public/js/index.js"></script>
  </body>
</html>

