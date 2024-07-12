<?php
session_start();
$actual_url = dirname($_SERVER[REQUEST_URI]);

// Sätter användaren till utloggad
$loggedInUser = $_SESSION["logged_in_user"];

$db = new SQLite3('../database/database.db');
$removeQuery = $db->prepare('UPDATE users SET logged_in = 0 WHERE username = :username');
$removeQuery->bindValue(':username', $loggedInUser, SQLITE3_TEXT);
$removeQuery->execute();

session_destroy();
header("Location: ".$actual_url."/../");
exit();
?>