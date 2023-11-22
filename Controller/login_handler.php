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

function deleteOldLoginAttempts() {
    global $conn;

    // Delete login attempts older than 10 minutes
    $deleteQuery = "DELETE FROM login_attempts WHERE last_attempt_time < NOW() - INTERVAL 900 SECOND;";
    $conn->query($deleteQuery);
}

function checkFailedLoginAttempts($username) {
    global $conn;

    // Check the number of failed login attempts and the time of the last attempt
    $query = "SELECT attempts, last_attempt_time FROM login_attempts WHERE username=?;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $attempts = $data['attempts'];
        $lastAttemptTime = strtotime($data['last_attempt_time']);

        // You can customize the rate limit duration (e.g., 1 hour)
        $rateLimitDuration = 900; // 10 minutes in seconds

        // Check if the user is rate-limited
        if ($attempts >= 5 && (time() - $lastAttemptTime) < $rateLimitDuration) {
            $_SESSION["error_message"] = "Too many login attempts. Please try again in 5 minutes.";
            echo '<script>alert("' . $_SESSION["error_message"] . '"); window.location.href = "../Pages/login.php";</script>';
            exit();
        }
    }
}

function insertLoginAttempt($username) {
    global $conn;

    // Insert a new login attempt into the login_attempts table
    $insertQuery = "INSERT INTO login_attempts (username, attempts, last_attempt_time) VALUES (?, 1, NOW()) ON DUPLICATE KEY UPDATE attempts = attempts + 1, last_attempt_time = NOW();";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("s", $username);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Delete old login attempts before processing login
    deleteOldLoginAttempts();

    // Check failed login attempts before processing login
    checkFailedLoginAttempts($username);

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

            // Reset login attempts for the user upon successful login
            $resetAttemptsQuery = "UPDATE login_attempts SET attempts = 0, last_attempt_time = NOW() WHERE username = ?;";
            $stmt = $conn->prepare($resetAttemptsQuery);
            $stmt->bind_param("s", $username);
            $stmt->execute();

            header("Location: ../Pages/home.php");
            exit();
        } else {
            // Increment login attempts for the user upon failed login
            insertLoginAttempt($username);

            $_SESSION["error_message"] = "Incorrect username or password.";
            echo '<script>alert("' . $_SESSION["error_message"] . '"); window.location.href = "../Pages/login.php";</script>';
            session_destroy();
            exit();
        }
    } else {
        // Increment login attempts for the user upon failed login
        insertLoginAttempt($username);

        $_SESSION["error_message"] = "Incorrect username or password.";
        echo '<script>alert("' . $_SESSION["error_message"] . '"); window.location.href = "../Pages/login.php";</script>';
        session_destroy();
        exit();
    }
}

$conn->close();
?>
