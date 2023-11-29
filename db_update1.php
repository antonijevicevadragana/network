<?php

require_once "connection.php";

$q="ALTER TABLE `profiles` ADD `bio` TEXT;";



if ($conn->query($q)) {
    echo "<p>Successfully added colunm in table</p>" ;
}
else {
    header("location:error.php?m=" .$conn->error);
}




?>