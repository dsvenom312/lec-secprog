<?php
session_start();
require "./connection.php";
require "./session_handler.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $targetDir = __DIR__ . "/../Pages/uploads/";
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

    $timestamp = time();
    $username = $_SESSION['username'];
    $randomNumber1 = mt_rand(1000000000, 9999999999);
    $randomNumber2 = mt_rand(1000000000, 9999999999);
    $customFileName = preg_replace('/[^a-zA-Z0-9_.]/', '', $timestamp . '_' . $randomNumber1 . '_' . $randomNumber2 . '.' . $imageFileType);
    $targetFile = $targetDir . $customFileName;


    // Check if the file is an actual image
    if (!getimagesize($_FILES["file"]["tmp_name"])) {
        $_SESSION["error_message"] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["file"]["size"] > 5 * 1000 * 1000) {
        $_SESSION["error_message"] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    if ($_FILES["file"]["size"] === 0) {
        $_SESSION["error_message"] = "Sorry, file error.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedFormats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowedFormats)) {
        $_SESSION["error_message"] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // File validation: Check MIME type
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileMimeType = mime_content_type($_FILES["file"]["tmp_name"]);
    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        $_SESSION["error_message"] = "Invalid file type.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $_SESSION["error_message"] = "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            // File uploaded successfully, save data to database
            $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

            // Additional input validation for comment
            if (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $comment)) {
                $_SESSION["error_message"] = "Invalid comment format.";
            } else {
                // Use prepared statements to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO photos (filename, comment, owner_id) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $customFileName, $comment, $_SESSION['id']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION["success_message"] = "File uploaded successfully.";
                } else {
                    $_SESSION["error_message"] = "Error uploading file. Please try again.";
                }
                $stmt->close();
            }
        } else {
            $_SESSION["error_message"] = "Sorry, there was an error uploading your file.";
        }
    }
    header("Location: ../Pages/home.php");
    exit();
}

$conn->close();
?>
