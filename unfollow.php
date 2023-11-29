<?php

//onaj ko salje zahtev za otpracivanje: Logovan korisnik
//onaj kome je upucn zahtev za otpracivanje dohvatamo iz url-a

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

$q="DELETE FROM `followers`
WHERE `id_sender` = $id
AND `id_receiver` = $friend_id ";

$conn->query($q);

header("Location: followers.php");


?>