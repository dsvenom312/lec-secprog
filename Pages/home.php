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

    <form action="../Controller/upload_handler.php" method="post" enctype="multipart/form-data">
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