<?php
//session_start();

session_start();
if (empty($_SESSION["id"])) {
    header("Location: index.php");  //ako korisnik nije ulogovan izbaciti ga, samo logovani korisnici mogu da idu na conekcije
}
require_once "header.php";
require_once "connection.php";
require_once "validaction.php"; //poziva zbog profileExists
$username = "";
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $id = $_SESSION['id']; //id log korisnika  
    $row = profileExists($id, $conn);

    if ($row === false) {
        $username = "";
    } else {
        //log korisnik ima profil

        $username = $row['first_name'] . " " . $row['last_name'];
    }
}


if (isset($_GET['friend_id'])) {
    $FiendId = $conn->real_escape_string($_GET["friend_id"]);


    //upit za korisnika ciji profil zelimo da vidimo

    $q = "SELECT `u`.`id` AS `id`, `u`.`username` AS `username1`, 
`p`.`first_name` AS `FirstName`,
`p`.`last_name` AS `LastName`,
`p`.`gender` as `gender`,
`p`.`dob` as `date`,
`p`.`dob` as `date`,
`p`.`bio` as `bio`
FROM `users` AS `u`
LEFT JOIN `profiles` AS `p`
ON `u`.`id` = `p`.`id_user`
WHERE `u`.`id` =$FiendId;
";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOW PROFILE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <link rel="stylesheet" href="style.css?ver=1">
</head>

<body>

</body>

</html>

<!-- pozdravljanje korisnika po imenu koji je ulogovan -->

<div class="container">
    <br>
    <h1 class="headerForm animate__animate__animated__bounce animacija">Welcome, <?php echo $username ?>, to our Social network</h1>
</div>


<!-- div za pirkaz korisnika na koga je kliknuto tabelarno -->

<?php
$res = $conn->query($q);
if ($res->num_rows == 0) {  // u slucaju da ta osoba na cije ime smo kliknuli ne postoji, ali cim je prosledjen id treba da postoji
?>
    <div class="error"> No user in database

    </div>


<?php
}

if ($res->num_rows > 0) {   //cim smo dosli ovde linkom neko postoji i pokazuje sve podatke o osobi koju smo kliknjuli, samo je pitanje da li ce pokazati username samo ili sve podatke iz tabele
    echo "<div class='container'><table>";

    while ($row = $res->fetch_assoc()) {
        echo "<tr><th>First Name</th><td>" . $row['FirstName'] . "</td></tr>";

        echo "<th>Last Name</th><td>" . $row['LastName'] . "</td></tr>";

        echo "<th>Username</th><td>" . $row['username1'] . "</td></tr>";

        echo "<th>Date of birth</th><td>" . $row['date'] . "</td></tr>";

        echo "<th>Gender</th><td>" . $row['gender'] . "</td></tr>";

        echo "<th>About me</th><td>" . $row['bio'] . "</td></tr>";
    }
}
echo "</table></div>";





?>