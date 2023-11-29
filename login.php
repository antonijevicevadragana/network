<?php

session_start(); // metoda za pokretanje sesije kod logovanja, ide obavezno na pocetku php fajla
require_once "header.php";

if (isset($_SESSION['id'])) {
    header("Location: index.php");
}

require_once "connection.php";
//require_once "header.php";


$usernameError = "*";
$passwordError = "*";
$username = " ";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // korisnik je poslao username i password

    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    if (empty($username)) {
        $usernameError = "Username cannot be blank";
    }

    if (empty($password)) {
        $passwordError = "Password cannot be blank";
    }

    if ($usernameError == "*" && $passwordError == "*") {

        // pokusamo da logujemo korisnika 

        $q = "SELECT * FROM `users` WHERE `username` = '$username'";

        $result = $conn->query($q);
        if ($result->num_rows == 0) {
            $usernameError = "This username is invalid.";
        } else {
            //  Postoji korisnik, proveriti password
            $row = $result->fetch_assoc();
            $dbPassword = $row['password']; // hesirana vrednost iz baze
            if (!password_verify($password, $dbPassword)) {
                $passwordError = "Wrong password, try again!";
            } else {
                //  ispravni su i username i password
                $_SESSION['id'] = $row['id']; // moze id da se upamti
                $_SESSION['username'] = $row['username']; // ili username
                header("Location: index.php");
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="style.css?ver=1">

</head>

<body>


    <div class="container">
        <div class="row justify-content-center align-items-md-center">
            <div class="col-md-6 align-items-md-center">
                <h1 class="headerForm animate__animate__animated__bounce animacija">Please login</h1>

                <form action="#" method="POST">
                    <p>
                        <label for="username">Username:</label>
                    </p>
                    <p>
                        <input type="text" name="username" id="username" value="<?php echo ltrim($username);?>">
                        <span class="error"><?php echo $usernameError; ?></span>
                    </p>
                    <p>
                        <label for="password">Password:</label>
                    </p>
                    <p>
                        <input type="password" name="password" id="password" value="">
                        <span class="error"><?php echo $passwordError; ?></span>
                    </p>
                    <p>
                        <input type="submit" value="Login">
                    </p>
                </form>

            </div>

            <div class="col-md-6 backimg"> </div>
        </div>
    </div>


</body>

</html>