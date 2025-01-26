<?php

session_start();


$_SESSION['flash_message'] = "You have been successfully logged out!";

session_unset();
session_destroy();

header("Location: index.php");
exit();