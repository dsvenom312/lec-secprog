<?php
session_start();
require "../Controller/connection.php";

// Check if the user is logged in
if (!isset($_SESSION['is_login']) || $_SESSION['is_login'] !== true) {
    header("Location: login.php");
    exit();
}

// Validate the photoID
if (!isset($_POST['photoID']) || !filter_var($_POST['photoID'], FILTER_VALIDATE_INT)) {
    $_SESSION["error_message"] = "Invalid photo ID.";
    header("Location: home.php");
    exit();
}

$photoID = $_POST['photoID'];
$userID = $_SESSION['id'];

// Check if the user owns the photo
$stmt = $conn->prepare("SELECT owner_id, filename FROM photos WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $photoID);
$stmt->execute();
$stmt->bind_result($ownerID, $filename);
$stmt->fetch();
$stmt->close();

if ($ownerID !== $userID) {
    $_SESSION["error_message"] = "You do not have permission to delete this photo.";
    header("Location: home.php");
    exit();
}

// Delete the photo from the database
$stmt = $conn->prepare("DELETE FROM photos WHERE id = ?");
$stmt->bind_param("i", $photoID);
$stmt->execute();
$stmt->close();

// Delete the photo file from the server
$filePath = __DIR__ . "/uploads/" . $filename;
if (file_exists($filePath)) {
    unlink($filePath);
}

$_SESSION["success_message"] = "Photo deleted successfully.";
header("Location: home.php");
exit();
?>
