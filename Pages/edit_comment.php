<?php
session_start();
require "../Controller/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input validation
    if (isset($_POST['photoID'], $_POST['editedComment']) && !empty($_POST['photoID'])) {
        $photoID = $_POST['photoID'];
        $editedComment = $_POST['editedComment'];

        // Perform the database update
        $stmt = $conn->prepare("UPDATE photos SET comment = ? WHERE id = ?");
        $stmt->bind_param("si", $editedComment, $photoID);
        $result = $stmt->execute();

        if ($result) {
            // Update successful, redirect back to the home page
            header("Location: home.php");
            exit();
        } else {
            // Handle database update error
            $_SESSION['error_message'] = "Error updating comment. Please try again.";
        }

        $stmt->close();
    } else {
        // Invalid request, redirect to home page
        $_SESSION['error_message'] = "Invalid request. Please try again.";
        header("Location: home.php");
        exit();
    }
} else {
    // Invalid request, redirect to home page
    header("Location: home.php");
    exit();
}
?>
