<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'mysql01.cs.virginia.edu');
define('DB_USERNAME', 'vbp8vgc');
define('DB_PASSWORD', 'Winter2022!!');
define('DB_NAME', 'vbp8vgc');
 
/* Attempt to connect to MySQL database */
try{
    $db = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>