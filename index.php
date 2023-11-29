<?php
require_once "connection.php";
require_once "validaction.php";


session_start();
require_once "header.php";

$poruka = "";
if (isset($_GET['p']) && $_GET['p'] == "ok") {
    $poruka = "You have successfully registered, please login to continue";
}
$username = "anonymus";
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $id=$_SESSION['id']; //id log korisnika  
$row=profileExists($id, $conn);

   $m="";
   if ($row===false) {
   //logovani korisnik nema profil
   $m="Create";
   }
   else {
    //log korisnik ima profil
    $m="Edit";
    $username = $row['first_name'] . " " . $row['last_name'];
   }

}

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


    <div class="container-fluid back">

        <?php if (strlen($poruka) > 0) { ?>
            <div class="text-center">
                <br>
                <div class="alert alert-success alert-dismissible  success" style="width: 50%;" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <?php echo $poruka; ?>
                </div>
            </div>
        <?php  } ?>

        <div class="row justify-content-center centar">
            <div class="welcome"><br>
                <h1 class="animate__animate__animated__bounceOut">Welcome, <?php echo $username ?>, to our Social network</h1>
            </div>
        </div>


        <div class="custom-shape-divider-top-1686762017">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="shape-fill"></path>
            </svg>
        </div> 
        
  

        

        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>