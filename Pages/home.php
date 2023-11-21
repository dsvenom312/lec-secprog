<?php
session_start();
//print_r($_SESSION['id']);

require "../Controller/connection.php";
require "../Controller/session_handler.php";

if ($_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit(); // Ensure script stops if not logged in
}

checkSessionExpiration();

// File upload configuration
$targetDir = __DIR__ . "/uploads/";
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
$targetFile = $targetDir . uniqid('', true) . '_' . basename($_FILES["file"]["name"]);

// Check if the file is an actual image
if (!getimagesize($_FILES["file"]["tmp_name"])) {
    echo "File is not an image.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["file"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
$allowedFormats = ["jpg", "jpeg", "png", "gif"];
if (!in_array($imageFileType, $allowedFormats)) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// File validation: Check MIME type
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
$fileMimeType = mime_content_type($_FILES["file"]["tmp_name"]);
if (!in_array($fileMimeType, $allowedMimeTypes)) {
    echo "Invalid file type.";
    $uploadOk = 0;
}

// File name sanitization
$targetFile = filter_var($targetFile, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

// Get owner_id from the user session
$ownerId = $_SESSION['id'];

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
} else {
    // If everything is ok, try to upload file
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, save data to database
        $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

        // Additional input validation for comment
        if (!preg_match('/^[a-zA-Z0-9\s.,!?]+$/', $comment)) {
            echo "Invalid comment format.";
            $uploadOk = 0;
        }

        // Use prepared statements to prevent SQL injection
        if ($uploadOk) {
            $stmt = $conn->prepare("INSERT INTO photos (filename, comment, owner_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $targetFile, $comment, $_SESSION['id']);
            $stmt->execute();
            $stmt->close();
            echo($_SESSION['id']);
            echo "File uploaded successfully.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Hello, world!</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">Select a photo:</label>
        <input type="file" name="file" id="file" required>
        <br>
        <label for="comment">Add a comment:</label>
        <textarea name="comment" id="comment" rows="4" required></textarea>
        <br>
        <input type="submit" name="submit" value="Upload">
    </form>

    <form action="../Controller/logout_handler.php" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

    <a href="./message.php"><button>Upload Message</button></a>

    <h2>Recent Photos and Comments</h2>
    <?php include './display.php'; ?>
</body>
</html>
