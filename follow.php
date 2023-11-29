<?php

//onaj ko salje zahtev: Logovan korisnik
//onaj kome je upucn zahtev dohvatamo iz url-a

session_start();
if (empty($_SESSION['id'])) {
    header("Location: index.php");
}
$id=$_SESSION['id'];
require_once "connection.php";

if (empty($_GET['friend_id'])) {
    header("Location: index.php");
}

$friend_id=$conn->real_escape_string($_GET['friend_id']);

$q="SELECT * FROM `followers`
WHERE `id_sender` =$id
AND `id_receiver`= $friend_id ";

$result=$conn->query($q);
if ($result->num_rows == 0) {
    $upit=" INSERT INTO `followers`(`id_sender`, `id_receiver`)
    VALUE ($id, $friend_id)";
    $result1 =$conn->query($upit);
}

header("Location: followers.php");


?>