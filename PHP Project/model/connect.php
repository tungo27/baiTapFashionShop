<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "fashion_mylishop";

// Create connection
/*
$conn = mysqli_connect($host, $user, "", $database);
mysqli_set_charset($conn, 'UTF8');
*/
// create connection using PDO
try {
    $dbh = new PDO('mysql:host=localhost;dbname=fashion_mylishop', $user, $password);
} catch (PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}
// Check connection
/*
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
 */

// echo "Connected successfully" ;