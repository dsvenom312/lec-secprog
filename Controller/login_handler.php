<?php
session_start();
require "./connection.php";
require "./session_handler.php";

function doLogin($username, $password) {
    global $conn;

    // Prepared statement to retrieve hashed password for the given username
    $query = "SELECT * FROM users WHERE username=?;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login_result = doLogin($username, $password);

    if ($login_result->num_rows == 1) {
        $data = $login_result->fetch_assoc();
        $hashed_password = $data['password'];

        if (hash_equals($hashed_password, hash('sha256', $password))) {
            // Check if this user is already logged in from another IP
            if (isset($_SESSION['login_time']) && $_SESSION['username'] === $username && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
                $_SESSION["error_message"] = "Multiple logins not allowed. You are already logged in from another device.";
                echo '<script>alert("' . $_SESSION["error_message"] . '"); window.location.href = "../Pages/login.php";</script>';
                exit();
            }

            // Store login time and IP address in session
            $_SESSION['login_time'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];

            // Regenerate session ID after successful login
            session_regenerate_id(true);

            $_SESSION["success_message"] = "Welcome, $username";
            $_SESSION['username'] = $data["username"];
            $_SESSION["role"] = $data["role"];
            $_SESSION["id"] = $data["id"];
            $_SESSION['is_login'] = true;

            header("Location: ../Pages/home.php");
            exit();
        } else {
            $_SESSION["error_message"] = "Incorrect username or password.";
            echo '<script>alert("' . $_SESSION["error_message"] . '"); window.location.href = "../Pages/login.php";</script>';
            session_destroy();
            exit();
        }
    } else {
        $_SESSION["error_message"] = "Account invalid."; 
        echo '<script>alert("' . $_SESSION["error_message"] . '"); window.location.href = "../Pages/login.php";</script>';
        session_destroy();
        exit();
    }
}

$conn->close();
?>
