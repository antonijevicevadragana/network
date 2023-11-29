<?php
session_start();
if (empty($_SESSION["id"])) {
    header("Location: index.php");
}
$id = $_SESSION["id"];
require_once "connection.php";
require_once "header.php";
require_once "validaction.php";
if (isset($_GET['friend_id'])) {
    // Zahtev za pracenje drugog korisnika
    $friendId = $conn->real_escape_string($_GET["friend_id"]);
    $q = "SELECT * FROM `followers` 
                WHERE `id_sender` = $id
                AND `id_receiver` = $friendId";
    $result = $conn->query($q);
    if ($result->num_rows == 0) {
        $upit = "INSERT INTO `followers`(`id_sender`, `id_receiver`)
                    VALUE ($id, $friendId)";
        $result1 = $conn->query($upit);
    }
}

if (isset($_GET['unfriend_id'])) {
    // Zahtev da se drugi korisnik odprati
    $friendId = $conn->real_escape_string($_GET["unfriend_id"]);
    $q = "DELETE FROM `followers`
                WHERE `id_sender` = $id
                AND `id_receiver` = $friendId";
    $conn->query($q);
}

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
// var_dump($niz2);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members of Social Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <link rel="stylesheet" href="style.css?ver=1">
</head>

<body>
    <div class="container">
        <br><h1 class="headerForm animate__animate__animated__bounce animacija">See other members from our site</h1>
    </div>
    <div class="container">

        <form method="POST">
            <label for="search">Search memeber</label>
            <input type="text" name="search" id="search">
            <!-- <input type="submit" value="Search"> -->
            <button name="submit" class='followbtn'>SEARCH</button>

        </form>
    </div>

    <?php

    //prvi put dolazimo get metodom to je u else grani, izlistani svi clanovi
    // ako smo dosli post metodom ispis je po pretrazi if grana.

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $str = $_POST['search'];
        $avatar = 'images/otherAvatar.jpg';


        $upitSlovo = "SELECT `u`.`id`, `u`.`username`, 
    CONCAT(`p`.`first_name`, ' ',`p`.`last_name`) AS `full_name`, `img`,
    `p`.`gender` as `gender`,
    `p`.`id` as `profileId`
    FROM `users` AS `u`
    LEFT JOIN `profiles` AS `p`
    ON `u`.`id` = `p`.`id_user`
    WHERE `u`.`id` != $id  AND  `p`.`first_name` LIKE '$str%' OR  `u`.`username`LIKE '$str%' AND `u`.`id` != $id
    ORDER BY `full_name`;";

        $res = $conn->query($upitSlovo);
        if ($res->num_rows == 0) {
    ?>
            <div class="error">No users in database</div>
        <?php

        } else {
            echo "<table>";
            // echo "<tr><th>Profile Images</th><th>Name</th><th colspan='2'>Action</th></tr>";
            echo "<tr><th>Profile Images</th><th>Name</th><th>Action</th><th>Followers</th><th>Following</th></tr>";

            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>";
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
                echo '</td><td>';
                $friendId = $row["id"];
                if ($row['full_name'] !== NULL) {
                    echo "<a href='show_profile.php?friend_id=$friendId'>" . $row['full_name'] . "</a>"; //prosledjuje id u linku
                    //echo $row['full_name'];
                } else {
                    echo "<a href='show_profile.php?friend_id=$friendId'>" . $row['username'] . "</a>";

                    //echo $row['username'];
                }
                echo "</td><td>";
                // Ovde cemo linkove za pracenje korisnika
                // $friendId = $row["id"];
                if (!in_array($friendId, $niz1)) {
                    if (!in_array($friendId, $niz2)) {
                        $text = "Follow";
                    } else {
                        $text = "Follow back";
                    }
                    echo "<a href='followers.php?friend_id=$friendId' class='followbtn'>$text</a>";
                } else {
                    echo " <a href='followers.php?unfriend_id=$friendId' class='unfollowbtn'>Unfollow</a>";
                }
                echo "</td><td>";
                echo NumberFollowsFromOther($conn, $friendId); //broji sve pratioce followers
                echo "</td><td>";
                echo NumberFollowFromOther($conn, $friendId);  //broji sva pracenja svih korisnika following
                echo "</td></tr>";
            }
            echo "</table>";
        }
        // ovde smo dosli get metodom, nije radjena pretraga
    } else {
        $avatar = 'images/otherAvatar.jpg';

        $q = "SELECT `u`.`id`, `u`.`username`, 
        CONCAT(`p`.`first_name`, ' ',`p`.`last_name`) AS `full_name`, `img`,
        `p`.`gender` as `gender`,
        `p`.`id` as `profileId`
        FROM `users` AS `u`
        LEFT JOIN `profiles` AS `p`
        ON `u`.`id` = `p`.`id_user`
        WHERE `u`.`id` != $id
        ORDER BY `full_name`;
         ";
        $result = $conn->query($q);
        if ($result->num_rows == 0) {
        ?>
            <div class="error">No users in database</div>
    <?php

        } else {
            echo "<table>";
            // echo "<tr><th>Profile Images</th><th>Name</th><th colspan='2'>Action</th></tr>";
            echo "<tr><th>Profile Images</th><th>Name</th><th>Action</th><th>Followers</th><th>Following</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>";
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
                echo '</td><td>';
                $friendId = $row["id"];
                if ($row['full_name'] !== NULL) {
                    echo "<a href='show_profile.php?friend_id=$friendId'>" . $row['full_name'] . "</a>"; //prosledjuje id u linku
                    //echo $row['full_name'];
                } else {
                    echo "<a href='show_profile.php?friend_id=$friendId'>" . $row['username'] . "</a>";

                    //echo $row['username'];
                }
                echo "</td><td>";
                // Ovde cemo linkove za pracenje korisnika
                // $friendId = $row["id"];
                if (!in_array($friendId, $niz1)) {
                    if (!in_array($friendId, $niz2)) {
                        $text = "Follow";
                    } else {
                        $text = "Follow back";
                    }
                    echo "<a href='followers.php?friend_id=$friendId' class='followbtn'>$text</a>";
                } else {
                    echo " <a href='followers.php?unfriend_id=$friendId' class='unfollowbtn'>Unfollow</a>";
                }
                echo "</td><td>";
                echo NumberFollowsFromOther($conn, $friendId); //broji sve pratioce followers
                echo "</td><td>";
                echo NumberFollowFromOther($conn, $friendId);  //broji sva pracenja svih korisnika following
                echo "</td></tr>";
            }
            echo "</table>";
        }
    }
    ?>
    <a href=""></a>
</body>

</html>