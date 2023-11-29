<?php


session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

require_once "header.php";

$id = $_SESSION['id'];
$firstName = $lastName = $dob = $gender = $img = $bio = "";
$firstNameError = $lastNameError = $dobError = $genderError = $imgError = "";
require_once "connection.php";
require_once "validaction.php";

$sucMessage = "";
$errMessage = "";

$profileRow = profileExists($id, $conn);
//profile row je false ako profil ne postoji, a asocijativni niz ako profil postoji

if ($profileRow !== false) {
    $firstName = $profileRow['first_name'];
    $lastName = $profileRow['last_name'];
    $gender = $profileRow['gender'];
    $dob = $profileRow['dob'];
    $img = $profileRow['img'];  //image je red u SQL
    $bio = $profileRow['bio'];

}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']); //post name u formi
    $gender = $conn->real_escape_string($_POST['gender']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $bio = $conn->real_escape_string($_POST['bio']);
   

    //vrsimo validaciju polja

    $firstNameError = nameValidation($firstName);
    $lastNameError = nameValidation($lastName);
    $genderError = genderValidation($gender);
    $dobError = dobValidation($dob);


    //validacija slike

    if (isset($_FILES['img'])) {

        $img_name = $_FILES['img']['name'];
        $img_size = $_FILES['img']['size']; //velicina slike
        $tmp_name = $_FILES['img']['tmp_name']; //mesto gde je slika sacuvana trenutno
        $Error = $_FILES['img']['error']; //error treba da je 0 kad ima slika

        if ($Error === 0) {  //ako je ubacena slika 
            $allowed_exs = array("jpg", "jpeg", "png"); //dozvoljene extenzije
            $img_exs = pathinfo($img_name, PATHINFO_EXTENSION);

            if ($img_size > 125000 || !in_array($img_exs, $allowed_exs)) {
                $imgError = "Sorry your file is too large or invalid extension! Allowed extension: jpg, jpeg, png. Allowed image size 1mb"; //proveravamo velicinu slike i extezijeu
            } else { //slika je dozvoljene velicine i extezije 
                $img = $id . '.jpg';
                $img_uplad_path = 'images/' . $img;
                move_uploaded_file($tmp_name, $img_uplad_path);
                $imgError = "";
                // echo "$img";
            }
        }
    }

    // var_dump($firstName);
    // var_dump($lastName);
    // var_dump($gender);
    // var_dump($dob);
    // var_dump($id);





    //ako je sve u redu ubacijemo novi red u tabelu profiles

    if ($firstNameError == "" && $lastNameError == "" && $genderError == "" && $dobError == "" && $imgError == "") {
        $q = "";

        if ($profileRow === false) {

            $q = "INSERT INTO `profiles`(`first_name`, `last_name`, `gender`, `dob`, `id_user`, `img`,`bio` ) 
            VALUE
            ('$firstName', '$lastName', '$gender', '$dob', $id, '$img','$bio')";
        } else {

            $q = "UPDATE `profiles` SET 
            `first_name` = '$firstName',
            `last_name` = '$lastName',
            `gender` = '$gender',
            `dob` = '$dob',
            `img`='$img',
            `bio`='$bio'

            WHERE `id_user` = $id;
            ";
        }



        if ($conn->query($q)) {
            //uspesno kreiran ili editovan profil
            if ($profileRow !== false) {
                $sucMessage = "You have edited your profile";
            } else {
                $sucMessage = "You have created your profile";
            }
        } else {
            //desila se greska u upitu
            $errMessage = "Error creating profile" . $conn->error;
        }
    }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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


    <div class="container">
        <div class="row justify-content-center align-items-md-center">
            <div class="col-md-6 align-items-md-center">

                <h1 class="headerForm animate__animate__animated__bounce animacija">Please fill profile details</h1>



                <form autocomplete="off" action="#" method="POST" enctype="multipart/form-data">
                    <p>
                        <label for="first_name">First name:</label>
                    </p>
                    <p>
                        <input type="text" name="first_name" id="first_name" value="<?php echo $firstName; ?>">
                        <span class="error">* <?php echo $firstNameError; ?></span>
                    </p>

                    <p>
                        <label for="last_name">Last name:</label>
                    </p>
                    <p>
                        <input type="text" name="last_name" id="last_name" value="<?php echo $lastName; ?>">
                        <span class="error">*<?php echo $firstNameError; ?></span>
                    </p>

                    <p>
                        <label for="gender">Gender:</label>
                    </p>
                    <p>
                        <input type="radio" name="gender" id="m" value="m" <?php if ($gender == 'm') {
                                                                                echo 'checked';
                                                                            } ?>> Male
                        <input type="radio" name="gender" id="f" value="f" <?php if ($gender == 'f') {
                                                                                echo 'checked';
                                                                            } ?>> Female
                        <input type="radio" name="gender" id="o" value="o" <?php if ($gender == 'o' || $gender == "") {
                                                                                echo 'checked';
                                                                            } ?>> Other
                        <span class="error"><?php echo $genderError; ?></span>

                    </p>

                    <p>
                        <label for="dob">Date of birth:</label>
                    </p>
                    <p>
                        <input type="date" name="dob" id="dob" value="<?php echo $dob; ?>">
                        <span class="error"><?php echo $dobError ?></span>


                    </p>

                    <p>
                        <label for="file">Insert a picture</label>
                    </p>

                    <p>
                        <input type="file" name="img" id="img" value="">
                        <span class="error"><?php echo $imgError ?></span>


                    </p>
                 
                        <!-- biografija -->
                    <p>
                        <label for="bio">About me</label>
                    </p>
                    <textarea name="bio" id="bio" cols="30" rows="10" value="<?php echo $bio; ?>"></textarea>
                    <p>
                        <?php
                        $poruka;
                        if ($profileRow === false) {
                            $poruka = "Create profile";
                        } else {
                            $poruka = "Edit profile";
                        }
                        ?>
                        <!-- <input type="submit" value="<?php echo $poruka; ?>"> -->
                        <input type="submit" value="<?php echo ($profileRow === false) ? 'Create profile' : 'Edit profile' ?>">
                    </p>


                </form>

            </div>

            <div class="col-md-6 backimg"> </div>
        </div>
    </div>

</body>

</html>