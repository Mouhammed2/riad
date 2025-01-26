
<?php
require 'db_conection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    
    $stmt = $conn->prepare("SELECT id, name, email, role, password_hash, picture, phone FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password_hash'])) {
            session_start();
            $_SESSION['login'] = true;
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['picture'] = $row['picture'];
     

            if ($row['role'] === "client") {
                header("Location: index.php ");
            } else {
                header("Location: admin_panel");
                exit();
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
    $conn->close();
}
?>
