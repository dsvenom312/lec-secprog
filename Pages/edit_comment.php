<?php
session_start();
require "../Controller/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photoID = $_POST['photoID'];
    $editedComment = $_POST['editedComment'];

    // Perform the database update
    $stmt = $conn->prepare("UPDATE photos SET comment = ? WHERE id = ?");
    $stmt->bind_param("si", $editedComment, $photoID);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the home page
    header("Location: home.php");
    exit();
} else {
    // Invalid request, redirect to home page
    header("Location: home.php");
    exit();
}
?>
