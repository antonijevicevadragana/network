<?php

session_start();
require_once "header.php";

session_unset(); // isto kao i //$_SESSION=array(); 
session_destroy();

header("Location: index.php");


?>