<?php
$poruka='';

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['m'])) {
   $poruka=$_GET['m'];
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social network</title>
    <!-- boost -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Ooops! An eror occured!</h1>
<div class="error">

<?php  echo $poruka; ?>
</div>

Return to  <a href="index.php">Home page</a>
    
</body>
</html>