    <?php
    require_once "connection.php";
    require_once "validaction.php";
    session_start();
    require_once "header.php";
    if (!isset($_SESSION["id"])) {
        header("Location: index.php");
    }
    $sucMessage = $errMessage = "";
    $passwordNewError = $passwordOldError = $retypeError = "";

    $id = $_SESSION["id"];
    $qPass = "SELECT `password` FROM `users` WHERE `id` = $id;";

    $result = $conn->query($qPass);

    $row = $result->fetch_assoc();
    $password = $row['password'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $passwordNew = $conn->real_escape_string($_POST['passwordNew']);
        $retype = $conn->real_escape_string($_POST['retype']);
        $passwordOld = $conn->real_escape_string($_POST['passwordOld']);

        $passwordNewError = passwordValidation($passwordNew);
        $passwordOldError = passwordValidation($passwordOld);
        $retypeError = passwordValidation($retype);

        if ($passwordNewError == "" && $passwordOldError == "" && $retypeError == "") {
            $q = "";
            if (password_verify($passwordOld, $password)) {
                if ($passwordNew === $retype) {
                    $passwordNew = password_hash($passwordNew, PASSWORD_DEFAULT);
                    $q = "UPDATE `users`
            SET `password` = '$passwordNew' 
            WHERE `id` = $id;";

                    if ($conn->query($q)) {
                        $sucMessage = "You have changed your profile";
                    } else {
                        // desila se greska u u pitu
                        $errMessage = "Error chaning password: " . $conn->error;
                    }
                } else {
                    $retypeError = "You must enter two same passwords";
                }
            } else {
                $passwordOldError = "Invalid password";
            }
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

        <link rel="stylesheet" href="style.css?ver=1">
    </head>

    <body>
        <div class="success">
            <?php echo $sucMessage ?>
        </div>
        <div class="error">
            <?php echo $errMessage; ?>
        </div>
        <form action="reset_password.php" method="POST">
            <p>
                <label for="passwordOld">Your Old passwrod:</label>
            </p>
            <p>
                <input type="password" name="passwordOld" id="passwordOld">
                <span class="error">*<?php echo $passwordOldError ?></span>
            </p>
            <p>
                <label for="passwordNew">Your New passwrod:</label>
            </p>
            <p>
                <input type="password" name="passwordNew" id="passwordNew">
                <span class="error">*<?php echo $passwordNewError ?></span>
            </p>
            <p>
                <label for="retype">Retype password:</label>
            </p>
            <p>
                <input type="password" name="retype" id="retype">
                <span class="error">*<?php echo $retypeError ?></span>
            </p>
            <p>
                <input type="submit" value="Change password">
            </p>
        </form>
    </body>

    </html>