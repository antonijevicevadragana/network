<?php


// ne dozvoljavamo pristup ovoj stranici logovanim korisnicima
session_start();
if (isset($_SESSION['id'])) {
    header("Location: index.php");
}

//$id = $_SESSION['id'];

require_once "connection.php";
require_once "validaction.php";
require_once "header.php";



$usernameError = "";
$passwordError = "";
$retypeError = "";
$username = "";
$password = "";
$retype = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
   

    //forma je poslata treba pokupiti vrednosti iz polja

    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $retype = $conn->real_escape_string($_POST['retype']);

    // var_dump($username);
    // var_dump($password);
    // var_dump($retype);

    //1) izvrsiti validaciju za promenljivu $username
    $usernameError = usernameValidation($username, $conn);  //pozvana fja iz validation.php

    //2) izvrsiti validaciju za promenljivu $password

    $passwordError = passwordValidation($password);

    //3) izvrsiti validaciju za promenljivu $retype

    $retypeError = passwordValidation($retype);
    if ($password != $retype) {
        $retypeError = "You must enter two same passwords";
    }
    //4.1) ako su sva polja validna onda treba dodati novog korisnika (treba izvrsiti inset upit nad tabelom users)

    if ($usernameError == "" && $passwordError == "" && $retypeError == "") {

        // lozinka treba prvo da se sifruje
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $q = " INSERT INTO `users` (`username`, `password`)
       VALUES
       ('$username', '$hash');";



        if ($conn->query($q)) {

            //kreirali smo novog korisnika, vodi ga na stranicu za logovanje
            header("Location: index.php?p=ok");

     //4.2) ako postoji neko polje koje nije validno ne raditi upit 

        } else {
            header("Location: error.php?" . http_build_query(['m' => "Greska kod kreiranja usera"]));
        }
    }


}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register new user</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <link rel="stylesheet" href="style.css?ver=1">
</head>

<body>


    <div class="container">
        <div class="row justify-content-center align-items-md-center">
            <div class="col-md-6 align-items-md-center">
                <h1 class="headerForm animate__animate__animated__bounce animacija">Register to our site</h1>

                <form action="register.php " method="POST">

                    <p>
                        <label for="username">Username</label>
                    </p>
                    <p>
                        <input type="text" name="username" id="username" value="<?php echo $username; ?>">
                        <span class="error">* <?php echo $usernameError; ?></span>
                    </p>

                    <p>
                        <label for="password">Password</label>
                    </p>
                    <p>
                        <input type="password" name="password" id="password" value="">
                        <span class="error">* <?php echo $passwordError; ?></span>
                    </p>


                    <p>
                        <label for="retype">Retype password</label>
                    </p>
                    <p>
                        <input type="password" name="retype" id="retype" value="">
                        <span class="error">* <?php echo $retypeError; ?></span>

                    </p>

                    <p>



                    <p>

                        <input type="submit" value="Register me!">
                    </p>

                </form>

            </div>

            <div class="col-md-6 backimg"> </div>
        </div>
    </div>



    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script> -->

</body>

</html>