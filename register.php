<?php
require 'db_conection.php';

if (!empty($_POST)) {
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
    $addrese = isset($_POST['addrese']) ? $_POST['addrese'] : null;
    $zip_code = isset($_POST['zip_code']) ? $_POST['zip_code'] : null;
    $dob = isset($_POST['dob']) ? $_POST['dob'] : null;
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $picture = "";
    if (isset($_FILES["picture"]) && $_FILES["picture"]["error"] == 0) {
        $target_dir = "images/";

        // Ensure folder exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $picture = $target_dir . basename($_FILES["picture"]["name"]);
        move_uploaded_file($_FILES["picture"]["tmp_name"], $picture);
    }

    $query = "INSERT INTO users (name, email, phone, picture, addrese, zip_code, dob, password_hash,role) 
              VALUES ('$name', '$email', '$phone', '$picture', '$addrese', '$zip_code', '$dob', '$password','client')";

    if (mysqli_query($conn, $query)) {
        header("Location:index.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
