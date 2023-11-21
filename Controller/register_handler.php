<!-- <?php
session_start();
require "./connection.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['username'], $_POST['password'], $_POST['phone_number'], $_POST['email'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format";
        header("Location: ../Pages/register.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password should be at least 8 characters long";
        header("Location: ../Pages/register.php");
        exit();
    }

    $hashed_password = hash('sha256', $password);
    $random_number = mt_rand(1000, 9999);

    $user_id = 'us' . $random_number;
    $role = 'guest';

    $stmt = $conn->prepare("INSERT INTO users (id, username, password, phone_num, email, role) VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssss", $user_id, $username, $hashed_password, $phone_number, $email, $role);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Registration successful. You can now login.";
        header("Location: ../Pages/login.php");
    } else {
        $_SESSION['error_message'] = "Error registering user";
        header("Location: ../Pages/register.php");
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "All fields are required";
    header("Location: ../Pages/register.php");
}

$conn->close();
?> -->

<?php
session_start();
require "./connection.php";

function isUsernameExists($username) {
    global $conn;

    $query = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

function isEmailExists($email) {
    global $conn;

    $query = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format";
        echo '<script>alert("Invalid email format"); window.location.href = "../Pages/register.php";</script>';
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password should be at least 8 characters long";
        echo '<script>alert("Password should be at least 8 characters long"); window.location.href = "../Pages/register.php";</script>';
        exit();
    }

    if (isUsernameExists($username)) {
        $_SESSION['error_message'] = "Username already exists. Please choose a different one.";
        echo '<script>alert("Username already exists. Please choose a different one."); window.location.href = "../Pages/register.php";</script>';
        exit();
    }

    if (isEmailExists($email)) {
        $_SESSION['error_message'] = "Email already exists. Please use a different one.";
        echo '<script>alert("Email already exists. Please use a different one."); window.location.href = "../Pages/register.php";</script>';
        exit();
    }

    $hashed_password = hash('sha256', $password);
    $random_number = mt_rand(1000, 9999);

    $user_id = 'us' . $random_number;
    $role = 'guest';

    $stmt = $conn->prepare("INSERT INTO users (id, username, password, phone_num, email, role) VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssss", $user_id, $username, $hashed_password, $phone_number, $email, $role);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Registration successful. You can now login.";
        header("Location: ../Pages/login.php");
    } else {
        $_SESSION['error_message'] = "Error registering user";
        echo '<script>alert("Error registering user"); window.location.href = "../Pages/register.php";</script>';
        exit();
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "All fields are required";
    echo '<script>alert("All fields are required"); window.location.href = "../Pages/register.php";</script>';
    exit();
}

$conn->close();
?>