<?php
    session_start();
    // print_r($_SESSION);

    require "../Controller/connection.php";
    require "../Controller/session_handler.php";

    if ($_SESSION['is_login'] !== true) {
        header("Location: login.php");
    }
    checkSessionExpiration();

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>hello world</h1>
    <form action="../Controller/logout_handler.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

    <a href="./message.php"><button>Upload Message</button></a>
</body>
</html>