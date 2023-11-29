<?php
session_start();

require_once "connection.php";
require_once "header.php";
require_once "validaction.php";

if (!isset($_SESSION["id"])) {
    header("Location: index.php");
}
$id = $_SESSION['id'];
$sucMessage = "";
$errMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = $conn->real_escape_string($_POST['post']);

    $q = "INSERT INTO `posts`( `id_user`, `contetnt` ) 
    VALUE
    ('$id','$post')";


    if ($conn->query($q)) {

        $sucMessage = "post add sucessfully";
    } else {
        // desila se greska u u pitu
        $errMessage = "Error chaning password: " . $conn->error;
    }
}




$q2 = $q = "SELECT `u`.`id`, `u`.`username`, 
CONCAT(`p`.`first_name`, ' ',`p`.`last_name`) AS `full_name`, `img`,
`p`.`gender` as `gender`,
`p`.`id` as `profileId`,
`posts`.`contetnt` as  `post`,
`created_time` as `time`

FROM `users` AS `u`
LEFT JOIN `profiles` AS `p`
ON `u`.`id` = `p`.`id_user`
LEFT JOIN `posts` ON `u`.`id` =`posts`.`id_user`
ORDER BY `created_time`DESC;
 ";


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social netowork</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <link rel="stylesheet" href="style.css?ver=1">
</head>

<body>

    <?php if (strlen($sucMessage) > 0) { ?>
        <div class="text-center">
            <br>
            <div class="alert alert-success alert-dismissible  success" style="width: 50%;" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $sucMessage; ?>
            </div>
        </div>
    <?php  } ?>

    <?php if (strlen($errMessage) > 0) { ?>
        <div class="text-center">
            <br>
            <div class="alert alert-success alert-dismissible  success" style="width: 50%;" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <?php echo $errMessage; ?>
            </div>
        </div>
    <?php  } ?>


    <form action="" method="POST">
        <p>
            <label for="bio">Created new post</label>
        </p>
        <textarea name="post" id="post" cols="80" rows="3" value="" placeholder="post content"></textarea>
        <p>
        <p>
            <input type="submit" value="post">
        </p>

    </form>

    <?php
    
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
                if ($row['profileId'] == NULL){
                    $avatar = 'images/otherAvatar.jpg';
                    printImg($avatar);
                }
                    
                }
                if ($row['full_name'] !== NULL) {
                    echo  " <i>". $row['full_name'] ."</i>";
                } else {
                    echo  " <i>".$row['username']."</i>";
                }
                
                echo "<p>". $row['post']. "<p>";
                echo "<p><i> DATE: </i><strong>". $row['time']. "</strong><p>";
            }
            echo "</div>";
        }

        
    }

    ?>
</body>

</html>