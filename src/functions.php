<?php
//Här ska du lägga till flera funktioner 

//Observera att följande funktion är sårbar för sql-injection och behöver förbättras
function selectPwd($username){
    // Öppna SQLite-databasen
    $db = new SQLite3('../database/database.db');

    // Förbered SQL-frågan
    $getQuery = $db->prepare("SELECT password, logged_in FROM users WHERE username = :username");
    $getQuery->bindValue(":username", $username, SQLITE3_TEXT);

    // Utför frågan
    $result = $getQuery->execute();

    // Hämta raden från resultatet
    $row = $result->fetchArray(SQLITE3_ASSOC);

    // Stäng databasanslutningen
    $db->close();
    // Returnera resultatet (kan vara null om användarnamnet inte hittades)
    return $row;

}


function insertUser($username, $password){
    // Öppna SQLite-databasen
    $db = new SQLite3('../database/database.db');

    // Förbered SQL-frågan
    $getQuery = $db->prepare("SELECT username FROM users WHERE username = :username");
    $getQuery->bindValue(":username", $username, SQLITE3_TEXT);

    // Utför förfrågan
    $result = $getQuery->execute();

    // Hämta raden från resultatet
    $row = $result->fetchArray();

    if ($row) {
        return "Användaren finns redan";
    };

    $insertQuery = $db->prepare('INSERT INTO users (username, password, logged_in) VALUES (:username, :password, 1)');

    $insertQuery->bindValue(':username', $username, SQLITE3_TEXT);
    $insertQuery->bindValue(':password', hash("sha3-512", $password), SQLITE3_TEXT);


    $result = $insertQuery->execute();
    if ($result){
        echo "New user created";
        return true;
    } else {
        return "Det gick inte att lägga till användaren i databasen!";
    }

    // Stäng databasanslutningen
    $db->close();
    // Returnera resultatet (kan vara null om användarnamnet inte hittades)
    //return true;

}

function checkProduct($name){
    session_start();
    if (!isset($_SESSION["logged_in_user"])) {
        header("Location: index.php"); 
        exit();
    }
    
    // Om användaren är inloggad, hämta användarnamnet från sessionen
    $loggedInUser = $_SESSION["logged_in_user"];



    $db = new SQLite3('../database/database.db');
    if (!$name){
        return;
    }
    $getQuery = $db->prepare("SELECT name, product_id FROM products WHERE name = :name");
    $getQuery->bindValue(":name", strtolower($name), SQLITE3_TEXT);
    // Utför förfrågan
    $result = $getQuery->execute();
    // Hämta raden från resultatet
    $row = $result->fetchArray();

    if (!$row) {
        $insertQuery = $db->prepare('INSERT INTO products (name) VALUES (:name)');

        $insertQuery->bindValue(':name', strtolower($name), SQLITE3_TEXT);

        $result = $insertQuery->execute();

        // Get the new product
        $getQuery = $db->prepare("SELECT name, product_id FROM products WHERE name = :name");
        $getQuery->bindValue(":name", strtolower($name), SQLITE3_TEXT);
        // Utför förfrågan
        $result = $getQuery->execute();
        // Hämta raden från resultatet
        $row = $result->fetchArray();
    }

    // Lägg till produkt i listan
    $insertQuery = $db->prepare('INSERT INTO list (list_id, product_id, username) VALUES (:list_id, :product_id, :username)');
    $insertQuery->bindValue(':list_id', $_GET["list"], SQLITE3_TEXT);
    $insertQuery->bindValue(':product_id', $row["product_id"], SQLITE3_TEXT);
    $insertQuery->bindValue(':username', $loggedInUser, SQLITE3_TEXT);
    $result = $insertQuery->execute();
}
?>


