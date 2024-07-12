<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $db = new SQLite3('../database/database.db');
    $product = $_GET["product_name"];
    $getQuery = $db->prepare("SELECT name FROM products WHERE name = :name");
    $getQuery->bindValue(":name", $product, SQLITE3_TEXT);
    // Utför förfrågan
    $result = $getQuery->execute();
    // Hämta raden från resultatet
    $row = $result->fetchArray();

    if ($row) {
        return TRUE;
    } else {
        return FALSE;
    }
    // echo json_encode(['exists'=>true]);

}
?>