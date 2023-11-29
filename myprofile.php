<?php
// na ovu stranicu se dolazi klikom na profilnu slicinu u hederu. Svoj profil imaju samo clanovi koji su popunili profil, oni koji su samo registrovani nemaju pristup ovoj stranici

require_once "connection.php";
//require_once "validaction.php";


session_start();
require_once "header.php";

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

$d = DobofUsers($conn, $id);
$g=GenderofUsers($conn, $id);
$bio=bioOfUsers($conn, $id);
$following = NumberFollow($conn, $id); // broj pracenja 
$followers = NumberFollows($conn, $id); //broj pratilaca

// za pratioce i pracenja da se izlistaju
// Odredimo koje druge korisnike prati logovan korisnik
$upit1 = "SELECT `id_receiver` FROM `followers` WHERE `id_sender` = $id";
$res1 = $conn->query($upit1);
$niz1 = array();
while ($row = $res1->fetch_array(MYSQLI_NUM)) {
    $niz1[] = $row[0];
}
// var_dump($niz1);

// Odrediti koji drugi korisnici prate logovanog korisnika
$upit2 = "SELECT `id_sender` FROM `followers` WHERE `id_receiver` = $id";
$res2 = $conn->query($upit2);
$niz2 = array();
while ($row = $res2->fetch_array(MYSQLI_NUM)) {
    $niz2[] = $row[0];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <link rel="stylesheet" href="style.css?ver=1">
</head>

<body>
    <!--1) u ovoj stranici treba da se prikazuju podaci o logovanom korisniku
    2) podaci koga prati /broj pracenja i njihove slike
    3) podaci ko njega prati/ broj pracenja i njegove slike
-->
    <div class="row justify-content-center">
        <h3>Name and Last Name: <?php echo $username ?> <br> Date of birth: <?php echo $d ?><br> Gender: <?php echo $g ?><br> Bio: <?php echo"<q>". $bio ."</q>"?></h3>
    </div>




    <div class="container">
        <div class="row">
        <div class="col-md-6">
                <h2>Followers (number of followers: <?php echo $followers; ?> )</h2>
                <?php
                $q1 = "SELECT `u`.`id` as `user`,
                `p`.`gender` as `gender`,
                `p`.`id` as `profileId`, 
                `img`,
                `u`.`username` AS `uname`,
                `p`.`first_name` as`name`,
                `p`.`last_name` AS `lname`
                FROM `users` AS `u`
                LEFT JOIN `profiles` AS `p`
                ON `u`.`id` = `p`.`id_user`
                -- ORDER BY `name`
                ";

                $res = $conn->query($q1);
                if ($res->num_rows == 0) {
                ?>
                    <div class="error">No other users in database</div>
                    <?php

                } else {

                    if (empty($niz2)) {
                    ?>
                        <div class="error">No other users in database</div>
                <?php
                    } else {

                        echo "<table>";
                        echo "<tr><th>ID user</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Profile Images</th></tr>";
                        $avatar = 'images/otherAvatar.jpg';

                        while ($row = $res->fetch_assoc()) {
                            $idPracenog = $row['user'];

                            foreach ($niz2 as $el) {
                                if ($el == $idPracenog) {
                                    echo "<tr><td>$idPracenog</td><td>" . $row['name'] . "</td><td>" . $row['lname'] . "</td><td>" . $row['uname'] . "</td><td>";

                                    if ($row['user']) {
                                        if ($row['profileId']) {
                                            if ($row['img'] != NULL) {
                                                $i = "images/" . $row['img']; //imaju svoju sliku
                                                printImg($i);
                                            } else {
                                                checkGender($row['gender']);    //poziva f-ju  i stampa avatara po polu                
                                            }
                                        }
                                        // ako nema profil  samo je registrovan onda postaviti avatar difault
                                        else {
                                            printImg($avatar);
                                        }
                                        echo "</td></tr>";
                                    }
                                }
                            }
                        }
                    }
                    echo "</table>";
                }


                ?>
            </div>

            <div class="col-md-6 ">
            <h2>Following(Number of following: <?php echo $following; ?> )</h2>
                <?php
                $q1 = "SELECT `u`.`id` as `user`,
            `p`.`gender` as `gender`,
            `p`.`id` as `profileId`, 
            `img`,
            `u`.`username` AS `uname`,
            `p`.`first_name` as`name`,
            `p`.`last_name` AS `lname`
            FROM `users` AS `u`
            LEFT JOIN `profiles` AS `p`
             ON `u`.`id` = `p`.`id_user`
            -- ORDER BY `name`
            ";

                $res = $conn->query($q1);
                if ($res->num_rows == 0) {
                ?>
                    <div class="error">No other users in database</div>
                    <?php

                } else {

                    if (empty($niz1)) {
                    ?>
                        <div class="error">No other users in database</div>
                <?php
                    } else {

                        echo "<table>";
                        echo "<tr><th>ID user</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Profile Images</th></tr>";
                        $avatar = 'images/otherAvatar.jpg';

                        while ($row = $res->fetch_assoc()) {
                            $idPracenog = $row['user'];

                            foreach ($niz1 as $el) {
                                if ($el == $idPracenog) {
                                    echo "<tr><td>$idPracenog</td><td>" . $row['name'] . "</td><td>" . $row['lname'] . "</td><td>" . $row['uname'] . "</td><td>";

                                    if ($row['user']) {
                                        if ($row['profileId']) {
                                            if ($row['img'] != NULL) {
                                                $i = "images/" . $row['img']; //imaju svoju sliku
                                                printImg($i);
                                            } else {
                                                checkGender($row['gender']);    //poziva f-ju  i stampa avatara po polu                
                                            }
                                        }
                                        // ako nema profil  samo je registrovan onda postaviti avatar difault
                                        else {
                                            printImg($avatar);
                                        }
                                        echo "</td></tr>";
                                    }
                                }
                            }
                        }
                    }
                    echo "</table>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    // postovi

$q = "SELECT `u`.`id`, `u`.`username`, 
CONCAT(`p`.`first_name`, ' ',`p`.`last_name`) AS `full_name`, `img`,
`p`.`gender` as `gender`,
`p`.`id` as `profileId`,
`posts`.`contetnt` as  `post`,
`created_time` as `time`

FROM `users` AS `u`
LEFT JOIN `profiles` AS `p`
ON `u`.`id` = `p`.`id_user`
LEFT JOIN `posts` ON `u`.`id` =`posts`.`id_user`
where `u`.`id` =$id
ORDER BY `created_time`DESC;
 ";
$avatar = 'images/otherAvatar.jpg';
$res = $conn->query($q);
if ($res->num_rows > 0) {
    
    while ($row = $res->fetch_assoc()) {
        echo "<div class='post'>";
        if ($row['post']) {
           
            if ($row['profileId']) {
                if ($row['img'] !== NULL) {
                    if ($row['img'] != NULL) {
                        $i = "images/" . $row['img']; //imaju svoju sliku
                        printImg($i);
                    } else {
                        checkGender($row['gender']);    //poziva f-ju  i stampa avatara po polu                
                    }
                }
                // ako nema profil  samo je registrovan onda postaviti avatar difault
                else {
                    printImg($avatar);
                }
            }
            if ($row['full_name'] !== NULL) {
                echo  " <i>". $row['full_name'] ."</i>";
            } else {
                echo  $row['username'];
            }
            echo "<p>". $row['post']. "<p>";
            echo "<p><i> DATE: </i><strong>". $row['time']. "</strong><p>";
        }
        echo "</div>";
    }
}?>
</body>

</html>