<<<<<<< Updated upstream
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
//$ownerId = $_SESSION['id'];

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
=======
<?php
session_start();
require "../Controller/connection.php";
require "../Controller/session_handler.php";

// Redirect if not logged in
if ($_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit();
}

// Check session expiration
checkSessionExpiration();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $targetDir = __DIR__ . "/uploads/";
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

    // Generate custom filename: timestamp_username.extension
    $timestamp = time();
    $username = $_SESSION['username'];
    $customFileName = preg_replace('/[^a-zA-Z0-9_.]/', '', $timestamp . '_' . $username . '.' . $imageFileType);
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
    header("Location: home.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../assets/home.css">
    
    <!-- Include the script for toggleEditForm -->
    <script>
        function toggleEditForm(photoID) {
            var commentElement = document.getElementById('comment_' + photoID);
            var formElement = document.getElementById('editForm_' + photoID);

            // Toggle form visibility
            formElement.style.display = (formElement.style.display === 'none' || formElement.style.display === '') ? 'block' : 'none';

            // If the form is visible, hide the comment text
            commentElement.style.display = (formElement.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</head>
<body>

    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<div style="background-color: #ffcccc; color: #ff0000; padding: 10px; text-align: center;">';
            echo $_SESSION['error_message'];
        echo '</div>';
        unset($_SESSION['error_message']);
    }
    ?>
    
    <div class="image-grid">
        <?php include './display.php'; ?>
    </div>

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

    <!-- <a href="./message.php"><button>Upload Message</button></a> -->
</body>
</html>


>>>>>>> Stashed changes
