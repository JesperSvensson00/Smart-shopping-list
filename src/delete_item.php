<?php
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

    header("Location: modify_db.php");
    exit();
}
?>