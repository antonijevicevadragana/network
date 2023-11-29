<?php
//require_once "header.php";

mysqli_report(MYSQLI_REPORT_OFF);  // da nam izlaze koje su greske
// $server="localhost";
// $database="network";
// $username="admin";
// $password="admin123";  

$server="localhost";
$database="network";
$username="networkAdmin";
$password="network26%";  

$conn=new mysqli($server, $username, $password, $database);  //mora se pridrzavati ovog redosleda


if ($conn->connect_error) {
    //header("location:error.php?m=" .$conn->connect_error);

    die("neuspela konekcija :" . $conn->connect_error);
}

$conn->set_charset("utf8");




?>