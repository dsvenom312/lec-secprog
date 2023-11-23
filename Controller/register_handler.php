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

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format";
        header("Location: ../Pages/register.php");
        exit();
    }

    // Validate phone number (only numbers allowed)
    if (!preg_match('/^\d+$/', $phone_number)) {
        $_SESSION['error_message'] = "Invalid phone number format. Only numbers are allowed.";
        header("Location: ../Pages/register.php");
        exit();
    }

    if (strlen($password) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s])\S{8,}$/', $password)) {
        $_SESSION['error_message'] = "Password should be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one symbol.";
        header("Location: ../Pages/register.php");
        exit();
    }

    if (isUsernameExists($username)) {
        $_SESSION['error_message'] = "Username already exists. Please choose a different one.";
        header("Location: ../Pages/register.php");
        exit();
    }

    if (isEmailExists($email)) {
        $_SESSION['error_message'] = "Email already exists. Please use a different one.";
        header("Location: ../Pages/register.php");
        exit();
    }

    // Register the user if username and email do not exist
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
?>
