<?php
session_start();
require "../Controller/connection.php";
require "../Controller/session_handler.php";

if ($_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_FILES['photo'], $_POST['comment'])) {
    $comment = $_POST['comment'];
    $userId = $_SESSION['id'];

    $targetDir = "../uploads/"; // Specify the directory where photos will be stored
    $targetFile = $targetDir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION["error_message"] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        $_SESSION["error_message"] = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 5000000) {
        $_SESSION["error_message"] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $_SESSION["error_message"] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        header("Location: upload.php");
        exit();
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            // File uploaded successfully, insert data into the database
            $filename = basename($_FILES["photo"]["name"]);
            $stmt = $conn->prepare("INSERT INTO photos (filename, comment, owner_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $filename, $comment, $userId);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $_SESSION["success_message"] = "The file " . htmlspecialchars(basename($_FILES["photo"]["name"])) . " has been uploaded.";
            } else {
                $_SESSION["error_message"] = "Error uploading file. Please try again.";
            }
            $stmt->close();
        } else {
            $_SESSION["error_message"] = "Sorry, there was an error uploading your file.";
        }
        header("Location: upload.php");
        exit();
    }
}

$conn->close();
?>
